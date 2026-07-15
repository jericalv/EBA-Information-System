<?php

namespace App\Services;

use App\Models\ConcessionairePayment;
use App\Models\PartnershipApplication;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Single source of truth for concessionaire monthly-fee tracking.
 *
 * A month is "covered" when a payment exists whose period_month falls in it
 * (or, for legacy rows without a period, whose payment_date falls in it).
 * Only months inside the contract window are ever owed, and the current
 * month is merely "due" until it ends — a concessionaire is overdue only
 * when a past in-contract month was never paid.
 */
class ConcessionaireFeeService
{
    /** Unpaid current month is flagged "due soon" from this day of the month. */
    public const DUE_SOON_DAY = 25;

    /** How far back arrears are counted when a contract predates tracking. */
    public const ARREARS_LOOKBACK_MONTHS = 24;

    public const STATUS_PAID = 'paid';
    public const STATUS_DUE = 'due';
    public const STATUS_DUE_SOON = 'due_soon';
    public const STATUS_OVERDUE = 'overdue';
    public const STATUS_NOT_STARTED = 'not_started';
    public const STATUS_ENDED = 'ended';
    public const STATUS_NO_FEE = 'no_fee';

    /**
     * Build fee plans for a collection of concessionaire users, keyed by id.
     * Also sets the latestPartnershipApplication relation on each user so
     * views can keep reading it directly.
     *
     * @param  Collection<int, User>  $concessionaires
     * @return array<int, array<string, mixed>>
     */
    public function plans(Collection $concessionaires, ?CarbonInterface $now = null): array
    {
        if ($concessionaires->isEmpty()) {
            return [];
        }

        $now = $now ? Carbon::instance($now) : now();
        $ids = $concessionaires->pluck('id');
        $latestApplications = $this->latestApplications($ids);
        $paidMonths = $this->paidMonthKeys($ids);

        $plans = [];

        foreach ($concessionaires as $concessionaire) {
            $latestApplication = $latestApplications->get($concessionaire->id);
            $concessionaire->setRelation('latestPartnershipApplication', $latestApplication);

            $plans[$concessionaire->id] = $this->planFor(
                $concessionaire,
                $latestApplication,
                $paidMonths->get($concessionaire->id) ?? collect(),
                $now
            );
        }

        return $plans;
    }

    /**
     * Fee plan for a single concessionaire (loads its own related data).
     *
     * @return array<string, mixed>
     */
    public function planForUser(User $concessionaire, ?CarbonInterface $now = null): array
    {
        $plans = $this->plans(collect([$concessionaire]), $now);

        return $plans[$concessionaire->id];
    }

    /**
     * @param  Collection<string, int>  $paidMonthKeys  'Y-m' keys of covered months (flipped map)
     * @return array<string, mixed>
     */
    public function planFor(
        User $concessionaire,
        ?PartnershipApplication $latestApplication,
        Collection $paidMonthKeys,
        CarbonInterface $now
    ): array {
        $now = Carbon::instance($now);
        $currentMonth = $now->copy()->startOfMonth();
        $currentMonthKey = $currentMonth->format('Y-m');

        $monthlyFee = (float) ($concessionaire->monthly_fee ?? 0);
        $contractStart = $latestApplication?->contract_period_start;
        $contractEnd = $latestApplication?->contract_period_end;
        $startMonth = $contractStart?->copy()->startOfMonth();
        $endMonth = $contractEnd?->copy()->startOfMonth();

        // Arrears: unpaid in-contract months strictly before the current month.
        $arrears = [];
        if ($monthlyFee > 0 && $startMonth) {
            $cursor = $startMonth->copy();
            $floor = $currentMonth->copy()->subMonths(self::ARREARS_LOOKBACK_MONTHS);
            if ($cursor->lt($floor)) {
                $cursor = $floor;
            }

            while ($cursor->lt($currentMonth)) {
                $withinContract = ! $endMonth || $cursor->lte($endMonth);
                $key = $cursor->format('Y-m');
                if ($withinContract && ! $paidMonthKeys->has($key)) {
                    $arrears[] = $key;
                }
                $cursor = $cursor->addMonthNoOverflow();
            }
        }

        // Is the current month even owed under the contract?
        $contractStarted = ! $startMonth || $startMonth->lte($currentMonth);
        $contractRunning = ! $endMonth || $currentMonth->lte($endMonth);
        $currentOwed = $monthlyFee > 0 && $contractStarted && $contractRunning;
        $currentPaid = $paidMonthKeys->has($currentMonthKey);
        $currentUnpaid = $currentOwed && ! $currentPaid;

        $status = match (true) {
            $monthlyFee <= 0 => self::STATUS_NO_FEE,
            ! $contractStarted => self::STATUS_NOT_STARTED,
            $arrears !== [] => self::STATUS_OVERDUE,
            ! $contractRunning => self::STATUS_ENDED,
            ! $currentUnpaid => self::STATUS_PAID,
            $now->day >= self::DUE_SOON_DAY => self::STATUS_DUE_SOON,
            default => self::STATUS_DUE,
        };

        $preselect = $arrears;
        if ($currentUnpaid) {
            $preselect[] = $currentMonthKey;
        }

        // Selectable when arrears exist even if the current month is paid,
        // so the record button keys off the fee, not just the current month.
        $hasSelectable = $monthlyFee > 0 && ($startMonth !== null || ! $currentPaid);

        return [
            'status' => $status,
            'status_label' => $this->label($status, $startMonth),
            'badge' => $this->badge($status),
            'monthly_fee' => $monthlyFee,
            'business' => $concessionaire->business_name ?: $concessionaire->name,
            'name' => $concessionaire->name,
            'owed_count' => count($arrears),
            'owed_months' => $arrears,
            'total_unpaid' => count($preselect),
            'current_paid' => $currentPaid,
            'current_unpaid' => $currentUnpaid,
            'has_selectable' => $hasSelectable,
            'current_month' => $currentMonthKey,
            'contract_start' => $startMonth?->format('Y-m'),
            'contract_end' => $endMonth?->format('Y-m'),
            'paid_months' => $paidMonthKeys->keys()->values()->all(),
            'preselect' => $preselect,
        ];
    }

    /**
     * Count plans per status bucket (missing statuses come back as 0).
     *
     * @param  array<int, array<string, mixed>>  $plans
     * @return array<string, int>
     */
    public function statusCounts(array $plans): array
    {
        $counts = array_fill_keys([
            self::STATUS_PAID,
            self::STATUS_DUE,
            self::STATUS_DUE_SOON,
            self::STATUS_OVERDUE,
            self::STATUS_NOT_STARTED,
            self::STATUS_ENDED,
            self::STATUS_NO_FEE,
        ], 0);

        foreach ($plans as $plan) {
            $counts[$plan['status']]++;
        }

        return $counts;
    }

    /**
     * Latest partnership application per user, keyed by user_id.
     *
     * @param  Collection<int, int>  $userIds
     * @return Collection<int, PartnershipApplication>
     */
    public function latestApplications(Collection $userIds): Collection
    {
        return PartnershipApplication::query()
            ->select([
                'partnership_applications.id',
                'partnership_applications.user_id',
                'partnership_applications.contract_period_start',
                'partnership_applications.contract_period_end',
                'partnership_applications.status',
                'partnership_applications.business_name',
            ])
            ->joinSub(
                PartnershipApplication::query()
                    ->selectRaw('MAX(partnership_applications.id) as latest_id, partnership_applications.user_id')
                    ->whereIn('partnership_applications.user_id', $userIds)
                    ->groupBy('partnership_applications.user_id'),
                'latest_pa',
                function ($join) {
                    $join->on('latest_pa.latest_id', '=', 'partnership_applications.id')
                        ->on('latest_pa.user_id', '=', 'partnership_applications.user_id');
                }
            )
            ->get()
            ->keyBy('user_id');
    }

    /**
     * Covered 'Y-m' month keys per concessionaire (as flipped maps so
     * lookups are $keys->has('2026-07')), keyed by concessionaire_id.
     *
     * @param  Collection<int, int>  $userIds
     * @return Collection<int, Collection<string, int>>
     */
    public function paidMonthKeys(Collection $userIds): Collection
    {
        return ConcessionairePayment::query()
            ->whereIn('concessionaire_id', $userIds)
            ->get(['concessionaire_id', 'period_month', 'payment_date'])
            ->groupBy('concessionaire_id')
            ->map(function ($payments) {
                return $payments
                    ->map(function ($payment) {
                        $covered = $payment->period_month ?? $payment->payment_date;

                        return $covered ? Carbon::parse($covered)->format('Y-m') : null;
                    })
                    ->filter()
                    ->flip();
            });
    }

    protected function label(string $status, ?CarbonInterface $startMonth): string
    {
        return match ($status) {
            self::STATUS_PAID => 'Paid',
            self::STATUS_DUE => 'Due This Month',
            self::STATUS_DUE_SOON => 'Due Soon',
            self::STATUS_OVERDUE => 'Overdue',
            self::STATUS_NOT_STARTED => $startMonth ? 'Starts ' . $startMonth->format('M Y') : 'Not Started',
            self::STATUS_ENDED => 'Contract Ended',
            default => 'No Fee Set',
        };
    }

    /** Which badge style a status renders with: paid | due | overdue | none. */
    protected function badge(string $status): string
    {
        return match ($status) {
            self::STATUS_PAID => 'paid',
            self::STATUS_DUE, self::STATUS_DUE_SOON => 'due',
            self::STATUS_OVERDUE => 'overdue',
            default => 'none',
        };
    }
}

<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Mail\PaymentsRecordedMail;
use App\Models\ActivityLog;
use App\Models\ConcessionairePayment;
use App\Models\PartnershipApplication;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class CashierController extends Controller
{
    public function dashboard()
    {
        $currentMonthStart = now()->startOfMonth();
        $currentMonthEnd = now()->endOfMonth();

        $activeConcessionairesCount = User::query()
            ->where('role', 'concessionaire')
            ->where('is_approved', true)
            ->where('is_active_concessionaire', true)
            ->count();

        $paidThisMonth = ConcessionairePayment::whereYear('period_month', now()->year)
            ->whereMonth('period_month', now()->month)
            ->pluck('concessionaire_id')
            ->toArray();

        $concessionaires = User::query()
            ->where('role', 'concessionaire')
            ->where('is_approved', true)
            ->where('is_active_concessionaire', true)
            ->get();

        $today = now()->day;
        $overdueCount = 0;
        $readyToRecordCount = 0;

        if ($concessionaires->isNotEmpty()) {
            $concessionaires->each(function (User $concessionaire) use (
                $paidThisMonth,
                $today,
                &$overdueCount,
                &$readyToRecordCount
            ) {
                $hasPaymentThisMonth = in_array($concessionaire->id, $paidThisMonth, true);
                $monthlyFee = (float) ($concessionaire->monthly_fee ?? 0);

                if ($hasPaymentThisMonth) {
                    $status = 'paid';
                } elseif ($monthlyFee <= 0) {
                    $status = 'no_contract';
                } elseif ($today >= 25) {
                    $status = 'due_soon';
                } else {
                    $status = 'overdue';
                }

                if ($status === 'overdue') {
                    $overdueCount++;
                }

                if (in_array($status, ['due_soon', 'overdue'], true)) {
                    $readyToRecordCount++;
                }
            });
        }

        $cashierMonthlyPayments = collect(range(5, 0))->map(function ($i) {
            $month = now()->subMonths($i);

            return [
                'month' => $month->format('M Y'),
                'total' => ConcessionairePayment::whereYear('payment_date', $month->year)
                    ->whereMonth('payment_date', $month->month)
                    ->sum('amount'),
            ];
        })->values()->toArray();

        $cashierPaymentTypes = [
            'cash' => ConcessionairePayment::where('payment_type', 'cash')->count(),
            'check' => ConcessionairePayment::where('payment_type', 'check')->count(),
            'bank_transfer' => ConcessionairePayment::where('payment_type', 'bank_transfer')->count(),
        ];

        // ---- Current-month collection totals for the stat cards ----
        $collectedThisMonth = (float) (collect($cashierMonthlyPayments)->last()['total'] ?? 0);

        $paymentsThisMonthCount = ConcessionairePayment::whereYear('payment_date', now()->year)
            ->whereMonth('payment_date', now()->month)
            ->count();

        return view('cashier.dashboard', compact(
            'activeConcessionairesCount',
            'overdueCount',
            'readyToRecordCount',
            'collectedThisMonth',
            'paymentsThisMonthCount',
            'cashierMonthlyPayments',
            'cashierPaymentTypes'
        ));
    }

    public function paymentsIndex(Request $request)
    {
        $currentMonthStart = now()->startOfMonth();
        $currentMonthEnd = now()->endOfMonth();

        $concessionaires = User::query()
            ->where('role', 'concessionaire')
            ->where('is_approved', true)
            ->where('is_active_concessionaire', true)
            ->withCount([
                'concessionairePayments as current_month_payment_count' => function ($query) use ($currentMonthStart, $currentMonthEnd) {
                    $query->whereBetween('payment_date', [$currentMonthStart, $currentMonthEnd]);
                },
            ])
            ->withSum('concessionairePayments as total_paid', 'amount')
            ->withMax('concessionairePayments as last_payment_date', 'payment_date')
            ->orderBy('business_name')
            ->orderBy('name')
            ->get();

        $now = now();
        $today = $now->day;
        $currentMonthKey = $now->format('Y-m');

        $concessionaireStatuses = [];
        $paymentPlans = [];

        if ($concessionaires->isNotEmpty()) {
            $latestApplications = PartnershipApplication::query()
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
                        ->whereIn('partnership_applications.user_id', $concessionaires->pluck('id'))
                        ->groupBy('partnership_applications.user_id'),
                    'latest_pa',
                    function ($join) {
                        $join->on('latest_pa.latest_id', '=', 'partnership_applications.id')
                            ->on('latest_pa.user_id', '=', 'partnership_applications.user_id');
                    }
                )
                ->get()
                ->keyBy('user_id');

            // Every covered month per concessionaire, so we can flag arrears/advances.
            $paidMonthsByConcessionaire = ConcessionairePayment::query()
                ->whereIn('concessionaire_id', $concessionaires->pluck('id'))
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

            foreach ($concessionaires as $concessionaire) {
                $latestApplication = $latestApplications->get($concessionaire->id);
                $concessionaire->setRelation('latestPartnershipApplication', $latestApplication);

                $monthlyFee = (float) ($concessionaire->monthly_fee ?? 0);
                $paidKeys = $paidMonthsByConcessionaire->get($concessionaire->id) ?? collect();
                $hasPaymentThisMonth = $paidKeys->has($currentMonthKey);

                if ($hasPaymentThisMonth) {
                    $status = 'paid';
                } elseif ($monthlyFee <= 0) {
                    $status = 'no_contract';
                } elseif ($today >= 25) {
                    $status = 'due_soon';
                } else {
                    $status = 'overdue';
                }

                $concessionaireStatuses[$concessionaire->id] = $status;

                // The calendar classifies every month itself, so we only need to
                // hand it the contract window, which months are already paid, and
                // which unpaid months to pre-select (arrears + the current month).
                $contractStart = $latestApplication?->contract_period_start;
                $contractEnd = $latestApplication?->contract_period_end;
                $owedCount = 0;
                $preselect = [];

                if ($monthlyFee > 0) {
                    // Arrears: unpaid in-contract months from contract start to last month.
                    if ($contractStart) {
                        $cursor = $contractStart->copy()->startOfMonth();
                        $floor = $now->copy()->subMonths(24)->startOfMonth();
                        if ($cursor->lt($floor)) {
                            $cursor = $floor;
                        }
                        $lastMonth = $now->copy()->subMonthNoOverflow()->startOfMonth();

                        while ($cursor->lte($lastMonth)) {
                            $key = $cursor->format('Y-m');
                            $withinContract = ! $contractEnd || $cursor->lte($contractEnd);
                            if ($withinContract && ! $paidKeys->has($key)) {
                                $preselect[] = $key;
                                $owedCount++;
                            }
                            $cursor = $cursor->addMonthNoOverflow();
                        }
                    }

                    // Current month (if unpaid and still within the contract).
                    $currentCursor = $now->copy()->startOfMonth();
                    if (! $hasPaymentThisMonth && (! $contractEnd || $currentCursor->lte($contractEnd))) {
                        $preselect[] = $currentMonthKey;
                    }
                }

                // A concessionaire can still be selectable when arrears exist but
                // the current month is already paid, so key the button off the fee.
                $hasSelectableMonths = $monthlyFee > 0
                    && ($contractStart !== null || ! $hasPaymentThisMonth);

                $paymentPlans[$concessionaire->id] = [
                    'monthly_fee' => $monthlyFee,
                    'business' => $concessionaire->business_name ?: $concessionaire->name,
                    'name' => $concessionaire->name,
                    'owed_count' => $owedCount,
                    'current_unpaid' => ! $hasPaymentThisMonth && $monthlyFee > 0,
                    'has_selectable' => $hasSelectableMonths,
                    'current_month' => $currentMonthKey,
                    'contract_start' => $contractStart?->format('Y-m'),
                    'contract_end' => $contractEnd?->format('Y-m'),
                    'paid_months' => $paidKeys->keys()->values()->all(),
                    'preselect' => $preselect,
                ];
            }
        }

        return view('cashier.payments', compact(
            'concessionaires',
            'concessionaireStatuses',
            'paymentPlans'
        ));
    }

    public function historyIndex(Request $request)
    {
        $filters = [
            'concessionaire_id' => $request->query('concessionaire_id'),
            'date_from' => $request->query('date_from'),
            'date_to' => $request->query('date_to'),
            'payment_type' => $request->query('payment_type'),
        ];

        $filterConcessionaires = User::query()
            ->where('role', 'concessionaire')
            ->where('is_approved', true)
            ->orderByRaw('COALESCE(NULLIF(business_name, ""), name) asc')
            ->get(['id', 'name', 'business_name']);

        $recentPayments = ConcessionairePayment::query()
            ->with(['concessionaire', 'recordedBy'])
            ->orderByDesc('created_at')
            ->get();

        return view('cashier.history', compact(
            'filterConcessionaires',
            'recentPayments'
        ));
    }

    public function downloadReceipt(Request $request, ConcessionairePayment $payment)
    {
        $payment->loadMissing(['concessionaire', 'recordedBy', 'partnershipApplication']);

        $receiptYear = $payment->payment_date?->format('Y') ?? now()->format('Y');
        $receiptNumber = sprintf('RCP-%s-%05d', $receiptYear, $payment->id);
        $logoPath = public_path('images/eba-logo.png');
        $logoDataUri = file_exists($logoPath)
            ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
            : null;

        $pdf = Pdf::loadView('pdf.payment-receipt', [
            'payment' => $payment,
            'receiptNumber' => $receiptNumber,
            'generatedAt' => now(),
            'logoDataUri' => $logoDataUri,
        ])->setPaper('a4');

        $filename = sprintf('invoice-%s.pdf', $receiptNumber);

        return $pdf->download($filename);
    }

    public function downloadHistoryPdf(Request $request)
    {
        $payments = ConcessionairePayment::query()
            ->with(['concessionaire:id,name,business_name', 'recordedBy:id,name'])
            ->orderByDesc('created_at')
            ->get();

        $generatedAt = now();

        $pdf = Pdf::loadView('cashier.history-pdf', [
            'payments' => $payments,
            'generatedAt' => $generatedAt,
        ])->setPaper('a4', 'landscape');

        $filename = sprintf('cashier-payment-history-%s.pdf', $generatedAt->format('Ymd-His'));

        return $pdf->download($filename);
    }

    public function viewHistoryPdf(Request $request)
    {
        $payments = ConcessionairePayment::query()
            ->with(['concessionaire:id,name,business_name', 'recordedBy:id,name'])
            ->orderByDesc('created_at')
            ->get();

        $generatedAt = now();

        $pdf = Pdf::loadView('cashier.history-pdf', [
            'payments' => $payments,
            'generatedAt' => $generatedAt,
        ])->setPaper('a4', 'landscape');

        $filename = sprintf('cashier-payment-history-%s.pdf', $generatedAt->format('Ymd-His'));

        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }

    public function downloadConcessionaireHistoryPdf(Request $request, User $concessionaire)
    {
        $payments = ConcessionairePayment::query()
            ->where('concessionaire_id', $concessionaire->id)
            ->with(['recordedBy:id,name'])
            ->orderByDesc('created_at')
            ->get();

        $generatedAt = now();

        $pdf = Pdf::loadView('cashier.concessionaire-history-pdf', [
            'concessionaire' => $concessionaire,
            'payments' => $payments,
            'generatedAt' => $generatedAt,
        ])->setPaper('a4', 'landscape');

        $businessOrName = $concessionaire->business_name ?: $concessionaire->name;
        $safeName = preg_replace('/[^A-Za-z0-9]+/', '-', $businessOrName) ?: 'concessionaire';
        $filename = sprintf('cashier-payment-history-%s-%s.pdf', trim($safeName, '-'), $generatedAt->format('Ymd-His'));

        return $pdf->download($filename);
    }

    public function viewConcessionaireHistoryPdf(Request $request, User $concessionaire)
    {
        $payments = ConcessionairePayment::query()
            ->where('concessionaire_id', $concessionaire->id)
            ->with(['recordedBy:id,name'])
            ->orderByDesc('created_at')
            ->get();

        $generatedAt = now();

        $pdf = Pdf::loadView('cashier.concessionaire-history-pdf', [
            'concessionaire' => $concessionaire,
            'payments' => $payments,
            'generatedAt' => $generatedAt,
        ])->setPaper('a4', 'landscape');

        $businessOrName = $concessionaire->business_name ?: $concessionaire->name;
        $safeName = preg_replace('/[^A-Za-z0-9]+/', '-', $businessOrName) ?: 'concessionaire';
        $filename = sprintf('cashier-payment-history-%s-%s.pdf', trim($safeName, '-'), $generatedAt->format('Ymd-His'));

        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }

    public function storePayment(Request $request)
    {
        $validated = $request->validate([
            'concessionaire_id' => ['required', 'integer', 'exists:users,id'],
            'payment_date' => ['required', 'date'],
            'months' => ['required', 'array', 'min:1', 'max:24'],
            'months.*' => ['required', 'string', 'regex:/^\d{4}-\d{2}$/'],
            'amounts' => ['required', 'array'],
            'or_number' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $concessionaire = User::findOrFail((int) $validated['concessionaire_id']);

        if ($concessionaire->role !== 'concessionaire' || ! $concessionaire->is_approved || ! $concessionaire->is_active_concessionaire) {
            return back()->with('error', 'Selected user is not an active approved concessionaire.');
        }

        $partnershipApplication = PartnershipApplication::query()
            ->where('user_id', $concessionaire->id)
            ->whereIn('status', ['approved', 'registered'])
            ->latest()
            ->first();

        $receivedDate = Carbon::parse($validated['payment_date']);
        $monthKeys = collect($validated['months'])->unique()->values();

        $created = collect();
        $skipped = [];
        $invalid = [];

        foreach ($monthKeys as $monthKey) {
            $period = Carbon::createFromFormat('Y-m', $monthKey)->startOfMonth();
            $rawAmount = $request->input("amounts.$monthKey");

            if (! is_numeric($rawAmount) || (float) $rawAmount < 1) {
                $invalid[] = $period->format('F Y') . ' needs a valid amount.';

                continue;
            }

            $alreadyPaid = ConcessionairePayment::where('concessionaire_id', $concessionaire->id)
                ->whereYear('period_month', $period->year)
                ->whereMonth('period_month', $period->month)
                ->exists();

            if ($alreadyPaid) {
                $skipped[] = $period->format('F Y');

                continue;
            }

            $created->push(ConcessionairePayment::create([
                'partnership_application_id' => $partnershipApplication?->id,
                'concessionaire_id' => $concessionaire->id,
                'recorded_by' => Auth::id(),
                'amount' => (float) $rawAmount,
                'payment_date' => $receivedDate->toDateString(),
                'period_month' => $period->toDateString(),
                'payment_type' => 'cash',
                'or_number' => $validated['or_number'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]));
        }

        $businessName = $concessionaire->business_name ?: $concessionaire->name;

        if ($created->isEmpty()) {
            $messages = [];
            if ($skipped) {
                $messages[] = 'Already recorded for: ' . implode(', ', $skipped) . '.';
            }
            $messages = array_merge($messages, $invalid);
            if (empty($messages)) {
                $messages[] = 'No payments were recorded.';
            }

            return back()->withErrors(['months' => $messages])->withInput();
        }

        $totalAmount = $created->sum(fn ($payment) => (float) $payment->amount);
        $monthsCovered = $created
            ->map(fn ($payment) => Carbon::parse($payment->period_month)->format('M Y'))
            ->implode(', ');

        ActivityLog::log(
            'payment_recorded',
            'payment',
            (string) $created->first()->id,
            'Cashier recorded ₱' . number_format($totalAmount, 2) . " for {$businessName} covering {$monthsCovered}",
            [
                'total_amount' => (string) $totalAmount,
                'payment_ids' => $created->pluck('id')->all(),
                'months' => $created->map(fn ($payment) => Carbon::parse($payment->period_month)->format('Y-m'))->all(),
                'concessionaire_id' => $concessionaire->id,
                'recorded_by' => Auth::id(),
            ]
        );

        try {
            Mail::to($concessionaire->email)->send(
                new PaymentsRecordedMail(
                    $concessionaire,
                    $created->map->fresh(['recordedBy'])->values(),
                    $receivedDate
                )
            );
        } catch (\Exception $e) {
            Log::warning('Payment notification mail failed: ' . $e->getMessage(), [
                'controller' => self::class,
                'payment_ids' => $created->pluck('id')->all(),
                'recipient' => $concessionaire->email,
            ]);
        }

        $count = $created->count();
        $summary = '₱' . number_format($totalAmount, 2) . " recorded for {$businessName} — {$count} "
            . Str::plural('month', $count) . " ({$monthsCovered}).";
        if ($skipped) {
            $summary .= ' Skipped already-paid: ' . implode(', ', $skipped) . '.';
        }

        return back()->with('success', $summary);
    }
}

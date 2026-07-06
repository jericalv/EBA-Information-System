<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Mail\PaymentRecordedMail;
use App\Models\ActivityLog;
use App\Models\ConcessionairePayment;
use App\Models\PartnershipApplication;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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

        $paidThisMonth = ConcessionairePayment::whereYear('payment_date', now()->year)
            ->whereMonth('payment_date', now()->month)
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

        // ---- Real 6-month sparkline series for the stat cards ----
        $sparkMonths = collect(range(5, 0))->map(function ($i) {
            $month = now()->subMonths($i);

            return [
                'start' => $month->copy()->startOfMonth(),
                'end' => $month->copy()->endOfMonth(),
            ];
        });

        $windowPayments = ConcessionairePayment::where('payment_date', '>=', $sparkMonths->first()['start'])
            ->get(['concessionaire_id', 'amount', 'payment_date']);

        // All approved concessionaires (not only currently active) so historical
        // months are counted by the contract that actually covered them.
        $approvedConcessionaires = User::query()
            ->where('role', 'concessionaire')
            ->where('is_approved', true)
            ->with('latestPartnershipApplication')
            ->get(['id', 'monthly_fee', 'is_active_concessionaire']);

        $lastMonthIndex = $sparkMonths->count() - 1;
        $collectionsSpark = [];
        $activeSpark = [];
        $readySpark = [];
        $overdueSpark = [];

        $sparkMonths->each(function ($month, $index) use (
            $windowPayments,
            $approvedConcessionaires,
            $lastMonthIndex,
            $activeConcessionairesCount,
            $readyToRecordCount,
            $overdueCount,
            &$collectionsSpark,
            &$activeSpark,
            &$readySpark,
            &$overdueSpark
        ) {
            $inMonth = $windowPayments->filter(function ($payment) use ($month) {
                return $payment->payment_date
                    && $payment->payment_date->between($month['start'], $month['end']);
            });
            $paidIds = $inMonth->pluck('concessionaire_id')->unique();

            $collectionsSpark[] = round((float) $inMonth->sum('amount'), 2);

            $activeThisMonth = $approvedConcessionaires->filter(function ($concessionaire) use ($month) {
                $application = $concessionaire->latestPartnershipApplication;
                $start = $application?->contract_period_start;
                $end = $application?->contract_period_end;

                if (! $start) {
                    return false;
                }

                return $start->lte($month['end']) && ($end === null || $end->gte($month['start']));
            });

            $unpaidBacklog = $activeThisMonth->filter(function ($concessionaire) use ($paidIds) {
                return (float) ($concessionaire->monthly_fee ?? 0) > 0
                    && ! $paidIds->contains($concessionaire->id);
            })->count();

            if ($index === $lastMonthIndex) {
                // Current month: mirror the live figures shown on the cards.
                $activeSpark[] = $activeConcessionairesCount;
                $readySpark[] = $readyToRecordCount;
                $overdueSpark[] = $overdueCount;
            } else {
                $activeSpark[] = $activeThisMonth->count();
                $readySpark[] = $unpaidBacklog;
                $overdueSpark[] = $unpaidBacklog;
            }
        });

        $statSparklines = [
            'collections' => $collectionsSpark,
            'active' => $activeSpark,
            'ready' => $readySpark,
            'overdue' => $overdueSpark,
        ];

        return view('cashier.dashboard', compact(
            'activeConcessionairesCount',
            'overdueCount',
            'readyToRecordCount',
            'cashierMonthlyPayments',
            'cashierPaymentTypes',
            'statSparklines'
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

        $thisMonthCounts = ConcessionairePayment::query()
            ->selectRaw('concessionaire_id, COUNT(*) as payment_count')
            ->whereYear('payment_date', now()->year)
            ->whereMonth('payment_date', now()->month)
            ->groupBy('concessionaire_id')
            ->pluck('payment_count', 'concessionaire_id');

        $paidThisMonth = ConcessionairePayment::whereYear('payment_date', now()->year)
            ->whereMonth('payment_date', now()->month)
            ->pluck('concessionaire_id')
            ->toArray();

        $today = now()->day;
        $concessionaireStatuses = [];

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

            $concessionaires->each(function (User $concessionaire) use (
                $latestApplications,
                $paidThisMonth,
                $today,
                &$concessionaireStatuses
            ) {
                $latestApplication = $latestApplications->get($concessionaire->id);

                $concessionaire->setRelation(
                    'latestPartnershipApplication',
                    $latestApplication
                );

                $hasContractPeriod = filled($latestApplication?->contract_period_start)
                    && filled($latestApplication?->contract_period_end);
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

                $concessionaireStatuses[$concessionaire->id] = $status;
            });
        }

        return view('cashier.payments', compact(
            'concessionaires',
            'thisMonthCounts',
            'paidThisMonth',
            'concessionaireStatuses'
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
            'amount' => ['required', 'numeric', 'min:1'],
            'payment_date' => ['required', 'date'],
            'or_number' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $concessionaireId = (int) $validated['concessionaire_id'];

        $concessionaire = User::findOrFail($concessionaireId);

        if ($concessionaire->role !== 'concessionaire' || ! $concessionaire->is_approved || ! $concessionaire->is_active_concessionaire) {
            return back()->with('error', 'Selected user is not an active approved concessionaire.');
        }

        $existingPayment = ConcessionairePayment::where('concessionaire_id', $concessionaireId)
            ->whereYear('payment_date', now()->year)
            ->whereMonth('payment_date', now()->month)
            ->exists();

        if ($existingPayment) {
            return back()->withErrors(['limit' => 'Payment already recorded for this concessionaire this month.'])->withInput();
        }

        $partnershipApplication = PartnershipApplication::query()
            ->where('user_id', $concessionaire->id)
            ->whereIn('status', ['approved', 'registered'])
            ->latest()
            ->first();

        $payment = ConcessionairePayment::create([
            'partnership_application_id' => $partnershipApplication?->id,
            'concessionaire_id' => $concessionaire->id,
            'recorded_by' => Auth::id(),
            'amount' => $validated['amount'],
            'payment_date' => $validated['payment_date'],
            'payment_type' => 'cash',
            'or_number' => $validated['or_number'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        $formattedAmount = number_format((float) $payment->amount, 2);
        $businessName = $concessionaire->business_name ?: $concessionaire->name;

        ActivityLog::log(
            'payment_recorded',
            'payment',
            (string) $payment->id,
            "Cashier recorded payment of ₱{$formattedAmount} for {$businessName}",
            [
                'amount' => (string) $payment->amount,
                'payment_type' => $payment->payment_type,
                'concessionaire_id' => $concessionaire->id,
                'recorded_by' => Auth::id(),
            ]
        );

        try {
            Mail::to($concessionaire->email)->send(
                new PaymentRecordedMail($payment->fresh(['concessionaire', 'recordedBy', 'partnershipApplication']))
            );
        } catch (\Exception $e) {
            Log::warning('Payment notification mail failed: ' . $e->getMessage(), [
                'controller' => self::class,
                'payment_id' => $payment->id,
                'recipient' => $concessionaire->email,
            ]);
        }

        return back()->with('success', "Payment of ₱{$formattedAmount} recorded for {$businessName}.");
    }
}

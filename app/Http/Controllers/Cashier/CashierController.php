<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Mail\ContractPeriodSavedMail;
use App\Mail\PaymentsRecordedMail;
use App\Models\ActivityLog;
use App\Models\ConcessionairePayment;
use App\Models\PartnershipApplication;
use App\Models\User;
use App\Services\ConcessionaireFeeService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Mail\Mailable;
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

        $concessionaires = User::query()
            ->where('role', 'concessionaire')
            ->where('is_approved', true)
            ->where('is_active_concessionaire', true)
            ->get();

        $feeService = app(ConcessionaireFeeService::class);
        $plans = $feeService->plans($concessionaires);
        $counts = $feeService->statusCounts($plans);

        $overdueCount = $counts[ConcessionaireFeeService::STATUS_OVERDUE];
        $dueCount = $counts[ConcessionaireFeeService::STATUS_DUE]
            + $counts[ConcessionaireFeeService::STATUS_DUE_SOON];
        $readyToRecordCount = $dueCount + $overdueCount;

        // Collection status of active concessionaires for the current month.
        // The campus is cash-only, so a payment-method breakdown says nothing —
        // this shows who has settled the month instead.
        $cashierCollectionStatus = [
            'paid' => $counts[ConcessionaireFeeService::STATUS_PAID],
            'due' => $dueCount,
            'overdue' => $overdueCount,
            'no_contract' => $counts[ConcessionaireFeeService::STATUS_NO_FEE]
                + $counts[ConcessionaireFeeService::STATUS_NOT_STARTED]
                + $counts[ConcessionaireFeeService::STATUS_ENDED],
        ];

        $cashierMonthlyPayments = collect(range(5, 0))->map(function ($i) {
            $month = now()->subMonths($i);

            return [
                'month' => $month->format('M Y'),
                'total' => ConcessionairePayment::whereYear('payment_date', $month->year)
                    ->whereMonth('payment_date', $month->month)
                    ->sum('amount'),
            ];
        })->values()->toArray();

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
            'cashierCollectionStatus'
        ));
    }

    public function paymentsIndex(Request $request, ConcessionaireFeeService $feeService)
    {
        $concessionaires = User::query()
            ->where('role', 'concessionaire')
            ->where('is_approved', true)
            ->where('is_active_concessionaire', true)
            ->withSum('concessionairePayments as total_paid', 'amount')
            ->withMax('concessionairePayments as last_payment_date', 'payment_date')
            ->orderBy('business_name')
            ->orderBy('name')
            ->get();

        $paymentPlans = $feeService->plans($concessionaires);

        return view('cashier.payments', compact(
            'concessionaires',
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

    /**
     * Month the export should be limited to (?month=YYYY-MM), or null for all.
     */
    private function exportMonth(Request $request): ?Carbon
    {
        $month = $request->query('month');

        if (is_string($month) && preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $month)) {
            return Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        }

        return null;
    }

    /**
     * All payments for the history exports, optionally limited to the month
     * the payment was made in.
     */
    private function historyPayments(?Carbon $month)
    {
        return ConcessionairePayment::query()
            ->with(['concessionaire:id,name,business_name', 'recordedBy:id,name'])
            ->when($month, function ($query) use ($month) {
                $query->whereBetween('payment_date', [
                    $month->copy()->startOfDay(),
                    $month->copy()->endOfMonth()->endOfDay(),
                ]);
            })
            ->orderByDesc('created_at')
            ->get();
    }

    public function downloadHistoryPdf(Request $request)
    {
        $month = $this->exportMonth($request);
        $payments = $this->historyPayments($month);

        $generatedAt = now();

        $pdf = Pdf::loadView('cashier.history-pdf', [
            'payments' => $payments,
            'generatedAt' => $generatedAt,
            'periodLabel' => $month?->format('F Y'),
        ])->setPaper('a4', 'landscape');

        $filename = sprintf(
            'cashier-payment-history-%s%s.pdf',
            $month ? $month->format('Y-m') . '-' : '',
            $generatedAt->format('Ymd-His')
        );

        return $pdf->download($filename);
    }

    public function viewHistoryPdf(Request $request)
    {
        $month = $this->exportMonth($request);
        $payments = $this->historyPayments($month);

        $generatedAt = now();

        $pdf = Pdf::loadView('cashier.history-pdf', [
            'payments' => $payments,
            'generatedAt' => $generatedAt,
            'periodLabel' => $month?->format('F Y'),
        ])->setPaper('a4', 'landscape');

        $filename = sprintf(
            'cashier-payment-history-%s%s.pdf',
            $month ? $month->format('Y-m') . '-' : '',
            $generatedAt->format('Ymd-His')
        );

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

    /**
     * Wrap a value so spreadsheet apps keep it as literal text; otherwise
     * Excel converts date-like cells to date numbers that render as #####
     * when the column is too narrow.
     */
    private function csvText(string $value): string
    {
        return '="' . $value . '"';
    }

    public function downloadHistoryCsv(Request $request)
    {
        $month = $this->exportMonth($request);
        $payments = $this->historyPayments($month);

        $filename = sprintf(
            'payment-history-%s%s.csv',
            $month ? $month->format('Y-m') . '-' : '',
            now()->format('Ymd-His')
        );

        return response()->streamDownload(function () use ($payments) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'Concessionaire',
                'Business Name',
                'Amount Paid (PHP)',
                'Payment Date',
                'Period Covered',
                'Payment Status',
                'Payment Type',
                'OR Number',
                'Recorded By',
                'Recorded At',
            ]);

            foreach ($payments as $payment) {
                fputcsv($handle, [
                    $payment->concessionaire?->name ?: 'N/A',
                    $payment->concessionaire?->business_name ?: 'N/A',
                    number_format((float) $payment->amount, 2, '.', ''),
                    $payment->payment_date ? $this->csvText($payment->payment_date->format('Y-m-d')) : 'N/A',
                    $payment->period_month ? $this->csvText($payment->period_month->format('M Y')) : 'N/A',
                    $payment->paymentTiming() ?? 'N/A',
                    ucfirst(str_replace('_', ' ', (string) $payment->payment_type)),
                    $payment->or_number ?: 'N/A',
                    $payment->recordedBy?->name ?: 'N/A',
                    $payment->created_at ? $this->csvText($payment->created_at->setTimezone('Asia/Manila')->format('Y-m-d h:i A')) : 'N/A',
                ]);
            }

            fputcsv($handle, ['']);
            fputcsv($handle, ['Total Payments', $payments->count()]);
            fputcsv($handle, ['Total Amount Paid (PHP)', number_format((float) $payments->sum('amount'), 2, '.', '')]);

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function downloadConcessionaireHistoryCsv(Request $request, User $concessionaire)
    {
        $payments = ConcessionairePayment::query()
            ->where('concessionaire_id', $concessionaire->id)
            ->with(['recordedBy:id,name'])
            ->orderByDesc('created_at')
            ->get();

        $businessOrName = $concessionaire->business_name ?: $concessionaire->name;
        $safeName = preg_replace('/[^A-Za-z0-9]+/', '-', $businessOrName) ?: 'concessionaire';
        $filename = sprintf('payment-history-%s-%s.csv', trim($safeName, '-'), now()->format('Ymd-His'));

        return response()->streamDownload(function () use ($payments) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'Amount Paid (PHP)',
                'Payment Date',
                'Period Covered',
                'Payment Status',
                'Payment Type',
                'OR Number',
                'Recorded By',
                'Recorded At',
            ]);

            foreach ($payments as $payment) {
                fputcsv($handle, [
                    number_format((float) $payment->amount, 2, '.', ''),
                    $payment->payment_date ? $this->csvText($payment->payment_date->format('Y-m-d')) : 'N/A',
                    $payment->period_month ? $this->csvText($payment->period_month->format('M Y')) : 'N/A',
                    $payment->paymentTiming() ?? 'N/A',
                    ucfirst(str_replace('_', ' ', (string) $payment->payment_type)),
                    $payment->or_number ?: 'N/A',
                    $payment->recordedBy?->name ?: 'N/A',
                    $payment->created_at ? $this->csvText($payment->created_at->setTimezone('Asia/Manila')->format('Y-m-d h:i A')) : 'N/A',
                ]);
            }

            fputcsv($handle, ['']);
            fputcsv($handle, ['Total Payments', $payments->count()]);
            fputcsv($handle, ['Total Amount Paid (PHP)', number_format((float) $payments->sum('amount'), 2, '.', '')]);

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
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

    /**
     * Save contract period updates for partnership applications.
     */
    public function saveContractPeriod(Request $request, PartnershipApplication $application)
    {
        $editableStatuses = ['pending', 'under_review', 'approved', 'registered'];
        if (! in_array($application->status, $editableStatuses, true)) {
            return back()->withErrors([
                'contract_period' => 'Contract period can only be edited for pending, under review, approved, or registered applications.',
            ]);
        }

        $validated = $request->validate([
            'contract_period_start' => 'required|date',
            'contract_period_end' => 'required|date|after_or_equal:contract_period_start',
        ]);

        $application->contract_period_start = $validated['contract_period_start'];
        $application->contract_period_end = $validated['contract_period_end'];
        $application->save();

        $freshApplication = $application->fresh();
        if (! $freshApplication || ! $freshApplication->contract_period_start || ! $freshApplication->contract_period_end) {
            Log::warning('Contract period save verification failed: dates still null after save.', [
                'controller' => self::class,
                'application_id' => $application->id,
                'cashier_id' => Auth::id(),
            ]);

            return back()->with('error', 'Contract period could not be saved. Please try again.');
        }

        if ($this->shouldSendPartnershipUpdateEmail($freshApplication)) {
            $this->sendMail($freshApplication->email, new ContractPeriodSavedMail($freshApplication->fresh()));
        }

        ActivityLog::log(
            'contract_period_saved',
            'partnership',
            (string) $freshApplication->id,
            "Cashier saved contract period for partnership application #{$freshApplication->id}",
            [
                'application_id' => $freshApplication->id,
                'contract_period_start' => $validated['contract_period_start'],
                'contract_period_end' => $validated['contract_period_end'],
                'cashier_id' => Auth::id(),
                'cashier_email' => Auth::user()?->email,
            ]
        );

        return back()
            ->with('success', 'Contract period saved successfully.')
            ->with('contract_period_saved', true);
    }

    private function sendMail(string $recipient, Mailable $mailable): void
    {
        try {
            if (config('queue.default') !== 'sync') {
                Mail::to($recipient)->queue($mailable);
                return;
            }

            Mail::to($recipient)->send($mailable);
        } catch (\Exception $e) {
            Log::warning('Mail failed: ' . $e->getMessage(), [
                'controller' => self::class,
                'recipient' => $recipient,
                'mailable' => $mailable::class,
            ]);
        }
    }

    private function shouldSendPartnershipUpdateEmail(PartnershipApplication $application): bool
    {
        if (! $application->user_id) {
            return true;
        }

        $user = User::find($application->user_id);

        if (! $user) {
            return true;
        }

        return $user->getNotificationPreference('email_partnership_updates');
    }
}

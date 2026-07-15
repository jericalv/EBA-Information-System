<?php

namespace App\Console\Commands;

use App\Mail\PaymentDueReminderMail;
use App\Models\ActivityLog;
use App\Models\User;
use App\Services\ConcessionaireFeeService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendPaymentDueReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payment:send-due-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send payment reminder emails to concessionaires with an outstanding monthly fee';

    /**
     * Execute the console command.
     */
    public function handle(ConcessionaireFeeService $feeService): int
    {
        $today = Carbon::today();

        $concessionaires = User::query()
            ->where('role', 'concessionaire')
            ->where('is_approved', true)
            ->where('is_active_concessionaire', true)
            ->whereNotNull('monthly_fee')
            ->get();

        $plans = $feeService->plans($concessionaires);
        $dueDate = $today->format('F Y');
        $sentCount = 0;
        $skippedCount = 0;

        $remindable = [
            ConcessionaireFeeService::STATUS_DUE,
            ConcessionaireFeeService::STATUS_DUE_SOON,
            ConcessionaireFeeService::STATUS_OVERDUE,
        ];

        foreach ($concessionaires as $concessionaire) {
            $plan = $plans[$concessionaire->id];

            // Skip anyone with nothing outstanding: already covered (including
            // advance payments), contract not started yet, or contract ended.
            if (! in_array($plan['status'], $remindable, true)) {
                $skippedCount++;

                continue;
            }

            $monthlyFee = '₱' . number_format((float) $concessionaire->monthly_fee, 2);

            try {
                Mail::to($concessionaire->email)->send(new PaymentDueReminderMail($concessionaire));

                ActivityLog::create([
                    'user_id' => null,
                    'user_name' => 'System',
                    'action' => 'payment_due_reminder_sent',
                    'subject_type' => User::class,
                    'subject_id' => (string) $concessionaire->id,
                    'description' => "Payment due reminder sent to {$concessionaire->email}",
                    'details' => [
                        'message' => "Payment due reminder sent for {$dueDate}. Fee: {$monthlyFee}",
                    ],
                    'ip_address' => null,
                ]);

                $sentCount++;
            } catch (\Throwable $exception) {
                Log::warning('Payment due reminder mail failed: ' . $exception->getMessage(), [
                    'command' => self::class,
                    'concessionaire_id' => $concessionaire->id,
                    'recipient' => $concessionaire->email,
                ]);
            }
        }

        $this->info("Payment due reminders complete. Sent: {$sentCount}, Skipped (nothing outstanding): {$skippedCount}.");

        return self::SUCCESS;
    }
}
<?php

namespace App\Console\Commands;

use App\Mail\PaymentDueReminderMail;
use App\Models\ActivityLog;
use App\Models\ConcessionairePayment;
use App\Models\User;
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
    protected $description = 'Send payment due reminder emails to concessionaires on the monthly deadline day (1st of the month)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $today = Carbon::today();

        $concessionaires = User::query()
            ->where('role', 'concessionaire')
            ->where('is_approved', true)
            ->whereNotNull('monthly_fee')
            ->get();

        $dueDate = 'the 1st of ' . $today->format('F Y');
        $sentCount = 0;
        $skippedCount = 0;

        foreach ($concessionaires as $concessionaire) {
            $hasPaymentThisMonth = ConcessionairePayment::query()
                ->where('concessionaire_id', $concessionaire->id)
                ->whereMonth('payment_date', $today->month)
                ->whereYear('payment_date', $today->year)
                ->exists();

            if ($hasPaymentThisMonth) {
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

        $this->info("Payment due reminders complete. Sent: {$sentCount}, Skipped (already paid): {$skippedCount}.");

        return self::SUCCESS;
    }
}
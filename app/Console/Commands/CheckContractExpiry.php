<?php

namespace App\Console\Commands;

use App\Mail\ContractExpiredMail;
use App\Mail\ContractExpiryWarningMail;
use App\Models\ActivityLog;
use App\Models\PartnershipApplication;
use App\Notifications\ContractExpiryNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class CheckContractExpiry extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'contract:check-expiry';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check concessionaire contracts for expiry and send contract warnings';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $today = Carbon::today();

        $this->expireContracts($today);
        $this->sendWarnings($today, 30, 'warning_30_sent');
        $this->sendWarnings($today, 7, 'warning_7_sent');
        $this->sendWarnings($today, 1, 'warning_1_sent');

        $this->info('Contract expiry check completed.');

        return self::SUCCESS;
    }

    private function expireContracts(Carbon $today): void
    {
        $expiredApplications = PartnershipApplication::query()
            ->with('user')
            ->where('status', 'registered')
            ->whereNotNull('contract_period_end')
            ->whereDate('contract_period_end', '<', $today)
            ->whereHas('user', function ($query) {
                $query->where('role', 'concessionaire');
            })
            ->get();

        foreach ($expiredApplications as $application) {
            $user = $application->user;

            if (! $user) {
                continue;
            }

            DB::transaction(function () use ($application, $user) {
                $user->update([
                    'role' => 'concessionaire',
                    'is_approved' => false,
                    'is_active_concessionaire' => false,
                ]);

                $application->update([
                    'status' => 'expired',
                ]);

                if ($user->getNotificationPreference('email_contract_expiry')) {
                    Mail::to($user->email)->send(new ContractExpiredMail($application->fresh()));
                }

                if ($user->getNotificationPreference('in_app_notifications')) {
                    $user->notify(new ContractExpiryNotification(
                        'Your concessionaire contract has expired.',
                        null
                    ));
                }

                ActivityLog::create([
                    'user_id' => null,
                    'user_name' => 'System',
                    'action' => 'contract_expired',
                    'subject_type' => 'partnership',
                    'subject_id' => (string) $application->id,
                    'description' => "Contract expired for partnership application #{$application->id}",
                    'details' => [
                        'application_id' => $application->id,
                        'user_id' => $user->id,
                        'email' => $user->email,
                        'contract_period_end' => (string) $application->contract_period_end,
                    ],
                    'ip_address' => null,
                ]);
            });

            $this->line("Expired contract processed for application #{$application->id}.");
        }
    }

    private function sendWarnings(Carbon $today, int $days, string $flagColumn): void
    {
        $targetDate = $today->copy()->addDays($days)->toDateString();

        $warningApplications = PartnershipApplication::query()
            ->with('user')
            ->where('status', 'registered')
            ->whereNotNull('contract_period_end')
            ->whereDate('contract_period_end', $targetDate)
            ->where($flagColumn, false)
            ->whereHas('user', function ($query) {
                $query->where('role', 'concessionaire');
            })
            ->get();

        foreach ($warningApplications as $application) {
            $user = $application->user;

            if (! $user) {
                continue;
            }

            DB::transaction(function () use ($application, $user, $days, $flagColumn) {
                if ($user->getNotificationPreference('email_contract_expiry')) {
                    Mail::to($user->email)->send(new ContractExpiryWarningMail($application->fresh(), $days));
                }

                $application->update([
                    $flagColumn => true,
                ]);

                $message = $days === 1
                    ? 'Your contract expires in 1 day. Please contact admin to renew.'
                    : "Your contract expires in {$days} days. Please contact admin to renew.";

                if ($user->getNotificationPreference('in_app_notifications')) {
                    $user->notify(new ContractExpiryNotification($message, $days));
                }

                ActivityLog::create([
                    'user_id' => null,
                    'user_name' => 'System',
                    'action' => 'contract_expiry_warning_sent',
                    'subject_type' => 'partnership',
                    'subject_id' => (string) $application->id,
                    'description' => "Sent {$days}-day contract expiry warning for partnership application #{$application->id}",
                    'details' => [
                        'application_id' => $application->id,
                        'user_id' => $user->id,
                        'email' => $user->email,
                        'days_remaining' => $days,
                        'contract_period_end' => (string) $application->contract_period_end,
                        'flag_column' => $flagColumn,
                    ],
                    'ip_address' => null,
                ]);
            });

            $this->line("{$days}-day warning sent for application #{$application->id}.");
        }
    }
}

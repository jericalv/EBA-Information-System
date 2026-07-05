<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $now = now();

        $applications = DB::table('partnership_applications as pa')
            ->join('users as u', 'u.id', '=', 'pa.user_id')
            ->where('pa.status', 'approved')
            ->select([
                'pa.id as application_id',
                'pa.user_id',
                'pa.email as application_email',
                'u.role as user_role',
                'u.is_active_concessionaire',
            ])
            ->get();

        foreach ($applications as $application) {
            $action = 'stale_approved_path_a_fixed';
            $description = "Backfilled stale approved partnership application #{$application->application_id} to registered.";

            if ($application->user_role !== 'concessionaire') {
                DB::table('users')
                    ->where('id', $application->user_id)
                    ->update([
                        'role' => 'concessionaire',
                        'is_active_concessionaire' => true,
                        'updated_at' => $now,
                    ]);

                $action = 'stale_approved_path_a_upgraded';
                $description = "Upgraded linked user and backfilled application #{$application->application_id} to registered.";
            } else {
                DB::table('users')
                    ->where('id', $application->user_id)
                    ->where(function ($query) {
                        $query->whereNull('is_active_concessionaire')
                            ->orWhere('is_active_concessionaire', false);
                    })
                    ->update([
                        'is_active_concessionaire' => true,
                        'updated_at' => $now,
                    ]);
            }

            DB::table('partnership_applications')
                ->where('id', $application->application_id)
                ->update([
                    'status' => 'registered',
                    'updated_at' => $now,
                ]);

            DB::table('activity_logs')->insert([
                'user_id' => null,
                'user_name' => 'System',
                'action' => $action,
                'subject_type' => 'partnership',
                'subject_id' => (string) $application->application_id,
                'description' => $description,
                'details' => json_encode([
                    'application_id' => $application->application_id,
                    'user_id' => $application->user_id,
                    'email' => $application->application_email,
                    'initial_user_role' => $application->user_role,
                ]),
                'ip_address' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Data backfill intentionally not reversed.
    }
};

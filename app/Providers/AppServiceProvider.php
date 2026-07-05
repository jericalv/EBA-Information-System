<?php

namespace App\Providers;

use App\Models\ConcessionairePayment;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(\App\Services\WordFilterService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();

        View::composer('concessionaire.*', function ($view) {
            if (! auth()->check() || auth()->user()->role !== 'concessionaire') {
                return;
            }

            $user = auth()->user();
            $monthlyFee = $user->monthly_fee;
            $hasPaidThisMonth = false;
            $isOverdue = false;
            $isDueSoon = false;

            if ($monthlyFee && $monthlyFee > 0) {
                $now = now();
                $hasPaidThisMonth = ConcessionairePayment::where('concessionaire_id', $user->id)
                    ->whereYear('payment_date', $now->year)
                    ->whereMonth('payment_date', $now->month)
                    ->exists();

                $isDueSoon = ! $hasPaidThisMonth && $now->day >= 25 && $monthlyFee && $monthlyFee > 0;
                $isOverdue = ! $hasPaidThisMonth && $now->day >= 1 && $now->day < 25;
            }

            $view->with('hasOverduePayment', $isOverdue);
            $view->with('isDueSoon', $isDueSoon);
            $view->with('hasPaidThisMonth', $hasPaidThisMonth ?? false);
        });
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}

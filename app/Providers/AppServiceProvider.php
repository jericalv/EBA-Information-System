<?php

namespace App\Providers;

use App\Services\ConcessionaireFeeService;
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

            // Composers fire for every partial, so compute the plan once per request.
            $plan = request()->attributes->get('concessionaireFeePlan');
            if ($plan === null) {
                $plan = app(ConcessionaireFeeService::class)->planForUser(auth()->user());
                request()->attributes->set('concessionaireFeePlan', $plan);
            }

            $view->with('feePlan', $plan);
            $view->with('hasPaidThisMonth', $plan['current_paid'] && $plan['monthly_fee'] > 0);
            $view->with('isDueSoon', in_array($plan['status'], [
                ConcessionaireFeeService::STATUS_DUE,
                ConcessionaireFeeService::STATUS_DUE_SOON,
            ], true));
            $view->with('hasOverduePayment', $plan['status'] === ConcessionaireFeeService::STATUS_OVERDUE);
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

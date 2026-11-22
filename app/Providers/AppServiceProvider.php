<?php

namespace App\Providers;

use App\Models\User;
use App\Payments\{CardGateway, CounterGateway, EWalletGateway,
    FpxGateway, PaymentProcessor, SandboxGateway};
use App\Repositories\Cached\CachedInvoiceRepository;
use App\Repositories\Contracts\InvoiceRepository;
use App\Repositories\Eloquent\EloquentInvoiceRepository;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Decorator: cache di luar, Eloquent di dalam
        $this->app->bind(InvoiceRepository::class, function () {
            return new CachedInvoiceRepository(
                new EloquentInvoiceRepository(),
                cache()->store(),
            );
        });

        // Siapa bina senarai gateway? Bank sebenar hanya di production.
        $this->app->singleton(PaymentProcessor::class, fn ($app) =>
            new PaymentProcessor($app->environment('production')
                ? [$app->make(FpxGateway::class), $app->make(CardGateway::class),
                   $app->make(EWalletGateway::class), $app->make(CounterGateway::class)]
                : [new SandboxGateway()]
            )
        );
    }

    public function boot(): void
    {
        // Throw exception bila lazy loading berlaku, kecuali di produksi
        Model::preventLazyLoading(! $this->app->isProduction());

        Authenticate::redirectUsing(
            fn (Request $request) => $request->is('api/*') ? null : '/login'
        );
        RateLimiter::for('api', fn (Request $request) =>
            Limit::perMinute(60)->by($request->user()?->id ?: $request->ip()));
        RateLimiter::for('login', fn (Request $request) =>
            Limit::perMinute(5)->by($request->input('email').'|'.$request->ip()));
        RateLimiter::for('payments', fn (Request $request) =>
            Limit::perMinute(3)->by($request->user()->id));
        Gate::define('view-bursary-reports', fn (User $user) => $user->isBursary());
        Passport::tokensCan(['payments:read' => 'Baca senarai bayaran harian']);
        Passport::tokensExpireIn(now()->addHour());
    }
}

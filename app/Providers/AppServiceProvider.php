<?php

namespace App\Providers;

use App\Payments\CardGateway;
use App\Payments\CounterGateway;
use App\Payments\EWalletGateway;
use App\Payments\FpxGateway;
use App\Payments\PaymentProcessor;
use App\Payments\SandboxGateway;
use App\Repositories\Cached\CachedInvoiceRepository;
use App\Repositories\Contracts\InvoiceRepository;
use App\Repositories\Eloquent\EloquentInvoiceRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Decorator: cache di luar, Eloquent di dalam
        $this->app->bind(InvoiceRepository::class, function () {
            return new CachedInvoiceRepository(
                new EloquentInvoiceRepository,
                cache()->store(),
            );
        });

        // Siapa bina senarai gateway? Bank sebenar hanya di production.
        $this->app->singleton(PaymentProcessor::class, fn ($app) => new PaymentProcessor($app->environment('production')
                ? [$app->make(FpxGateway::class), $app->make(CardGateway::class),
                    $app->make(EWalletGateway::class), $app->make(CounterGateway::class)]
                : [new SandboxGateway]
        )
        );
    }

    public function boot(): void
    {
        Model::preventLazyLoading(
            ! $this->app->isProduction()
        );
    }
}

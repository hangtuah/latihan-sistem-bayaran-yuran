<?php

namespace App\Providers;

use App\Payments\CardGateway;
use App\Payments\EWalletGateway;
use App\Payments\FpxGateway;
use App\Payments\PaymentProcessor;
use App\Repositories\Contracts\InvoiceRepository;
use App\Repositories\Eloquent\EloquentInvoiceRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * The container bindings for the application.
     *
     * @var array<class-string, class-string>
     */
    public array $bindings = [
        InvoiceRepository::class => EloquentInvoiceRepository::class,
    ];
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(
            PaymentProcessor::class,
            fn ($app) => new PaymentProcessor([
                $app->make(FpxGateway::class),
                $app->make(CardGateway::class),
                $app->make(EWalletGateway::class),
            ]),
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

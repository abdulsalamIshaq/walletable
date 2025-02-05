<?php

namespace Walletable;

use Illuminate\Support\ServiceProvider;
use Walletable\WalletManager;
use Walletable\Commands\InstallCommand;
use Walletable\Facades\Wallet;
use Walletable\Internals\Lockers\OptimisticLocker;
use Walletable\Money\Formatter\IntlMoneyFormatter;
use Walletable\Money\Money;
use Walletable\Internals\Details\Info;
use Walletable\Internals\Details\MoneyCast;
use Walletable\Internals\Details\TextCast;
use Walletable\Transaction\CreditDebitAction;
use Walletable\Transaction\TransferAction;

class WalletableServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(WalletManager::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Money::formatter('intl', function () {
            return new IntlMoneyFormatter(
                new \NumberFormatter('en_US', \NumberFormatter::CURRENCY)
            );
        });

        Wallet::locker('optimistic', OptimisticLocker::class);

        Wallet::action('transfer', TransferAction::class);
        Wallet::action('credit_debit', CreditDebitAction::class);

        Info::cast('text', TextCast::class);
        Info::cast('money', MoneyCast::class);

        $this->addPublishes();
        $this->addCommands();
    }

    /**
     * Register Walletable's publishable files.
     *
     * @return void
     */
    public function addPublishes()
    {
        $this->publishes([
            __DIR__ . '/../config/walletable.php' => config_path('walletable.php')
        ], 'walletable.config');

        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'walletable.migrations');

        $this->publishes([
            __DIR__ . '/../database/models' => app_path('Models'),
        ], 'walletable.models');
    }

    /**
     * Register Walletable's commands.
     *
     * @return void
     */
    protected function addCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
            ]);
        }
    }
}

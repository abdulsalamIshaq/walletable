<?php

namespace Walletable\Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Walletable\Money\Currency;
use Walletable\Money\Money;
use Walletable\Tests\Models\Transaction;
use Walletable\Tests\Models\Wallet;
use Walletable\WalletableServiceProvider;

class TestBench extends BaseTestCase
{
    use MockeryPHPUnitIntegration;

    protected function getEnvironmentSetUp($app)
    {
        $config = require __DIR__ . '/../config/walletable.php';

        $app['config']->set('wallatable', $config);
        $app['config']->set('wallatable.models.wallet', Wallet::class);
        $app['config']->set('wallatable.models.transaction', Transaction::class);
        $this->migrate();
    }

    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            WalletableServiceProvider::class,
        ];
    }

    public function setUpCurrencies()
    {
        Money::currencies(
            Currency::new('NGN', '₦', 'Naira', 'Kobo', 100, 566),
            Currency::new('USD', '$', 'Dollar', 'Cent', 100, 840)
        );
    }

    public function migrate()
    {
        // import the CreateWalletsTable class from the migration
        include_once __DIR__ . '/../database/migrations/2020_12_25_001500_create_wallets_table.php';
        // import the CreateTransactionsTable class from the migration
        include_once __DIR__ . '/../database/migrations/2020_12_25_001600_create_transactions_table.php';

        // run the up() method of that migration class
        (new \CreateWalletsTable())->up();
        (new \CreateTransactionsTable())->up();


        Schema::create('walletables', function (Blueprint $table) {
            $table->id();
            $table->string('name', 75);
            $table->string('email', 75);
            $table->timestamps();
        });
    }
}

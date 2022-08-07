<?php

namespace Walletable\Tests;

use Walletable\Internals\Lockers\OptimisticLocker;
use Walletable\Money\Money;
use Walletable\Tests\Models\Transaction;

class LockerTest extends TestBench
{
    public function testOptimisticLockerCredit()
    {
        $this->setUpCurrencies();
        $locker = new OptimisticLocker();
        $wallet = $this->createWallet();

        ($transaction = new Transaction())->forceFill([
            'wallet_id' => $wallet->id,
            'type' => 'credit',
            'currency' => 'NGN',
        ]);

        $this->assertSame(0, $wallet->refresh()->amount->getInt());

        $locker->creditLock($wallet, Money::NGN(100000), $transaction);

        $transaction->syncOriginal();

        $this->assertSame(100000, $wallet->refresh()->amount->getInt());
        $this->assertSame(100000, $transaction->amount->getInt());
        $this->assertSame(100000, $transaction->balance->getInt());
    }

    public function testOptimisticLockerDebit()
    {
        $this->setUpCurrencies();
        $locker = new OptimisticLocker();
        $wallet = $this->createWallet(100000);

        ($transaction = new Transaction())->forceFill([
            'wallet_id' => $wallet->id,
            'type' => 'debit',
            'currency' => 'NGN',
        ]);

        $this->assertSame(100000, $wallet->refresh()->amount->getInt());

        $locker->debitLock($wallet, Money::NGN(100000), $transaction);

        $transaction->syncOriginal();

        $this->assertSame(0, $wallet->refresh()->amount->getInt());
        $this->assertSame(100000, $transaction->amount->getInt());
        $this->assertSame(0, $transaction->balance->getInt());
    }
}

<?php

namespace PayUzduotis\Controllers;

use PayUzduotis\Transactions;
use PayUzduotis\Transaction;
use PayUzduotis\Controllers\CurrencyController;

class FeeController
{
    private static $cashInFeePerc = 0.03;
    private static $cashOutFeePerc = 0.3;
    private $transaction = null;
    private $feeAmount = null;

    public function __construct($transaction)
    {
        $this->transaction = $transaction;
    }

    public static function calculatePercentage($num, $percent)
    {
        return ceil(($num * $percent)) / 100;
    }

    public function calculateTransactionFee()
    {
        if ($this->transaction->getType() == Transaction::$TYPES['cash_in'])
        {
            $this->calculateCashInFee();
        }
        else if ($this->transaction->getType() == Transaction::$TYPES['cash_out'])
        {
            $this->calculateCashOutFee();
        }
    }

    private function calculateCashInFee()
    {
        $sum = $this->transaction->getSum();
        $this->setAmount(self::calculatePercentage($sum, self::$cashInFeePerc));

        //fee can't be more than 5 EUR
        $sumEUR = $this->transaction->getSumEUR();
        $feeAmountEUR = self::calculatePercentage($sum, self::$cashInFeePerc);
        if ($feeAmountEUR > 5)
        {
            $feeAmountEUR = 5;
            $convBack = new CurrencyController($feeAmountEUR, CurrencyController::EUR);
            $convBack->convertTo($this->transaction->getCurrency());
            //overwrite the original fee amount to 5 EUR
            $this->setAmount($convBack->getSum());
        }
    }

    private function calculateCashOutFee()
    {
        if ($this->transaction->getUserType() == Transaction::$USER_TYPES['natural'])
        {
            $this->calculateNaturalCashOutFee();
        }
        else if ($this->transaction->getUserType() == Transaction::$USER_TYPES['juridical'])
        {
            $this->calculateJuridicalCashOutFee();
        }
    }

    private function calculateNaturalCashOutFee()
    {
        $sum = $this->transaction->getSum();
        $transactions = Transactions::getAllUserCashOutTransactionsFromSpecificWeek(Transactions::getAll(), $this->transaction->getId(), $this->transaction->getDate());
        
        //only apply the following rules if current transaction is no more than the 3rd one this week
        if (count($transactions) <= 3)
        {
            $transSum = Transactions::calculateTotalTransactionsSumInEUR($transactions);
            if ($transSum <= 1000)
            {
                $this->setAmount(0);
            }
            else
            {
                //check if current transaction went over the limit of 1000 EUR and if so, calculate by how much
                if ($transSum - $this->transaction->getSumEUR() < 1000)
                {
                    $diff = $transSum - 1000; //calculate the difference
                    $diff = new CurrencyController($diff, CurrencyController::EUR);
                    $diff->convertTo($this->transaction->getCurrency());
                    $sum = $diff->getSum();
                }
                //if the limit was already breached by a previous transaction, then we count fees from the whole sum
                else
                {
                    $sum = $this->transaction->getSum();
                }
                $this->setAmount(self::calculatePercentage($sum, self::$cashOutFeePerc));
            }
        }
        else 
        {
            $this->setAmount(self::calculatePercentage($sum, self::$cashOutFeePerc));
        }
    }

    private function calculateJuridicalCashOutFee()
    {
        $sum = $this->transaction->getSum();
        $this->setAmount(self::calculatePercentage($sum, self::$cashOutFeePerc));

        //fee can't be less than 0.5 EUR
        $sumEUR = $this->transaction->getSumEUR();
        $feeAmountEUR = self::calculatePercentage($sum, self::$cashOutFeePerc);
        if ($feeAmountEUR < 0.5)
        {
            $feeAmountEUR = 0.5;
            $convBack = new CurrencyController($feeAmountEUR, CurrencyController::EUR);
            $convBack->convertTo($this->transaction->getCurrency());
            //overwrite the original fee with the minimum 0.5 EUR
            $this->setAmount($convBack->getSum());
        }
    }

    public function getRaw()
    {
        return $this->feeAmount;
    }

    public function getTransaction()
    {
        return $this->transaction;
    }

    private function setAmount($fee)
    {
        $this->feeAmount = $fee;
    }
}

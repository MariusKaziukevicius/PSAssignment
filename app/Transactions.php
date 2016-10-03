<?php

namespace PayUzduotis;

use PayUzduotis\Transaction;
use PayUzduotis\Controllers\FeeController;

class Transactions
{
    private static $all = [];

    public static function addNew($data)
    {
        if (!is_object($data))
            $transaction = new Transaction($data);
        else
            $transaction = $data;
        array_push(self::$all, $transaction);

        $fee = new FeeController($transaction);
        $fee->calculateTransactionFee();
        $transaction->setFee($fee->getRaw());

        return $transaction;
    }
    
    public static function clearAllData()
    {
        self::$all = null;
        self::$all = [];
    }

    public static function getAll()
    {
        return self::$all;
    }

    public static function getAllUserCashOutTransactionsFromSpecificWeek($transactions, $id, $strDate)
    {
        $selectedTrans = array();
        foreach ($transactions as $trans)
        {
            if ($trans->getType() == Transaction::$TYPES['cash_out'] && $trans->getId() == $id)
            {
                $curr_week = date("W", strtotime($strDate));
                $trans_week = date("W", strtotime($trans->getDate()));
                if ($curr_week == $trans_week)
                {
                    $selectedTrans[] = $trans;
                }
            }
        }

        return $selectedTrans;
    }

    public static function calculateTotalTransactionsSumInEUR($transactions)
    {
        $sum = 0;
        foreach ($transactions as $trans)
        {   
            $sum += $trans->getSumEUR();
        }

        return $sum;
    }
}

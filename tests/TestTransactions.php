<?php

use PayUzduotis\Transactions;
use PayUzduotis\Transaction;

class TestTransactions extends UnitTestCase 
{
    private $transactions = [];

    function setUp()
    {
        $this->transactions = [
            new Transaction([   
                'id' => 1, 
                'date' => '2016-01-07', 
                'userType' => 'juridical', 
                'type' => 'cash_in', 
                'sum' => 300, 
                'currency' => 'EUR'
            ]),
            new Transaction([      
                'id' => 3, 
                'date' => '2016-01-11', 
                'userType' => 'juridical', 
                'type' => 'cash_out', 
                'sum' => 1500, 
                'currency' => 'JPY'
            ]),
             new Transaction([      
                'id' => 2, 
                'date' => '2016-02-23', 
                'userType' => 'natural', 
                'type' => 'cash_out', 
                'sum' => 125, 
                'currency' => 'USD'
            ]),
             new Transaction([      
                'id' => 2, 
                'date' => '2016-02-22', 
                'userType' => 'natural', 
                'type' => 'cash_out', 
                'sum' => 21, 
                'currency' => 'EUR'
            ])
        ];
    }

    function testCalculateTotalTransactionsSumInEUR()
    {
        $expected = 0;
        foreach ($this->transactions as $trans)
        {
            $expected += $trans->getSumEUR();
        }
        $this->assertEqual(Transactions::calculateTotalTransactionsSumInEUR($this->transactions), $expected);
    }

    function testGetAllUserCashOutTransactionsFromSpecificWeek()
    {
        //check if the right number of transactions is returned
        $expected = 2;
        $this->assertEqual(count(Transactions::getAllUserCashOutTransactionsFromSpecificWeek($this->transactions, 2, '2016-02-27')), $expected);

        //check if the right transactions are being returned
        $transactions = Transactions::getAllUserCashOutTransactionsFromSpecificWeek($this->transactions, 2, '2016-02-27');
        $expected = $transactions[0]->getSumEUR() + $transactions[1]->getSumEUR();
        $this->assertEqual(Transactions::calculateTotalTransactionsSumInEUR($transactions), $expected);
    }
}

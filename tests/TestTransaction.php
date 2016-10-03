<?php

use PayUzduotis\Transactions;
use PayUzduotis\Transaction;
use PayUzduotis\Controllers\CurrencyController;

class TestTransaction extends UnitTestCase 
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
                'userType' => 'natural', 
                'type' => 'cash_out', 
                'sum' => 1500, 
                'currency' => 'JPY'
            ]),
             new Transaction([      
                'id' => 2, 
                'date' => '2016-02-23', 
                'userType' => 'juridical', 
                'type' => 'cash_out', 
                'sum' => 125, 
                'currency' => 'USD'
            ]),
             new Transaction([      
                'id' => 2, 
                'date' => '2016-01-01', 
                'userType' => 'natural', 
                'type' => 'cash_out', 
                'sum' => 21, 
                'currency' => 'EUR'
            ])
        ];
    }

    function testConstructor()
    {
        $transaction = new Transaction([      
                'id' => 2, 
                'date' => '2016-01-01', 
                'userType' => 'natural', 
                'type' => 'cash_out', 
                'sum' => 21, 
                'currency' => 'EUR'
        ]);

        $expected = 2;
        $this->assertEqual($transaction->getId(), $expected);
        $expected = '2016-01-01';
        $this->assertEqual($transaction->getDate(), $expected);
        $expected = 'natural';
        $this->assertEqual($transaction->getUserType(), $expected);
        $expected = 'cash_out';
        $this->assertEqual($transaction->getType(), $expected);
        $expected = 21;
        $this->assertEqual($transaction->getSum(), $expected);
        $expected = CurrencyController::EUR;
        $this->assertEqual($transaction->getCurrency(), $expected);

        $transaction = new Transaction([      
                '2016-01-01', 
                2, 
                'natural', 
                'cash_out', 
                21, 
                'EUR'
        ]);

        $expected = 2;
        $this->assertEqual($transaction->getId(), $expected);
        $expected = '2016-01-01';
        $this->assertEqual($transaction->getDate(), $expected);
        $expected = 'natural';
        $this->assertEqual($transaction->getUserType(), $expected);
        $expected = 'cash_out';
        $this->assertEqual($transaction->getType(), $expected);
        $expected = 21;
        $this->assertEqual($transaction->getSum(), $expected);
        $expected = CurrencyController::EUR;
        $this->assertEqual($transaction->getCurrency(), $expected);
    }

    function testGetSumInEUR()
    {
        //JPY to EUR
        $transaction = $this->transactions[1];
        $expected = $transaction->getSum() / CurrencyController::getRate(CurrencyController::JPY);
        $this->assertEqual($transaction->getSumEUR(), $expected);

        //USD to EUR
        $transaction = $this->transactions[2];
        $expected = $transaction->getSum() / CurrencyController::getRate(CurrencyController::USD);
        $this->assertEqual($transaction->getSumEUR(), $expected);

        //EUR
        $transaction = $this->transactions[0];
        $expected = $transaction->getSum();
        $this->assertEqual($transaction->getSumEUR(), $expected);
    }
}

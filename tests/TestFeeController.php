<?php

use PayUzduotis\Transaction;
use PayUzduotis\Transactions;
use PayUzduotis\Controllers\FeeController;

class TestFeeController extends UnitTestCase 
{
    function tearDown()
    {
        Transactions::clearAllData();
    }
    
    function testCalculatePercentage()
    {
        $expected = 10;
        $this->assertEqual(feeController::calculatePercentage(100, 10), $expected);

        $expected = 14.08;
        $this->assertEqual(feeController::calculatePercentage(256, 5.5), $expected);
    }

    function testCalculateCashInFee()
    {
        $transactions = [
            new Transaction([      
                'id' => 2, 
                'date' => '2016-02-23', 
                'userType' => 'juridical', 
                'type' => 'cash_in', 
                'sum' => 125, 
                'currency' => 'USD'
        ]),
            new Transaction([      
                'id' => 1, 
                'date' => '2016-05-06', 
                'userType' => 'natural', 
                'type' => 'cash_in', 
                'sum' => 565, 
                'currency' => 'JPY'
        ]),
            new Transaction([      
                'id' => 1, 
                'date' => '2015-03-17', 
                'userType' => 'natural', 
                'type' => 'cash_in', 
                'sum' => 100000, 
                'currency' => 'EUR'
        ])];

        //check USD
        $expected = 0.04;
        Transactions::addNew($transactions[0]);
        $this->assertEqual($transactions[0]->getOperationFee(), $expected);
        
        //check JPY
        $expected = 0.17;
        Transactions::addNew($transactions[1]);
        $this->assertEqual($transactions[1]->getOperationFee(), $expected);
        
        //check the 5 EUR limit
        $expected = 5;
        Transactions::addNew($transactions[2]);
        $this->assertEqual($transactions[2]->getOperationFee(), $expected);
    }

    function testCalculateJuridicalCashOutFee()
    {
        $transactions = [
            new Transaction([      
                'id' => 2, 
                'date' => '2016-02-23', 
                'userType' => 'juridical', 
                'type' => 'cash_out', 
                'sum' => 10, 
                'currency' => 'EUR'
        ]),
            new Transaction([      
                'id' => 1, 
                'date' => '2016-05-06', 
                'userType' => 'juridical', 
                'type' => 'cash_out', 
                'sum' => 565, 
                'currency' => 'JPY'
        ]),
            new Transaction([      
                'id' => 1, 
                'date' => '2015-03-17', 
                'userType' => 'juridical', 
                'type' => 'cash_out', 
                'sum' => 1963, 
                'currency' => 'USD'
        ])];

        //check the 0.5 EUR minimum fee
        $expected = 0.5;
        Transactions::addNew($transactions[0]);
        $this->assertEqual($transactions[0]->getOperationFee(), $expected);

        //check JPY
        $expected = 1.7;
        Transactions::addNew($transactions[1]);
        $this->assertEqual($transactions[1]->getOperationFee(), $expected);

        //check USD
        $expected = 5.89;
        Transactions::addNew($transactions[2]);
        $this->assertEqual($transactions[2]->getOperationFee(), $expected);
    }

    function testCalculateNaturalCashOutFee()
    {
        $transactions = [
            new Transaction([      
                'id' => 1, 
                'date' => '2016-02-23', 
                'userType' => 'natural', 
                'type' => 'cash_out', 
                'sum' => 145, 
                'currency' => 'EUR'
        ]),
            new Transaction([      
                 'id' => 1, 
                 'date' => '2016-02-22', 
                 'userType' => 'natural', 
                 'type' => 'cash_out', 
                 'sum' => 565, 
                 'currency' => 'JPY'
        ]),
            new Transaction([      
                 'id' => 1, 
                 'date' => '2016-02-27', 
                 'userType' => 'natural', 
                 'type' => 'cash_out', 
                 'sum' => 125, 
                 'currency' => 'USD'
        ]),
           new Transaction([      
                 'id' => 1, 
                 'date' => '2016-02-25', 
                 'userType' => 'natural', 
                 'type' => 'cash_out', 
                 'sum' => 868, 
                 'currency' => 'USD'
        ]),
           new Transaction([      
                 'id' => 1, 
                 'date' => '2016-02-25', 
                 'userType' => 'natural', 
                 'type' => 'cash_out', 
                 'sum' => 1652, 
                 'currency' => 'USD'
        ])];

        //check if the 1st cash out of the week is free if it doesn't exceed 1000 EUR
        $expected = 0;
        Transactions::addNew($transactions[0]);
        $this->assertEqual($transactions[0]->getOperationFee(), $expected);

        //check if the 2nd cash out of the week is free if it doesn't exceed 1000 EUR
        $expected = 0;
        Transactions::addNew($transactions[1]);
        $this->assertEqual($transactions[1]->getOperationFee(), $expected);

        //check if the 3rd cash out of the week is free if it doesn't exceed 1000 EUR
        $expected = 0;
        Transactions::addNew($transactions[2]);
        $this->assertEqual($transactions[2]->getOperationFee(), $expected);

         //check if the 4th transaction of the week only applies a simple percentage fee
        $expected = 2.61;
        Transactions::addNew($transactions[3]);
        $this->assertEqual($transactions[3]->getOperationFee(), $expected);

        
        Transactions::clearAllData();
        //check if the 1st cash out of the week is free if it doesn't exceed 1000 EUR
        $expected = 0;
        Transactions::addNew($transactions[0]);
        $this->assertEqual($transactions[0]->getOperationFee(), $expected);

        //check if the 2nd cash out of the week is not free if it exceeds the total cash out sum for this week (1000 EUR), but the fee should only apply to the exceeded amount
        $expected = 2.01;
        Transactions::addNew($transactions[4]);
        $this->assertEqual($transactions[4]->getOperationFee(), $expected);

        //check if the 3rd cash out of the week is calculated with a simple percentage
        $expected = 1.7;
        Transactions::addNew($transactions[1]);
        $this->assertEqual($transactions[1]->getOperationFee(), $expected);
    }
}

<?php

use PayUzduotis\Controllers\CurrencyController;

class TestCurrencyController extends UnitTestCase 
{
    function testCurrencyConversion()
    {
        //convert 1 EUR to JPY
        $expected = CurrencyController::getRate('JPY');
        $curr = new CurrencyController(1, CurrencyController::EUR);
        $curr->convertTo(CurrencyController::JPY);
        $this->assertEqual($curr->getSum(), $expected);

        //convert 1 EUR to USD
        $expected = CurrencyController::getRate('USD');
        $curr = new CurrencyController(1, CurrencyController::EUR);
        $curr->convertTo(CurrencyController::USD);
        $this->assertEqual($curr->getSum(), $expected);

        //convert 1 JPY to EUR
        $expected = 1 / CurrencyController::getRate('JPY');
        $curr = new CurrencyController(1, CurrencyController::JPY);
        $curr->convertTo(CurrencyController::EUR);
        $this->assertEqual($curr->getSum(), $expected);

        //convert 1 USD to EUR
        $expected = 1 / CurrencyController::getRate('USD');
        $curr = new CurrencyController(1, CurrencyController::USD);
        $curr->convertTo(CurrencyController::EUR);
        $this->assertEqual($curr->getSum(), $expected);
    }

    function testCurrencyComparison()
    {
        $this->assertTrue(CurrencyController::compareTwo(CurrencyController::EUR, "eUR"));
        $this->assertFalse(CurrencyController::compareTwo(CurrencyController::JPY, CurrencyController::USD));
    }
}

<?php

namespace PayUzduotis\Controllers;

class CurrencyController 
{
    private $currency = null;
    private $sum = null;

    const EUR = "EUR";
    const USD = "USD";
    const JPY = "JPY";

    protected $currencies = [
        'EUR', 'USD', 'JPY'
    ];

    protected static $rates = [
        'EUR' => 1,
        'USD' => 1.1497,
        'JPY' => 129.53
    ];

    function __construct($sum, $currency)
    {
        $this->sum = $sum;
        $this->currency = $currency;
    }

    /**
    * Check if two currencies are the same
    *
    * @param $currency1
    * @param $currency2
    * @return boolean
    */
    public static function compareTwo($currency1, $currency2)
    {
        $currency1 = strtoupper($currency1);
        $currency2 = strtoupper($currency2);

        return $currency1 === $currency2;
    }

    public function convertTo($currency)
    {
        //check if we really need to convert, aka if the currencies are different
        if (self::compareTwo($this->currency, $currency))
        {
            //currency the same, so we call it a day
            return;
        }
        
        //convert currency to EUR
        if (self::compareTwo($currency, self::EUR))
        {
            $sum = $this->sum / self::$rates[$this->currency];
        }
        //convert currency from EUR to something else
        else
        {
            $sum = $this->sum * self::$rates[$currency];
        }

        $this->setSum($sum);
    }

    public function getSum()
    {
        return $this->sum;
    }

    public function setSum($sum)
    {
        $this->sum = $sum;
    }

    public static function getRate($currency)
    {
        return self::$rates[$currency];
    }
}

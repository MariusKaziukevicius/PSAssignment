<?php

namespace PayUzduotis;

use PayUzduotis\Controllers\CurrencyController;

class Transaction
{
    /**
     * Transaction types
    */
    public static $TYPES = [
        'cash_in' => 'cash_in',
        'cash_out' => 'cash_out'
    ];

     /**
     * User types
    */
    public static $USER_TYPES = [
        'natural' => 'natural',
        'juridical' => 'juridical'
    ];

    /**
     * The attributes that are assignable
     */
    protected $fields = [
        'date', 'id', 'userType', 'type', 'sum', 'currency'
    ];

    private $data = null;
    private $sumEUR = null;
    private $fee;

    function __construct($data)
    {
        //check if data is an associative array and if so - just clone it
        if (array_key_exists("date", $data))
        {
            $this->data = $data;
        }
        //otherwise we make the associations ourselves
        else
        {
            for ($i = 0; $i < count($this->fields); $i++)
            {
                $this->data[$this->fields[$i]] = $data[$i];
            }
        }

        //gets sum in EUR and converts currencies only if needed
        $eur = new CurrencyController($this->getSum(), $this->getCurrency());
        $eur->convertTo(CurrencyController::EUR);
        $this->sumEUR = $eur->getSum();
    }

    public function getId()
    {
        return $this->data['id'];
    }

    public function getDate()
    {
        return $this->data['date'];
    }

    public function getUserType()
    {
        return $this->data['userType'];
    }

    public function getType()
    {
        return $this->data['type'];
    }

    public function getSum()
    {
        return $this->data['sum'];
    }

    public function getSumEUR()
    {
        return $this->sumEUR;
    }

    public function getCurrency()
    {
        return $this->data['currency'];
    }

    public function getOperationFee()
    {
        return $this->fee;
    }

    public function setFee($fee)
    {
        $this->fee = $fee;
    }
}

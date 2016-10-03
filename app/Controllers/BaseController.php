<?php

namespace PayUzduotis\Controllers;

use PayUzduotis\Controllers\FileController;
use PayUzduotis\Transactions;

class BaseController
{
    //accepted commands
    private $commands = array("CalculateRemunerationFees");  
    //arguments that were passed  
    private $arguments = array();                              

    public function __construct()
    {
        
    }

    public static function makeSingleton()
    {
        return new BaseController();
    }

    public function init()
    {
        global $argv;
        $this->getArguments($argv);
    }

    public function executeCommand($command)
    {
        if ($command == "")
            return;
        //check if command exists
        if (in_array($command, $this->commands))
        {
            //construct the name of the function that executes the command
            $funcName = "command".$command;
            //call it
            if (!isset($this->arguments[1]))
                return;
            return $this->$funcName($this->arguments[1]);
        }
        else
        {
            //command not defined
        }
    }

    private function getArguments($args)
    {
        $this->arguments = $args;
    }

     /**
     * Reads a csv file. 
     * Calculates the remuneration fees for each transaction provided.
     *
     * @param  string $filePath
     * @return array containing all fees
     */
    private function commandCalculateRemunerationFees($filePath)
    {
       $file = new FileController($filePath);
       $file->setType("csv");
       $rawData = $file->parse();

       foreach ($rawData as $raw)
       {
           $transaction = Transactions::addNew($raw);
       }

       $fees = array();
       foreach (Transactions::getAll() as $transaction)
       {
           $fees[] = number_format($transaction->getOperationFee(), 2);
       }
       
       return $fees;
    }
}

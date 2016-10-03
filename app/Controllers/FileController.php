<?php

namespace PayUzduotis\Controllers;

class FileController
{
    private $filePath = null;
    private $fileType = null;

    public function __construct($filePath)
    {
        $this->filePath = $filePath;
    }

    public function setType($fileType)
    {
        $this->fileType = strtolower($fileType);
    }

    public function parse()
    {
        //for the purposes of the assignment only files of csv type are handled
        if ($this->fileType == "csv")
        {
            return $this->parseCSV();
        }
        else
        {
            //do nothing
        }
    }

    private function parseCSV()
    {
        if (($handle = fopen($this->filePath, "r")) !== false) 
        {
            $row = 0;
            $data = null;
            $returnData = array();
            while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                $num = count($data);
                $row++;
                $returnData[] = $data;
            }
            fclose($handle);
        }

        return $returnData;
    }
}

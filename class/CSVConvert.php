<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CSVConvert
 *
 * @author Ivan Zhu <wenhua.ivan@gmail.com>
 */
class CSVConvert{

    public $fileNameSource;
    public $filePathSource;
    public $fileNameResult;
    public $from_encoding;
    public $to_encoding;
	
    public $dataSource;
    public $errors = array();
    public $arrayConvered = array();

    public function __construct($filePath,$to_encoding = 'UTF-8',$from_encoding = 'UTF-16LE'){
        $this->filePathSource = $filePath;
        $this->to_encoding = $to_encoding;
        $this->from_encoding = $from_encoding;
    }

    public function getConvertedArray(){
        if(($data = file_get_contents($this->filePathSource)) !== false){
            if(($dataNew = mb_convert_encoding($data,"$this->to_encoding","$this->from_encoding")) !== FALSE){
                $this->arrayConvered = str_getcsv($dataNew, "\t","\r\n");
            }
        }
    }

}

?>

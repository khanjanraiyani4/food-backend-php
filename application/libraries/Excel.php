<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');  

/*require_once APPPATH."/third_party/excelclasses/PHPExcel.php";

class Excel extends PHPExcel {

    public function __construct() {
        parent::__construct();
    }
}*/

require APPPATH."/third_party/excel/vendor/autoload.php";

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Excel extends Spreadsheet {

    public function __construct() {
        parent::__construct();
    }

    public function print_sheet($sheet){
        return new Xlsx($sheet);
    }

    public function load($file_path){
        return IOFactory::createReader($file_path);
    }
}
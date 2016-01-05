<?php

class TimeManager {
    
    public $set_timezone;
    public $timezone;
    public $timestamp;
    public $full_date;
    
/* 
$mysqldate = date( 'Y-m-d H:i:s');
echo 'Fecha '.$mysqldate;
$phpdate = strtotime( $mysqldate );
echo ' Timestamp '. $phpdate;
echo '<br>';


$date = new DateTime();
echo $date->format('U = Y-m-d H:i:s') . "\n";

//$date->setTimestamp(1171502725);
//echo $date->format('U = Y-m-d H:i:s') . "\n";
 
date_default_timezone_set('Europe/London');
 if (ini_get('date.timezone')) {
    echo 'date.timezone: ' . ini_get('date.timezone');
} */

    public function SetTimeZone (){
        date_default_timezone_set($this->set_timezone);  
    }

    public function GetTimeZone() {
        if (date_default_timezone_get()) {
            $this->timezone = date_default_timezone_get();
        }
    }

    public function Today_Timestamp () {
        $date = new DateTime();
        //echo '<br> Timestamp';
        $this->timestamp = $date->format('U');
        //echo ' Hora: ' . $date->format('Y-m-d H:i:s') . "\n";
    }

    public function Today_Date () {
        $today = new DateTime();
       // echo '<br> Fecha propuesta: ';
         $this ->full_date = $today->format('Y-m-d H:i:s');
    }
}
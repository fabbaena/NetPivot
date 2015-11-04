<?php
class NetPivot {
    public function PrepareF5File($uuid,$filename){
        
        $command = 'tr -d \'\\015\' < '. $filename .' > '. $uuid;
        try {
            $pwd = exec($command,$output,$error);
            if ($error == 0){
                return true;
            } else {
                return false;
            }
        } catch (Exception $ex) {
            return false;
        }
    }
}
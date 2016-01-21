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
    
    public function getCounters ($uuid,$module,$obj) { //Get all the distinct objects for a specific module + object group
        $model = new Crud();
        $model->select='DISTINCT (obj_name)';
        $model->from='details';
        if ($obj !=''){
            $model->condition='files_uuid="'.$uuid.'" AND module="'.$module.'" AND obj_grp="'.$obj.'"';
        } else {
            $model->condition='files_uuid="'.$uuid.'" AND module="'.$module.'"';
        }
        $model->Read();
        $output = $model->rows;
        return $total = count($output);
    }

    public function getCNCO ($uuid,$module,$obj,$exception) {
        $model = new Crud();
        $model->select='*';
        $model->from='details';
        if ($exception==1 AND $obj!=''){
            $model->condition='files_uuid="'.$uuid.'" AND module="'.$module.'" AND obj_grp!="'.$obj.'" AND converted=1';
        } elseif ($exception==0 AND $obj!='') {    
            $model->condition='files_uuid="'.$uuid.'" AND module="'.$module.'" AND obj_grp="'.$obj.'" AND converted=1';
        } elseif ($exception==0 AND $obj=='') {   
            $model->condition='files_uuid="'.$uuid.'" AND module="'.$module.'" AND converted=1';
        }
        $model->Read();
        $rows = $model->rows;
        $converted = count($rows);

        $model2 = new Crud();
        $model2->select='*';
        $model2->from='details';
        if ($exception==1 AND $obj!=''){    
            $model2->condition='files_uuid="'.$uuid.'" AND module="'.$module.'" AND obj_grp!="'.$obj.'" AND converted=0 AND omitted=0';
        } elseif ($exception==0 AND $obj!='') {
            $model2->condition='files_uuid="'.$uuid.'" AND module="'.$module.'" AND obj_grp="'.$obj.'" AND converted=0 AND omitted=0';
        } elseif ($exception==0 AND $obj=='') {   
            $model2->condition='files_uuid="'.$uuid.'" AND module="'.$module.'" AND converted=0 AND omitted=0';
        }
        $model2->Read();
        $rows2 = $model2->rows;
        $noconverted = count($rows2);

        $model3 = new Crud();
        $model3->select='*';
        $model3->from='details';
        if ($exception==1 AND $obj!=''){    
            $model3->condition='files_uuid="'.$uuid.'" AND module="'.$module.'" AND obj_grp!="'.$obj.'" AND omitted=1';
        } elseif ($exception==0 AND $obj!='') {    
            $model3->condition='files_uuid="'.$uuid.'" AND module="'.$module.'" AND obj_grp="'.$obj.'" AND omitted=1';
        } elseif ($exception==0 AND $obj=='') {    
            $model3->condition='files_uuid="'.$uuid.'" AND module="'.$module.'" AND omitted=1';
        }
        $model3->Read();
        $rows3 = $model3->rows;
        $omited = count($rows3);
        $r=array();
        $r = array(1=>$converted,2=>$noconverted,3=>$omited);
        return $r;
    }
    
    public function getCNCO_Obj($uuid,$module,$obj) {
        $u = array();
        $model = new Crud();
        $model->select='*';
        $model->from='details';
        $model->condition='files_uuid="'.$uuid.'" AND module="'.$module .'" AND obj_grp="'.$obj.'" GROUP BY obj_name';
        $model->Read();
        $t = $model->rows;
        $total = count($t);
        for ($c=0;$c<$total;$c++){
            $tt = new Crud();
            $tt->select='*';
            $tt->from='details';
            $tt->condition='files_uuid="'.$uuid.'" AND module="'.$module.'" AND obj_grp="'.$obj.'" AND obj_name="'.$t[$c]['obj_name'].'"';
            $tt->Read();
            $ts = $tt->rows;
            $tstt= count($ts);
            $total_c=0;
            $total_nc=0;
            $total_o=0;
            for ($h=0;$h<$tstt;$h++){                                 
                if ($ts[0]['obj_name'] == $ts[$h]['obj_name']){
                    if ($ts[$h]['converted']==1){
                        $total_c = $total_c +1;
                    } 
                    if ($ts[$h]['converted']==0) {
                        $total_nc = $total_nc+1;
                    }
                    if ($ts[$h]['omitted']==1) {
                        $total_o = $total_o+1;
                    }
                }
                $e =array ();
                $e[1] =array ('name'=>$ts[$h]['obj_name'],'converted'=>$total_c,'no_converted'=>$total_nc,'omitted'=>$total_o);
            }
             $u = array_merge($u,$e);
        }
        return $u;
    }
    
    public function getTotalLines ($uuid) {
        $model=new Crud();
        $model->select='*';
        $model->from='details';
        $model->condition='files_uuid="'.$uuid.'"';
        $model->Read();
        $lines = $model->rows;
        $total = count($lines);
        return $total;
    }
    

}
 
<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../model/FileManager.php';
require '../model/Crud.php';



if (isset($_GET['file']) && isset($_GET['uuid'])) {
    $file = htmlspecialchars($_GET['file']);
    $uuid = htmlspecialchars($_GET['uuid']);
    $model = new FileManager;
    $model-> file = $file;
    $model->DeleteFile();
    $so = $model->message;
    if ($so == true) {
        try {
            $bd = new Crud();
            $bd->deleteFrom = 'files';
            $bd->condition = "uuid='" . $uuid . "'";
            $bd->Delete();
            $mensaje = $bd->mensaje;
            if ($mensaje == true) {
                header ('location: ../dashboard/index.php?deleted_file=true');
            } else {
                header ('location: ../dashboard/index.php?deleted_file=false');
            }        
        } catch (Exception $ex) {
            header ('location: ../dashboard/index.php?deleted_file=false');
        }
    } else {
        header ('location: ../dashboard/index.php?'. $so .'');
    }
} else {
    header ('location: ../dashboard/index.php?deleted_file=false');
}


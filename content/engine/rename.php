<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require '../model/StartSession.php';
require '../model/Crud.php';

$value = htmlspecialchars($_GET['newname']);
$uuid = htmlspecialchars($_GET['uuid']);

$model = new Crud();
$model->update ='files';
$model->set = "filename='$value'";
$model->condition = "uuid='$uuid'";
$model->Update();                        
$so = $model->mensaje;
if ($so == true) {
    header('location:../dashboard/index.php?newnok');
} else {
    header('location:../dashboard/index.php?newnnull');
}

?>
            


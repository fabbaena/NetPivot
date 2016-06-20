<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require '../model/StartSession.php';
require '../model/Crud.php';

$sesion = new StartSession();
$usuario = $sesion->get('usuario');
$user_type = $sesion->get('type');
if($usuario == false ) {
    header('location:../index.php');
} else {
    ?>

<!DOCTYPE html>
<html lang="en">
<head>

<?php include ('../engine/css.php');?>

<title>Conversions Errors & Warnings</title>  
</head>
<body >
<?php include ('../engine/menu1.php');?>
        
<?php include ('../engine/menu2.php'); ?>
     <div class="col-md-1"></div>
   <div class="col-md-10 content">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4>Conversions Errors & Warnings<br><small><a href="<?php echo $_SERVER['HTTP_REFERER']; ?>">Go back</a></small></h4>
            </div>
            <div class="panel-body">
                
                    <?php
                        $value = htmlspecialchars($_POST['uuid']);
                        $model = new Crud();
                        $model->select ='error_file';
                        $model->from = 'conversions';
                        $model->condition = 'files_uuid="'.$value.'"' ;
                        $model->Read();
                        $rows = $model->rows;
                        $mensaje = $model->mensaje;
                        
                        foreach ($rows as $info){
                            $file = $info['error_file'];
                        }

                        $file = '../dashboard/files/'. $file;
                         
                            if ( !($gestor = fopen("$file", "r"))) {
                            exit ('Unable to open the input file') ;
                        } else {
                            while (!feof($gestor))
                            {
                                $myline = fgets($gestor);
                                echo $myline;
                                echo '<br>';
                            }
                            fclose($gestor);
                        }  
                    ?>
            </div>
        </div>
   </div>
   <footer class="pull-left footer ">
            <p class="col-md-12">
                <hr class="divider">
                
            </p>
   </footer>

</body>
</html>

<?php
    }
?>
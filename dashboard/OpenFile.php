<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require '../model/StartSession.php';
require '../model/FileManager.php';

$sesion = new StartSession();
$usuario = $sesion->get('usuario');

if($usuario == false ) {
    header('location:../index.php');
} else {
    ?>

<!DOCTYPE html>
<html lang="en">
<head>

<?php include ('../engine/css.php');?>

<title>Original File</title>  
</head>
<body >
<?php include ('../engine/menu1.php');?>
        <li><a href="#"><span class="glyphicon glyphicon-dashboard"></span> Dashboard</a></li>
	<li class="active"><a href="index.php"><span class="glyphicon glyphicon-fire"></span> Convert F5 File</a></li>
<?php include ('../engine/menu2.php'); ?>
   <div class="col-md-10 content">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4>Original File</h4>
            </div>
            <div class="panel-body">
                
                    <?php
                        $file = htmlspecialchars($_POST['uuid']);
                         
                            if ( !($gestor = fopen("files/$file", "r"))) {
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
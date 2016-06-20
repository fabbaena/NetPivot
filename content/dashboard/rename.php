<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require '../model/StartSession.php';
require '../model/Crud.php';

$sesion = new StartSession();
$usuario = $sesion->get('usuario'); //Get username
$id= $sesion->get('id'); //Get user id
$user_type = $sesion->get('type'); //Get user type = administrator or user
if (isset($_GET['file'])) {
    $filename = htmlspecialchars($_GET['file']);
}

if($usuario == false ) { 
    header('location: /'); 
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>

<?php include ('../engine/css.php');?>

<title>Original File</title>  
</head>
<body>
<?php include ('../engine/menu1.php');?>
       
<?php include ('../engine/menu2.php'); ?>
    <div class="col-md-1"></div>
   <div class="col-md-10 content">
        <div class="panel panel-default">
            <div class="panel-body">
               <?php include('../engine/breadcrumbs.php');//Includethe breadcrumb ?>
            </div>
            
            <div class="panel-body">
                <div class="col-xs-12 content">
                        <h2 class="filename">Rename File</h2>
                
                        <br><br>
                <?php 
               
                    $model = new Crud();
                    $model->select='*';
                    $model->from='files';
                    $model->condition='uuid="'.$filename.'"';
                    $model->Read();
                    $esto = $model->rows;
                    
                    foreach ($esto as $aqui) {
                        echo '<strong>Current Name: </strong>'.$aqui['filename'].'<br><br>';
                    }
                    
                ?>
                                   
                <form action="GET">
                    <div class="form-group">
                        <label for="newname">New File Name:</label>  
                        <input type="text" name="newname" id="newname" size="40" required/><br/><br/>
                        <input type="hidden" name="uuid" id="uuid" value="<?php echo $filename; ?>">
                        <input type="submit" class="btn btn-primary btn-lg margin-set" title="Rename" formaction="../engine/rename.php">                
                    </div>
                </form>
                    
                    </div>
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

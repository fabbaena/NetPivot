<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require '../model/StartSession.php';
require '../model/Crud.php';
require '../model/ConnectionBD.php';
require '../model/TimeManager.php';

$sesion = new StartSession();
$usuario = $sesion->get('usuario'); //Got username
$id= $sesion->get('id'); //Got user id
$user_type = $sesion->get('type'); //Got user type = administrator or user

if($usuario == false ) { 
    header('location: /'); 
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>

        <?php include ('../engine/css.php');//Include links to stylesheets and js scripts ?>
        <title>NetPivot</title>  
    </head>
    <body >
    
    <?php include ('../engine/menu1.php');//Include the first part of the nav bar ?>
    
	
   
    <?php 
        include ('../engine/menu2.php');//Include the second part of the nav bar
        $user_id = htmlspecialchars($_GET['id']);
        $model = new Crud();
        $model->select = '*';
        $model->from = 'users';
        $model->condition = 'id=' . $user_id ;
        $model->Read();
        $info = $model->rows;
        foreach ($info as $data){
            $type = $data['type'];
            $max_files = $data['max_files'];
            $max_conversions = $data['max_conversions'];
        }
    ?>				
        <div class="col-md-1"></div>
    <div class="col-md-10 content">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4>Modify User</h4>
            </div>
            <div class="panel-body">
                <h4>Settings</h4><hr >
                <div class="table-responsive">
                    <form action="../engine/mod_user.php?id=<?php echo $user_id ?>" method="POST">
                            <div class="form-group">
                                
                                <label for="password">Reset Password :</label>
                                <input type="password" name="password" id="password" size="40"/><br/> <br/>                                
                                <label for="usertype">User type : </label>
                                <select name="usertype" id="usertype">
                                    <option value="" disabled="disabled" >User Type</option>
                                    <?php //Verify what type of user is and select it.
                                        if ($type == 'Administrator'){
                                            echo '<option value="Administrator"selected="selected" >Administrator</option>';
                                            echo '<option value="User" >User</option>';
                                        } else {
                                            echo '<option value="Administrator" >Administrator</option>';
                                            echo '<option value="User" selected="selected">User</option>';
                                        }
                                    ?>                                                                                             
                                </select></br></br>
                                

                                
                                   
                                <input type="submit" value="Save">
                                <?php 
                                    if(isset($_GET['new_done'])) {
                                        echo '<p class="text-primary">User created correctly</p>';
                                    }
                                     if(isset($_GET['user_exists'])) {
                                        echo '<p class="text-danger">User exists already</p>';
                                    }
                                     if(isset($_GET['new_error'])) {
                                        echo '<p class="text-warning">Error trying to create user, please try again</p>';
                                    }
                                ?>
                    </form>
                        </div>
                        <hr>
                </div>                
            </div>    
        </div>
        <footer class="pull-left footer">
            <p class="col-md-12">
                <hr class="divider">
            </p>
         </footer>
</body>
</html>

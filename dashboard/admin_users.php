<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../model/StartSession.php';
require '../model/FileManager.php';
require '../model/Crud.php';
require '../model/ConnectionBD.php';
require '../model/TimeManager.php';
 
$sesion = new StartSession();
$usuario = $sesion->get('usuario'); //Got username
$id= $sesion->get('id'); //Got user id
$user_type = $sesion->get('type'); //Got user type = administrator or user

if($usuario == false ) {
    header('location:../index.php');
} else {
    ?>

<!DOCTYPE html>
<html lang="en">
    <head>

        <?php include ('../engine/css.php');//Include links to stylesheets and js scripts ?>
        <title>NetPivot</title>  
    </head>
    <body >
    
    <?php include ('../engine/menu1.php');//Include the first part of the nav bar ?>
    
    
   
    <?php include ('../engine/menu2.php');//Include the second part of the nav bar?>				
        <div class="col-md-1"></div>
        <div class="col-md-10 content">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4>Users Management</h4>
            </div>
            <div class="panel-body">
                <h4>New User</h4><hr >
                    <div class="table-responsive">
                        <form action="../engine/add_user.php" method="POST">
                            <div class="form-group">
                                <label for="username">Username (required):</label>  
                                <input type="text" name="username" id="sender_name" size="40" required/><br/><br/> 
                                <label for="password">Password (required):</label>
                                <input type="password" name="password" id="password" size="40" required/><br/> <br/>
                                <label for="usertype">User type (required): </label>
                                <select name="usertype" id="usertype" required>
                                    <option value="" disabled="disabled" selected="selected">User Type</option>
                                    <option value="Administrator">Administrator</option>
                                    <option value="User">User</option>
                                </select></br></br>
                                <label for="max_files">Maximum number of files allowed to upload: </label>
                                <select name="max_files" id="usertype" required>
                                    <option value="" disabled="disabled" selected="selected"># Files</option>
                                    <option value="0">0</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                    <option value="6">6</option>
                                    <option value="7">7</option>
                                    <option value="8">8</option>
                                    <option value="9">9</option>
                                    <option value="10">10</option>
                                    <option value="100">Unlimited</option>
                                </select></br></br>
                                <label for="max_conversions">Maximum number of allowed conversions per file: </label>
                                <select name="max_conversions" id="usertype">
                                    <option value="" disabled="disabled" selected="selected"># Conversions</option>
                                    <option value="0">0</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                    <option value="6">6</option>
                                    <option value="7">7</option>
                                    <option value="8">8</option>
                                    <option value="9">9</option>
                                    <option value="10">10</option>
                                    <option value="100">Unlimited</option>
                                </select><br><br>
                                <input type="submit" value="Create">
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
                <h4>Users Created</h4>
                <table id="mytable" class="table table-bordred table-striped">
                    <thead>
                            <th>Username</th>
                            <th>Type</th>
                            <th>Max Files</th>
                            <th>Max Conversions</th>
                            <th>More</th>
                            <th>Edit</th>
                            <th>Delete</th>
                    </thead>
                    <tbody>
                            <?php
                                $model = new Crud();
                                $model->select = '*';
                                $model->from = 'users';
                                $model->Read();
                                $array = $model->rows;
                                foreach ($array as $data) {
                                    echo '<tr>';
                                    echo '<td style="width: 20%"> '. $data['name'] . "</td>";
                                    echo '<td style="width: 20%">'. $data['type'] . "</td>";
                                    if ($data['max_files']== 100) {
                                        echo '<td style="width: 20%">Unlimited</td>';
                                    } else {
                                        echo '<td style="width: 20%">'. $data['max_files'] . "</td>";
                                    }
                                      if ($data['max_files']== 100) {
                                        echo '<td style="width: 20%">Unlimited</td>';
                                    } else {
                                        echo '<td style="width: 20%">'. $data['max_conversions'] . "</td>";
                                    }                                   
                                    echo '<td style="width: 10%"><p data-placement="top" title="User details"><a href="info_user.php?id='. $data['id'] . '" class="btn btn-primary btn-xs" role="button"><span class="glyphicon glyphicon-eye-open"></span></a></p></td>';
                                    echo '<td style="width: 10%"><p data-placement="top" title="Modify user settings"><a href="modify_user.php?id='. $data['id'] . '" class="btn btn-warning btn-xs" role="button"><span class="glyphicon glyphicon-pencil"></span></a></p></td>';
                                    if ($data['id'] != 1) {
                                        echo '<td style="width: 10%"><p data-placement="top" title="Delete"><a href="../engine/delete_user.php?id='. $data['id'] .  '" class="btn btn-success btn-xs" role="button"><span class="glyphicon glyphicon-trash"></span></a></p></td>';
                                    } 
                                    
                                    echo '</tr>';
                                }
                            ?>
                    </tbody>
                </table>        
                <?php
                     if(isset($_GET['mod_ok'])) {
                        echo '<p class="text-primary">User modified correctly</p>';
                     }
                      if(isset($_GET['mod_error'])) {
                        echo '<p class="text-primary">Error modifing user settings, please try again.</p>';
                     }
                      if(isset($_GET['delete_ok'])) {
                        echo '<p class="text-primary">User deleted correctly.</p>';
                     }
                      if(isset($_GET['delete_error'])) {
                        echo '<p class="text-primary">Error deleting user, please try again.</p>';
                     }
                ?>
            </div>
        </div>        
        <footer class="pull-left footer">
            <p class="col-md-12">
                <hr class="divider">
            </p>
        </footer>
    </div>
</body>
</html>
<?php
}
?>
<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once dirname(__FILE__) .'/../model/StartSession.php';
require_once dirname(__FILE__) .'/../model/UserList.php';
require_once dirname(__FILE__) .'/../model/FileManager.php';

$session = new StartSession();
$user = $session->get('user');
if(!$user) {
    header('location: /'); 
    exit();
}

if (!isset($_GET['file'])) {
    header('location: ./'); 
    exit();
}

$uuid = htmlspecialchars($_GET['file']);

$file = new FileManager(array('uuid' => $uuid));
$file->load('uuid');

if(!$user->has_role("System Admin") && $file->users_id != $user->id) {
    header('location: ./');
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
       
    <div class="col-md-1"></div>
   <div class="col-md-10 content">
        <div class="panel panel-default">
            <div class="panel-body">
                <ol class="breadcrumb">
                    <li><a href="index.php">Files</a></li>
                </ol>
                <hr>
            </div>
            
            <div class="panel-body">
                <div class="col-xs-12 content">
                        <h2 class="filename">Rename File</h2>
                <form action="GET">
                    <div class="form-group">
                        <label for="newname">New File Name:</label>  
                        <input type="text" name="newname" id="newname" value="<?= $file->filename ?>" size="40" required/><br/><br/>
                        <input type="hidden" name="uuid" id="uuid" value="<?= $file->uuid ?>">
                        <input type="submit" class="btn btn-primary margin-set" title="Rename" formaction="../engine/rename.php">                
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

<?php 
$current_page = basename($_SERVER['PHP_SELF']);  
$filename = $sesion->get('filename');

?>

<ol class="breadcrumb">
    <li class=""><a href="index.php">Conversion Manager</a></li>
    <?php
    $x = array("modules.php","objects.php","text.php");
    if($current_page == 'brief.php') {
        echo '<li class="active">'.$filename.'</li>';
    } elseif(in_array($current_page,$x)) {
        echo '<li class=""><a href="brief.php">'.$filename.'</a></li>';
    }
    ?>
    <?php
    $x = array("objects.php","text.php");
    if($current_page == 'modules.php') {
        echo '<li class="active">Modules</li>';
    } elseif(in_array($current_page,$x)) {
        $url_value = "value=".$_GET['value'];
        echo '<li class=""><a href="modules.php?'.$url_value.'">Modules</a></li>';
    }
    ?>
    <?php
    $x = array("text.php");
    if($current_page == 'objects.php') {
        echo '<li class="active">Objects</li>';
    } elseif(in_array($current_page,$x)) {
        $url_value = "value=".$_GET['value'];
        $url_obj = "obj=".$_GET['obj'];
        echo '<li class=""><a href="objects.php?'.$url_value.'&'.$url_obj.'">Objects</a></li>';
    }
    ?>
    <?php
    if($current_page == 'text.php') {
        echo '<li class="active">Configuration</li>';
    }
    ?>
</ol>
<hr>

<?php
    //
    echo ("this is the php page!");
    if ($_SERVER['REQUEST_METHOD'] == 'POST'){
        echo($_POST['test']);     
    }
    
?>

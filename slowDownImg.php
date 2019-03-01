<?php 
    $numSec = rand(1,9);
    sleep($numSec);
    header("Content-Type: image/jpeg");
    readFile("http://lorempixel.com/400/200/");    
?>

<?php
    require('config.php');
    $servername = "localhost";
    $conn = new mysqli($servername, $un, $pw, $db);
    if ($conn->connect_error){
        die("connection failed: ".$conn->connect_error);
    }
    //$nextValue = rand(0, 10);
    $sessionId = generateRandomString(11);
    $height = rand(500,1800);
    $width = rand(500,1800);
    $connectTime = "";
    $renderTime = "";
    $deviceType = chooseRandDevice();
    $error = "";
    $time = "";
    for ($i = 0;$i < 1000; $i++){
        if (rand(0,10) > 7){
            $sessionId = generateRandomString(11); 
            $height = rand(500,1800);
            $width = rand(500,1800);
            $deviceType = chooseRandDevice();
        }   
        $connectTime = rand(0,1000);
        $renderTime = rand(0,1000);
        $error = "Error ".rand(100,400);
        $time = rand(1000,4000);
        /*
        echo $sessionId . " | ";
        echo $height. " | ";
        echo $width . " | ";
        echo $connectTime . " | ";
        echo $renderTime . " | ";
        echo $deviceType . " | ";
        echo $error . " | ";
        echo $time . " | ";
        echo "-----------";*/
        $ltInsert = "INSERT INTO loadTable VALUES ('{$sessionId}', '{$width}', '{$height}', '{$connectTime}', '{$renderTime}', '{$deviceType}');";
        if ($conn->query($ltInsert)){
            echo "inserted!\n";
        } 
        else{
            echo ("error\n");
            echo (mysqli_error($conn));
        }

        $etInsert = "INSERT INTO errorTable VALUES ('{$sessionId}', '{$error}', '{$time}')";
        if ($conn->query($etInsert)){
            echo "inserted!\n";
        }
        else{
            echo ("error\n");
            echo (mysqli_error($conn));
        }

    }   

    function chooseRandDevice(){
        $r = rand(0,2);
        if ($r==0){
            return "Chrome";
        }
        else if ($r == 1){
            return "Internet Explorer";
        }
        else{
            return "Firefox";
        }
    }
    function generateRandomString($length = 11) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
?>

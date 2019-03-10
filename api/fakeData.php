<?php
    //phpinfo();
    require('config.php');
    require('random-uagent/uagent.php');
    //$url = 'http://localhost:8000/api/log.php';
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
    $ua = random_uagent();
    $parsedUa = get_browser($ua,true);
    $os = $parsedUa['platform'];
    $browser = $parsedUa['browser'];
    $version = $parsedUa['version'];
    $error = "";
    $time = "";
    for ($i = 0;$i < 1000; $i++){
        if (rand(0,10) > 7){
            $sessionId = generateRandomString(11); 
            $height = rand(500,1800);
            $width = rand(500,1800);
            $ua = random_uagent();
            $parsedUa = get_browser($ua,true);
            $os = $parsedUa['platform'];
            $browser = $parsedUa['browser'];
            $version = $parsedUa['version'];

        }   
        $connectTime = rand(0,1000);
        $renderTime = rand(0,1000);
        $error = "Error ".rand(100,400);
        $time = rand(1000,4000);
       
        //insert directly into db 
        $ltInsert = "INSERT INTO loadTable VALUES ('{$sessionId}', '{$width}', '{$height}', '{$connectTime}', '{$renderTime}', '{$os}', '{$browser}', '{$version}');";
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

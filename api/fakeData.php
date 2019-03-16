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

    //delete existing values
    $loadDeleteStatement = "DELETE FROM loadTable;";
    if (mysqli_query($conn, $loadDeleteStatement)) {
        echo "Record deleted successfully";
    } 
    else {
        echo "Error deleting record: " . mysqli_error($conn);
    }
    
   $errorDeleteStatement = "DELETE FROM errorTable;";
    if (mysqli_query($conn, $errorDeleteStatement)) {
        echo "Record deleted successfully";
    } 
    else {
        echo "Error deleting record: " . mysqli_error($conn);
    }



    //$nextValue = rand(0, 10);
    $sessionId = generateRandomString(11);
    $randRes = randResolution();
    $width = $randRes[0];
    $height = $randRes[1];
    $connectTime = "";
    $renderTime = "";
    $ua = random_uagent();
    $parsedUa = get_browser($ua,true);
    $os = $parsedUa['platform'];
    $browser = $parsedUa['browser'];
    $version = $parsedUa['version'];
    $error = "";
    $time = rand(1546329600,1577865600); //random value between 1/1/2019 and 1/1/2020
    $file = randFile();
    for ($i = 0;$i < 1000; $i++){
        if (rand(0,10) > 7){
            $sessionId = generateRandomString(11);
            $randRes = randResolution();
            $width = $randRes[0];
            $height = $randRes[1];
            $ua = random_uagent();
            $parsedUa = get_browser($ua,true);
            $os = $parsedUa['platform'];
            $browser = $parsedUa['browser'];
            $version = $parsedUa['version'];
            $file = randFile();

        }   
        $connectTime = rand(0,1000);
        $renderTime = rand(0,1000);
        $error = "Error ".rand(100,400);
        $time = rand(1546329600,1577865600); //random value between 1/1/2019 and 1/1/2020
       
        //insert directly into db 
        $ltInsert = "INSERT INTO loadTable VALUES ('{$sessionId}', '{$width}', '{$height}', '{$connectTime}', '{$renderTime}', '{$os}', '{$browser}', '{$version}', '{$file}', '{$time}');";
        if ($conn->query($ltInsert)){
            //echo "inserted!\n";
        } 
        else{
            echo ("error\n");
            echo (mysqli_error($conn));
        }

        $etInsert = "INSERT INTO errorTable VALUES ('{$sessionId}', '{$error}', '{$time}', '{$os}', '{$browser}', '{$version}', '{$file}')";
        if ($conn->query($etInsert)){
           // echo "inserted!\n";
        }
        else{
            echo ("error\n");
            echo (mysqli_error($conn));
        }

    }   
    echo "done";
    function generateRandomString($length = 11) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    function randFile(){
        $i = rand(0,3);
        if ($i == 0){
            return "index.html";
        }
        else if ($i == 1){
            return "errorPages.html";
        }
        else if ($i == 2){
            return "slow.html";
        }
        else{
            return "randomLoad.html";
        }
    }
    function randResolution(){
        $i = rand(0,9);
        switch ($i) {
            case 0:
                return [1366,768];
            case 1:
                return [1600,900];
            case 2:
                return [1280,1024];
            case 3:
                return [1920,1080];
            case 4:
                return [1536,864];
            case 5:
                return [1024,768];
            case 6:
                return [1440,900];
            case 7:
                return [1280,800];
            case 8:
                return [1280,720];
            case 9: 
                $width = rand(1000, 2000);
                $height = rand(800, 1100);
                return [$width, $height];
        }
    }
?>  

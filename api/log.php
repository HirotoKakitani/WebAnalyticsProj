<?php
    require('config.php');
    if ($_SERVER['REQUEST_METHOD'] == 'POST'){
        //set up database connection
        $servername = "localhost";
        $conn = new mysqli($servername, $un, $pw, $db);
        if ($conn->connect_error){
            die("connection failed: ".$conn->connect_error);
        }

        //load log received
        if ($_POST['t'] == 'load'){
            //check for packet completeness
            if (!$_POST['id'] || !$_POST['w'] || !$_POST['h'] || !$_POST['d'] || !$_POST['ct'] || !$_POST['rt'] || !$_POST['fn'] || !$_POST['ti']){
                $conn->close();
                throw new Exception("Incomplete packet: {$_POST['id']}, {$_POST['w']}, {$_POST['h']}, {$_POST['d']}, {$_POST['ct']}, {$_POST['rt']}, {$_POST['fn']}, {$_POST['ti']} ");
            }
            echo ("this is a php page\n");
            $browserInfo = get_browser($_POST['d'], true);
            $stmt = $conn->prepare("INSERT INTO loadTable VALUES (?,?,?,?,?,?,?,?,?,?)");
            $stmt->bind_param("ssssssssss",$_POST['id'],$_POST['w'],$_POST['h'],$_POST['ct'],$_POST['rt'],$browserInfo['platform'],$browserInfo['browser'],$browserInfo['version'],$_POST['fn'],$_POST['ti']);

            if ($stmt->execute()){
                echo "inserted!\n";
            } 
            else{
                echo ("error\n");
                echo (mysqli_error($conn));
            }
        }
        
        //errror log received
        else {
             //check for packet completeness
            if (!$_POST['id'] || !$_POST['e'] || !$_POST['ti'] || !$_POST['t'] || !$_POST['d'] || !$_POST['el']){
                $conn->close();
                throw new Exception("Incomplete packet");
            }
            $browserInfo = get_browser($_POST['d'], true); 
            // send data to db
            $stmt = $conn->prepare("INSERT INTO errorTable VALUES (?,?,?,?,?,?,?)");
            $stmt->bind_param("sssssss",$_POST['id'],$_POST['e'],$_POST['ti'],$browserInfo['platform'],$browserInfo['browser'],$browserInfo['version'],$_POST['el']);

            if ($stmt->execute()){
                echo "inserted!\n";
            }
            else{
                echo ("error\n");
                echo (mysqli_error($conn));
            }
        }
    }
    else{
        echo("not a POST request");
    }

    $conn->close();
?>

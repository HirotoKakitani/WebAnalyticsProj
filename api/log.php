<?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST'){
        //set up database connection
        $servername = "localhost";
        $username = "";
        $password = "";
        $dbname = "";
        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error){
            die("connection failed: ".$conn->connect_error);
        }

        //load log received
        if ($_POST['t'] == 'load'){
            //check for packet completeness
            if (!$_POST['id'] || !$_POST['w'] || !$_POST['h'] || !$_POST['d'] || !$_POST['ct'] || !$_POST['rt']){
                $conn->close();
                throw new Exception("Incomplete packet: {$_POST['id']}, {$_POST['w']}, {$_POST['h']}, {$_POST['d']}, {$_POST['ct']}, {$_POST['rt']} ");
            }
            echo ("this is a php page\n");
            //$browser = get_browser($_POST['d'], true);
            $insertStatement = "INSERT INTO loadTable VALUES ('{$_POST['id']}', '{$_POST['w']}', '{$_POST['h']}', '{$_POST['ct']}', '{$_POST['rt']}', '{$_POST['d']}');";
            if ($conn->query($insertStatement)){
                echo "inserted!\n";
            } 
            else{
                echo ("error\n");
                echo (mysqli_error($conn));
            }
        }
        //error log received
        else {
             //check for packet completeness
            if (!$_POST['id'] || !$_POST['e'] || !$_POST['ti'] || !$_POST['t']){
                $conn->close();
                throw new Exception("Incomplete packet");
            }
            // send data to db
            $insertStatement = "INSERT INTO errorTable VALUES ('{$_POST['id']}', '{$_POST['e'] }', '{$_POST['ti']}')";
            if ($conn->query($insertStatement)){
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

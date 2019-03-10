<?php
    session_start();
    require('config.php');
    $invalidPage ="<html>
                       <head>
                           <title>Analytics</title>
                           <meta charset='UTF=8'>
                       </head>
                       <body>
                           <p>Incorrect credentials</p>
                           <a href='login.html'>Back</a>
                       </body>
                   </html>";

    if ($_SERVER['REQUEST_METHOD'] == 'POST'){
        //set up db conenction
        $servername = "localhost";
        $conn = new mysqli($servername, $un, $pw, $db);
        if ($conn->connect_error){
            die("connection failed: ".$conn->connect_error);
        }
        
        //TODO sanitize

        $selectStatement = "SELECT * FROM userTable WHERE username = '{$_POST['username']}'";
        $qResults = $conn->query($selectStatement);
        //hash the password
        //$pwHash = $conn->real_escape_string(password_hash($_POST['password'],PASSWORD_DEFAULT));
        $pwHash = password_hash($_POST['password'],PASSWORD_DEFAULT);
        echo ("{$pwHash} is the entered password<br>");

        if ($qResults->num_rows > 0){
            while($row = $qResults->fetch_assoc()){
                echo ("{$row['pwHash']} is the hash in the db<br>");    
                //check if username and password match an entry
                if ($_POST['username'] == $row['username'] && password_verify($_POST['password'], $row['pwHash'])){
                    echo ("User authenticated!");
                    $_SESSION['username'] = $row['username'];
                    $_SESSION['admin'] = $row['admin'];
                    header("Location: dashboard.php");
                    exit;
                }
                else{
                    echo ($invalidPage);
                }
            }
        }
        else{
            echo ($invalidPage);
        }

        $conn->close();
                 
    }
    else{
        echo ("not a post request");
    }
?>

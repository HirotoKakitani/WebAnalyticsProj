<?php
    //phpinfo();
    if ($_SERVER['REQUEST_METHOD'] == 'POST'){
        if ($_POST['username'] == 'admin' && $_POST['password']=='password1'){
            header("Location: dashboard.php");
            exit;
        }
        else{
            echo ("
                <html>
                    <head>
                        <title>Analytics</title>
                        <meta charset='UTF=8'>
                    </head>
                    <body>
                        <p>Incorrect credentials</p>
                        <a href='login.html'>Back</a>
                    </body>
                </html>
            ");
        }   
    }
    else{
        echo ("not a post request");
    }
?>

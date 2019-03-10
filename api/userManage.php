<?php
    session_start();
    //redirect to login if not logged in
    if (!$_SESSION['username']){
        header("Location: login.html");
    } 
    if ($_SESSION['admin'] == 'false'){
         echo ("
            <html>
                <head>
                    <title>Analytics</title>
                    <meta charset='UTF=8'>
                </head>
                <body>
                    <h1>Invalid Permissions</h1>
                    <a href=dashboard.php>Go Back</a>
                </body>
            </html>
        ");

    }
    else{ 
        echo ("
            <html>
                <head>
                    <title>Analytics</title>
                    <meta charset='UTF=8'>
                </head>
                <body>
                    <h1>User Management</h1>
                </body>
            </html>
        ");
    } 
?>

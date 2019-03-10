<?php
    session_start();
    //redirect to login if not logged in
    if (!$_SESSION['username']){
        header("Location: login.html");
    }   
    //make link to user management page visible only when logged in as admin
    if ($_SESSION['admin'] == 'true'){
        $adminLink = "<a href=userManage.php>Manage Errors </a>";
    }
    else{
        $adminLink = "";     
    }
    echo ("
        <html>
            <head>
                <title>Analytics</title>
                <meta charset='UTF=8'>
            </head>
            <body>
                <h1>Dashboard</h1>
                <a href=loadLog.php>Load Log (Client Characteristics and Speed)</a><br>
                <a href=errorLog.php>Error Log</a><br>
                {$adminLink}
            </body>
        </html>
    ");
 
?>

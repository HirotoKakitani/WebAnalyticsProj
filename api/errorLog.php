<?php
    session_start();
    //redirect to login if not logged in
    if (!$_SESSION['username']){
        header("Location: login.html");
    } 
    require('config.php');
    $servername = "localhost";
    $conn = new mysqli($servername, $un, $pw, $db);
    if ($conn->connect_error){
        die("connection failed: ".$conn->connect_error);
    }
    $selectStatement = "SELECT * FROM errorTable ORDER BY sessionId;";
    $qResults = $conn->query($selectStatement);
    $tableString = "<table>
                    <tr>
                        <th>Session Id</th>
                        <th>Error</th>
                        <th>Time</th>
                    </tr>
                    ";
    if ($qResults->num_rows > 0){
        while($row = $qResults->fetch_assoc()){
            //echo ("sessionId: " .$row['sessionId']. "\n   Device Type: " .$row['deviceType']."\n");
            $tableString .= "<tr> ". "<td>".$row['sessionId'] ."</td><td>".$row['error']."</td><td>".$row['time']."</td><td>";
        }
    }
    $tableString .= "</table>"; 
    echo ("
        <html>
            <head>
                <title>Analytics</title>
                <meta charset='UTF=8'>
            </head>
            <body>
                <h1>error Logs</h1>
                <a href=dashboard.php>Back</a>");
    echo ($tableString);
    echo("
            </body>
        </html>
    ");
  
?>

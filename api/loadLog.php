<?php
    $servername = "localhost";
    $username = "";
    $password = "";
    $dbname = "";
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error){
        die("connection failed: ".$conn->connect_error);
    }
    $selectStatement = "SELECT * FROM loadTable ORDER BY sessionId;";
    $qResults = $conn->query($selectStatement);
    $tableString = "<table>
                    <tr>
                        <th>Session Id</th>
                        <th>Width</th>
                        <th>Height</th>
                        <th>Connection Time</th>
                        <th>Render Time</th>
                        <th>Device Type</th>
                    </tr>
                    ";
    if ($qResults->num_rows > 0){
        while($row = $qResults->fetch_assoc()){
            //echo ("sessionId: " .$row['sessionId']. "\n   Device Type: " .$row['deviceType']."\n");
            $tableString .= "<tr> ". "<td>".$row['sessionId'] ."</td><td>".$row['width']."</td><td>".$row['height']."</td><td>".$row['connectTime']."</td><td>".$row['renderTime']."</td><td>".$row['deviceType']."</td></tr>";
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
                <h1>Load Logs</h1>
                <a href=dashboard.php>Back</a>");
    echo ($tableString);
    echo("
            </body>
        </html>
    ");
  
?>

<?php
    session_start();
    require('config.php');
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
        //set up database connection
        $servername = "localhost";
        $conn = new mysqli($servername, $un, $pw, $db);
        if ($conn->connect_error){
            die("connection failed: ".$conn->connect_error);
        }
        $selectStatement = "SELECT username, admin FROM userTable;";
        $qResults = $conn->query($selectStatement);
        $tableString = "<table>
                        <tr>
                            <th>User Name</th>
                            <th>Admin</th>
                            <th></th>
                        </tr>
                        ";
        if ($qResults->num_rows > 0){
            while($row = $qResults->fetch_assoc()){
                $tableString .= "
                                <tr>
                                    <td>{$row['username']}</td>
                                    <td>{$row['admin']}</td>
                                    <td><button class='edit'>Edit</button></td>
                                </tr>
                                ";
            }
        }
        $tableString .= "</table>"; 
 
        echo ("
            <html>
                <head>
                    <title>Analytics</title>
                    <meta charset='UTF=8'>
                    <link rel='stylesheet' type='text/css' href='style.css'>
                    <script src='userManage.js'></script>
                </head>
                <body>
                    <h1>User Management</h1>
                    <a href=dashboard.php>Back</a><hr>
                    {$tableString}
                    <div id='modalBox' class='modal'>
                        <!-- Modal content -->
                        <div id='modal-content'>
                            <input type='text' id='modalText'></input>
                            <input type='checkbox' id='modalCheck'>Admin</input><br>
                            <button id='save'>Save</button>
                            <button id='delete'>Delete</button>
                            <button id='close'>Close</button>
                        </div>
                    </div>
                    <input type='text' id='hiddenField' style='display:none'></input>
                </body>
            </html>
        ");
    } 
?>

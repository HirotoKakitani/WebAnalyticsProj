<?php
    session_start();
    //redirect to login if not logged in
    if (!$_SESSION['username']){
        header("Location: login.html");
    } 
    require('config.php');
    
    //Create database connection
    $servername = "localhost";
    $conn = new mysqli($servername, $un, $pw, $db);
    if ($conn->connect_error){
        die("connection failed: ".$conn->connect_error);
    }
 
    //edit user
    if ($_SERVER['REQUEST_METHOD'] == 'PUT'){
        parse_str(file_get_contents("php://input"),$putVars); //read put request from stdin
        $updateStatement = "UPDATE userTable SET username ='{$putVars['u']}', admin='{$putVars['a']}' WHERE username = '{$putVars['o']}'";
        if (mysqli_query($conn, $updateStatement)) {
            echo "Updated successfully";
        } 
        else {
            echo "Error updating record: " . mysqli_error($conn);
        }
    }
    //delete user
    else if ($_SERVER['REQUEST_METHOD'] == 'DELETE'){
        parse_str(file_get_contents("php://input"),$delVars); //read put request from stdin
        $deleteStatement = "DELETE FROM userTable WHERE username = '{$delVars['u']}'";
   
        if (mysqli_query($conn, $deleteStatement)) {
            echo "Record deleted successfully";
        } 
        else {
            echo "Error deleting record: " . mysqli_error($conn);
        }
    }

    else{
        echo "Not a put or delete request";
    }

    mysqli_close($conn);
?>

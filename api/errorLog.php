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
    $numUnique = 0;
    $uniqueStatement = "SELECT COUNT(DISTINCT sessionId) as count FROM errorTable;";
    $qResults = $conn->query($uniqueStatement);
    if ($qResults->num_rows > 0){
        $row = $qResults->fetch_assoc();
        $numUnique = $row['count'];
    }
    $numData = 0;
    $numStatement = "SELECT COUNT(*) as count FROM loadTable;";
    $qResults = $conn->query($numStatement);
    if ($qResults->num_rows > 0){
        $row = $qResults->fetch_assoc();
        $numData = $row['count'];
    }
    $numString= "<p id=numString>Collected <b>{$numData}</b> data points from <b>{$numUnique}</b> users.</p> "; 
    
    $script = "<script>
                ";
    $script = createErrorTimeline($conn, $script);
    $script = createErrorBrowserGraph($conn, $script);
    $script = createErrorOsGraph($conn, $script);
    $script = createErrorFileGraph($conn, $script);
     
    $script .= "</script>";
    
    echo ("
        <html>
            <head>
                <title>Analytics</title>
                <meta charset='UTF=8'>
                <link rel='stylesheet' type='text/css' href='lib/dygraph.css'>
                <script src='lib/dygraph.min.js'></script>
                <script src='lib/plotly-latest.min.js'></script>
           </head>
            <body>
                <h1>Error Logs</h1>
                <a href=dashboard.php>Back</a><hr>
                {$numString}
                <h2>Error Timeline</h2>
                <div id='errorTime'></div><hr>
                <h2>Browser Error Distribution</h2>
                <div id='browserError'></div><hr>
                <h2>OS Error Distribution</h2>
                <div id='osError'></div><hr>
                <h2>File Error Distribution</h2>
                <div id='fileError'></div>
            </body>
            {$script}
        </html>
    ");

    function createErrorTimeline($conn, $script){
        $selectStatement = "SELECT time FROM errorTable ORDER BY time;";
        $qResults = $conn->query($selectStatement);
        file_put_contents("errorData.csv", "");  //clears file
        $fp = fopen('errorData.csv','w');
        fputcsv($fp, ["time","Number of errors"]); 
        $errorTimeData=array();
        if ($qResults->num_rows > 0){
            //convert into Ymd format, push into array
            while($row = $qResults->fetch_assoc()){
                array_push($errorTimeData, date("Ymd", substr("{$row['time']}", 0, 10)));
            }
        }
        //count number of occurances on each day, write into csv file
        $errorCountsData = array_count_values($errorTimeData);
        foreach($errorCountsData as $date => $count){
            fputcsv($fp,[$date,$count]);
        }
        fclose($fp);
        $script .= "var errorTime = new Dygraph(document.getElementById('errorTime'),'errorData.csv',{width:1200});";
        return $script;
    }

    function createErrorBrowserGraph($conn,$script){
        $selectStatement = "SELECT browser, version, COUNT(browser) AS count FROM errorTable GROUP BY browser, version;";    //get number of errors per browser

        $qResults = $conn->query($selectStatement);
        $browserArray = array();
        $browserList = []; //list of browsers
        $countList = [];
        if ($qResults->num_rows > 0){
            while($row = $qResults->fetch_assoc()){
                $bFullName = "{$row['browser']} {$row['version']}";
                array_push($browserList,$bFullName);
                array_push($countList, $row['count']);
            }
        }
        $browserString = "'".implode("','", $browserList)."'";
        $countString = implode(",", $countList);
        $script .= " var browserData = [
                        {
                            x: [{$browserString}],
                            y: [{$countString}],
                            type: 'bar'
                        }
                    ];
                    Plotly.newPlot('browserError', browserData);";
                return $script;

    }


    function createErrorOsGraph($conn,$script){
        $selectStatement = "SELECT os, COUNT(os) AS count FROM errorTable GROUP BY os;";//get number of errors per os
        $qResults = $conn->query($selectStatement);
        $osList = []; //list of os in variable safe form
        $countList = [];
        if ($qResults->num_rows > 0){
            while($row = $qResults->fetch_assoc()){
                array_push($osList,$row['os']);
                array_push($countList, $row['count']);
            }
        }
        $osString = "'".implode("','", $osList)."'";
        $countString = implode(",", $countList);

        $script .= " var osData = [ 
                        {
                            x: [{$osString}],
                            y: [{$countString}],
                            type: 'bar'
                        }
                    ];
                    Plotly.newPlot('osError', osData);
                    ";
        return $script;

    }
    
    function createErrorFileGraph($conn,$script){
        $selectStatement = "SELECT filename, COUNT(filename) AS count FROM errorTable GROUP BY filename;
;";//get number of errors per os
        $qResults = $conn->query($selectStatement);
        $fileList = []; //list of os in variable safe form
        $countList = [];
        if ($qResults->num_rows > 0){
            while($row = $qResults->fetch_assoc()){
                array_push($fileList,$row['filename']);
                array_push($countList, $row['count']);
            }
        }
        $fileString = "'".implode("','", $fileList)."'";
        $countString = implode(",", $countList);

        $script .= " var fileData = [ 
                        {
                            x: [{$fileString}],
                            y: [{$countString}],
                            type: 'bar'
                        }
                    ];
                    Plotly.newPlot('fileError', fileData);
                    ";
        return $script;
    }
?>

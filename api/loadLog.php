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
    //Show number of data points collected
    $numUnique = 0;
    $uniqueStatement = "SELECT COUNT(DISTINCT sessionId) as count FROM loadTable;
";
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
    //Create the graphs
    $script = "<script>
              ";
    $script = createBrowserGraph($conn, $script);
    $script = createOsGraph($conn, $script);
    $script = createResGraph($conn, $script);
    $script = createLoadGraph($conn, $script); 
    $script .= "
              </script>";
    echo("
        <html>
            <head>
                <title>Analytics</title>
                <meta charset='UTF=8'>
                <link rel='stylesheet' type='text/css' href='lib/dygraph.css'>
                <script src='lib/dygraph.min.js'></script>
                <script src='lib/plotly-latest.min.js'></script>
            </head>
            <body>
                <h1>Load Logs</h1>
                <a href=dashboard.php id='backButton'>Back</a><hr>
                {$numString}
                <h2>Browser Distribution</h2>
                <div id='browserGraph'></div><hr>
                <h2>OS Distribution</h2>
                <div id='osGraph'></div><hr>
                <h2>Screen Resolution Distribution</h2>
                <div id='resGraph'></div><hr>
                <h2>Connection Times</h2>
                <div id='timeGraph'></div>
            </body>
            {$script}
        </html>
    ");
    
    function createBrowserGraph($conn, $script){
        $selectStatement = "SELECT browser, version, COUNT(DISTINCT sessionId) as count FROM loadTable GROUP BY browser, version;
";    //get unique browsers and counts
        $qResults = $conn->query($selectStatement);
        $browserArray = array();
        $bVarList = []; //list of browsers in variable safe form
        if ($qResults->num_rows > 0){
            while($row = $qResults->fetch_assoc()){
                $bFullName = "{$row['browser']} {$row['version']}";
                $bLabelName = getBrowser($bFullName);
                $bVarName = "brow".preg_replace("/(\W)+/", "", $bFullName); //make name variable safe
                array_push($bVarList,$bVarName);
                $script .= " var {$bVarName} = {
                        x: ['{$bLabelName}'],
                        y: [{$row['count']}],
                        name: '{$bFullName}',
                        type: 'bar'
                    };
                    ";
            }
        }
        $dataString = "var browserData = [". implode(",", $bVarList) . "];";
        $script .= $dataString;
        $script .= "
                var layout = {barmode: 'stack'};
                Plotly.newPlot('browserGraph', browserData, layout);
                ";
        return $script;
    }

    function createOsGraph($conn, $script){
        $selectStatement = "SELECT os, COUNT(DISTINCT sessionId) AS count FROM loadTable GROUP BY os;";    //get unique os and counts
        $qResults = $conn->query($selectStatement);
        $osVarList = []; //list of os in variable safe form
        if ($qResults->num_rows > 0){
            while($row = $qResults->fetch_assoc()){
                $osVarName = "os".preg_replace("/(\W)+/", "", $row['os']); //make name variable safe
                array_push($osVarList,$osVarName);
                $script .= " var {$osVarName} = {
                        x: ['{$row['os']}'],
                        y: [{$row['count']}],
                        name: '{$row['os']}',
                        type: 'bar'
                    };
                    ";
            }
        }
        $dataString = "var osData = [". implode(",", $osVarList) . "];";
        $script .= $dataString;
        $script .= "
                Plotly.newPlot('osGraph', osData, layout);
                ";
        return $script;
    }

    function createResGraph($conn,$script){
        $selectStatement = "SELECT width, height, COUNT(DISTINCT sessionId) as count FROM loadTable GROUP BY width, height;"; //get counts of resolutions 
        $qResults = $conn->query($selectStatement);
        $resVarList = []; //list of os in variable safe form
        if ($qResults->num_rows > 0){
            while($row = $qResults->fetch_assoc()){
                $resLabelName = "{$row['width']}x{$row['height']}";
                $resCondensed = getCondensedRes($resLabelName);
                $resVarName = "res{$row['width']}x{$row['height']}";
                array_push($resVarList,$resVarName);
                $script .= " var {$resVarName} = {
                        x: ['{$resCondensed}'],
                        y: ['{$row['count']}'],
                        name: '{$resLabelName}',
                        type: 'bar'
                    };
                    ";
            }
        }
        $dataString = "var resData = [". implode(",", $resVarList) . "];";
        $script .= $dataString;
        $script .= "
                var resLayout={showlegend:false, barmode:'stack'};
                Plotly.newPlot('resGraph', resData, resLayout);
                ";
        return $script;
    }

    function createLoadGraph($conn, $script){
        //get file pointer to write to csv file
        file_put_contents("timeData.csv", "");  //clears file
        $fp = fopen('timeData.csv','w');
        fputcsv($fp, ["time","Connect Time","Render Time"]); 
        $selectStatement = "SELECT connectTime, renderTime, filename, time FROM loadTable GROUP BY connectTime, renderTime, filename, time ORDER BY time;";
        $qResults = $conn->query($selectStatement);
        if ($qResults->num_rows > 0){
            while($row = $qResults->fetch_assoc()){
                $csvEntry = []; //list that represents entry into csv file
                array_push($csvEntry, date("Ymd", substr("{$row['time']}", 0, 10)));
                array_push($csvEntry, $row['connectTime']);
                array_push($csvEntry, $row['renderTime']);
                //array_push($csvEntry, $row['filename']);
                fputcsv($fp,$csvEntry);
            }
        }
        fclose($fp);
        $script .= "
                    var timeGraph = new Dygraph(document.getElementById('timeGraph'),'timeData.csv',{width:1200});
                    ";
        return $script;
    }

    //get just the browser type from $brow
    function getBrowser($brow){
        $splitBrow = explode(' ',$brow);
        if ($splitBrow[0] == "Chrome" || $splitBrow[0] == "Firefox" || $splitBrow[0] == "IE" || $splitBrow[0] == "Safari" || $splitBrow[0] == "Opera"){
            return $splitBrow[0];
        }
        else{
            return "Other";
        }
    } 

    function getCondensedRes($resLabelName){
        if ($resLabelName == "1366x768" ||$resLabelName == "1600x900" ||$resLabelName == "1280x1024" ||$resLabelName == "1920x1080" ||$resLabelName == "1536x864" ||$resLabelName == "1024x768" ||$resLabelName == "1440x900" ||$resLabelName == "1280x800" ||$resLabelName == "1280x720"){
            return $resLabelName;
        }
        else{
            return "Other";
        }
    }
?>

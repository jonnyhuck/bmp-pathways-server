<?php
require('connection.php');

//connect to database
if (!$dbconn = pg_connect($connstr)) {
    echo json_encode(array('received' => false, 'message' => pg_last_error()));
    $received = false;
    exit();
}

//build the query
$query = "select users.id_user, users.time_registered, count(gps_log.*), max(log_time) from users left join gps_log on users.id_user = gps_log.id_user group by users.id_user order by users.id_user;";

//insert data via stored proc (returns ID)
if (!$result = pg_query($query)) {
    $content = "<h1>" . pg_last_error() . "</h1>";
    exit();
}

//open data table
$content = "<table><tr><td><strong>User ID</strong></td><td><strong>Time of Registration</strong></td><td><strong>Number of Records</strong></td><td><strong>Most Recent Update</strong></td></tr>";

//populate rows
while ($row = pg_fetch_assoc($result)) {

    //get data
    $id_user = $row['id_user'];
    $time_reg = $row['time_registered'];
    $log_count = $row['count'];
    $update_time = $row['max'];

    //date verification
    if (empty($update_time)) {      //no date is set
        $update_time = "N/A";
        if (strtotime($time_reg) > strtotime('-24 hours')) {    //only just joined, not need to be red yet!
            $class = "class=\"green\"";
        } else {
            $class = "class=\"red\"";
        }
    } else if (strtotime($update_time) <= strtotime('-24 hours')) { //date is >= 12 hours old
        $update_time = date("D j M Y G:i:s", strtotime($update_time));
        $class = "class=\"red\"";
    } else {                        //date is < 12 hours old
        $update_time = date("D j M Y G:i:s", strtotime($update_time));
        $class = "class=\"green\"";
    }

    //write to content table
    $content .= "<tr><td $class>$id_user</td><td $class>".date("D j M Y G:i:s", strtotime($time_reg))."</td><td $class>$log_count</td><td $class>$update_time</td></tr>";
}

//close data table
$content .= "</table>";

//close the database connection
pg_close($dbconn);
?>

<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>BMP Portal</title>
        <meta name="description" content="Belfast Mobility Project Data Portal">
        <meta name="author" content="jonnyhuck">
        <meta http-equiv="refresh" content="60" />
        <style>
            body {
                font-family: "HelveticaNeue-Light", "Helvetica Neue Light", "Helvetica Neue", Helvetica, Arial, "Lucida Grande", sans-serif; 
                font-weight: 200;
                padding: 0px;
                margin: 10px;
                text-align: center;
            }
            .green {
                color: green;
            }
            .red {
                color: red;
            }
            table {
                margin-left:auto; 
                margin-right:auto;
                border-spacing: 10px;
                border-collapse: collapse;
            }
            td { 
                margin: 0px;
                padding: 5px;
                border: 0.5px solid grey;
            }
        </style>
    </head>
    <body>
        <h2>Welcome to the BMP Portal.</h2>
        <p class="green">Users that have uploaded data in the last 24 hours (or have not yet been signed up for 24 hours) are green.</p>
        <p class="red">Users that have <strong>not</strong> uploaded data in the last 24 hours are red.</p>
        <?php echo $content; ?>
    </body>
</html>
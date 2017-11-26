<?php
require('connection.php');

// Connecting, selecting database
$dbconn = pg_connect($connstr) or die('Could not connect: ' . pg_last_error());

//insert data into correct table
$query = "insert into users(dummy) values(1::bit) returning id_user;";
$result = pg_query($query) or die('Query failed: ' . pg_last_error());

// Get the result status
$status = pg_result_status($result);

//get the result
$userid = pg_fetch_array($result, 0, PGSQL_NUM);

// Determine status
if ($status == PGSQL_TUPLES_OK)
   $response = array('result' => "True", 'user_id' => $userid[0]);
else
   $response = array('result' => "False");

echo json_encode($response);
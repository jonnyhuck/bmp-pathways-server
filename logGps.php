<?php
require('connection.php');

//get and decode the json object
$json = json_decode(file_get_contents('php://input'));
$len = sizeof($json);
$received = true;

//connect to database
if(!$dbconn = pg_connect($connstr)){
	echo json_encode(array('received' => false, 'message' => pg_last_error()));
	$received = false;
	exit();
}

//insert every one...
for ($i=0; $i < $len; $i++) { 

	// 	verify input data
	if(!isset($json[$i]->id, $json[$i]->user_id, $json[$i]->lat, $json[$i]->lon, $json[$i]->acc, $json[$i]->timestamp)){
		array_push($response, $id);
		$received = false;
		continue;
	}

	//read input data
	$id = intval($json[$i]->id);
	$user_id = intval($json[$i]->user_id);
	$lat = floatval($json[$i]->lat);
	$lon = floatval($json[$i]->lon);
	$acc = floatval($json[$i]->acc);
	$logtime = floatval($json[$i]->timestamp);		

	//insert data via stored proc (returns ID)
	if(!$result = pg_query("SELECT * FROM Belfast_LogGPS($user_id, $lon, $lat, $acc, $logtime)")){ 
		array_push($response, $id);
		$received = false;
		continue;
	}
	
	// 	test result status
 	$status = pg_result_status($result);
 	if ($status != PGSQL_TUPLES_OK) {
 		array_push($response, $id);
 		$received = false;
 		continue;
 	}

	// 	check the result
 	$row = pg_fetch_array($result, 0, PGSQL_NUM);
 	if(!$row[0] > 0){
 		array_push($response, $id);
 		$received = false;
 		continue;
 	}

	// 	count rows
 	if(pg_affected_rows($result) != 1) {
 		array_push($response, $id);
 		$received = false;
 		continue;
 	}
}

//respond
echo json_encode(array("received" => $received, "notReceived" => $response));
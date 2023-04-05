<?php
// mysql
include("init.php");

// run id
if(!isset($_REQUEST["id"])) die("ERROR");

// header
header("Content-Type: text/plain");

// mysql
$stmt = $conn->query("SELECT source_code FROM runs WHERE run_id=".(int)$_REQUEST["id"]);
$row = $stmt->fetch_row();
print($row[0]);

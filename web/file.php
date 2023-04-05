<?php
// mysql
include("init.php");

// contest number
if(!isset($_REQUEST["id"])) die("<h1>Проблем</h1><h2>Не е посочен номер на задача.</h2>");
$id = (int)$_REQUEST["id"];

// extension
if(!isset($_REQUEST["ext"])) die("<h1>Проблем</h1><h2>Не е посочено разирението на файла.</h2>");
$ext = $conn->real_escape_string($_REQUEST["ext"]);

// sql & execute
$sql = "SELECT p.letter, c.set_code FROM problems as p, contests as c WHERE p.contest_id = c.contest_id AND p.problem_id=$id";
// echo $sql;
$result = $conn->query($sql);
$row = $result->fetch_assoc();

// close
$conn->close();

// document type (pdf,doc,docx,xls,xlsx,txt)
$ctype = "";
switch ($ext)
{
  case "pdf": $ctype = "application/pdf"; break;
  case "docx":
  case "doc": $ctype = "application/msword"; break;
  case "xls":
  case "xlsx": $ctype = "application/vnd.ms-excel"; break;
  default: $ctype = "application/force-download";
}

// load file
$letter = $row["letter"];
$file = $SETS_DIR."/".$row["set_code"]."/$letter/description.$ext";

// view file
header("Content-Type: $ctype");
header("Content-Disposition: attachment; filename=$letter.$ext;" );
readfile( $file );

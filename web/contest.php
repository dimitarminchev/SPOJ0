<?php
// current page
$page = "contests";

// header
include("header.php");

// init
include("init.php");

// contest number
if(!isset($_REQUEST["id"]))
die( sprintf("<div class='card bg-danger text-light m-4 p-4'><div class='card-body'><h1 class='card-title'>%s</h1><p class='card-text'>%s</p></div></div>",
	$lang["contest"]["problem"],
	$lang["contest"]["info1"]
));
$id = (int)$_REQUEST["id"];

// sql
$sql = <<<EOT
SELECT
   `contest_id`,
   `set_code`,
   `name`,
   `start_time`,
   `duration`,
   `show_sources`,
   `about`,
   UNIX_TIMESTAMP(NOW()) as `unow`,
   UNIX_TIMESTAMP(`start_time`) as `ustart`,
   UNIX_TIMESTAMP(`start_time`) + `duration` * 60 as `uend`
FROM `contests`
WHERE `contest_id` = $id
EOT;
// echo "<pre>$sql</pre>";

// execute sql
$result = $conn->query($sql);

// Contest exists check
if ($result->num_rows == 0)
die( sprintf("<div class='card bg-danger text-light m-4 p-4'><div class='card-body'><h1 class='card-title'>%s</h1><p class='card-text'>%s</p></div></div>",
	$lang["contest"]["problem"],
	$lang["contest"]["info2"]
));
$row = $result->fetch_assoc();

// Contest start time check
if ($row["unow"] < $row["ustart"])
die( sprintf("<div class='card bg-danger text-light m-4 p-4'><div class='card-body'><h1 class='card-title'>%s</h1><p class='card-text'>%s</p></div></div>",
	$lang["contest"]["problem"],
	$lang["contest"]["info3"]
));

// Contest end time check
if($row["unow"] > $row["uend"])
die( sprintf("<div class='card bg-danger text-light m-4 p-4'><div class='card-body'><h1 class='card-title'>%s</h1><p class='card-text'>%s</p></div></div>",
        $lang["contest"]["problem"],
        $lang["contest"]["info4"]
));

// start time
$start = new DateTime($row["start_time"]);

// contest header
$text = <<<EOT
<h1>%s</h1>
<div class="row">

<!-- 1 -->
<div class="col-md-4 mt-2">
 <div class="card">
  <div class="card-body">
   <p class="card-text"> %s </p>
   <h3 class="card-title"> %s </h3>
  </div>
 </div>
</div>

<!-- 2 -->
<div class="col-md-4 mt-2">
 <div class="card">
  <div class="card-body">
   <p class="card-text"> %s </p>
   <h3 class="card-title"> %s </h3>
  </div>
 </div>
</div>

<!-- 3 -->
<div class="col-md-4 mt-2">
 <div class="card">
  <div class="card-body">
   <p class="card-text"> %s </p>
   <h3 class="card-title"> %s </h3>
  </div>
 </div>
</div>

</div>
EOT;
echo sprintf( $text,
	$lang["contest"]["problems"],
	$lang["contest"]["contest"], $row["name"],
	$lang["contest"]["start"], $start->format("d.m.Y H:i"),
	$lang["contest"]["duration"], $row["duration"]
);

// contest table
$text = <<<EOT
<div class="row">
<div class="col-md-12">
<table class="table table-striped">
<thead>
<tr>
<th> %s </th>
<th> %s </th>
<th> %s </th>
<th class="text-end"> %s </th>
</tr>
</thead>
<tbody>
EOT;
echo sprintf( $text,
	$lang["contest"]["id"],
	$lang["contest"]["letter"],
	$lang["contest"]["about"],
	$lang["contest"]["action"]
);

// sql & execute
$sql = "SELECT * FROM problems WHERE contest_id=$id";
$result = $conn->query($sql);
if ($result->num_rows > 0) {

// print contest table
while($row = $result->fetch_assoc()) {

// contest row
$id = $row["problem_id"];
$about = $row["about"];
$letter = strtoupper($row["letter"]);

// contest row
$text = <<<EOT
<tr>
<td>$id</td>
<td>$letter</td>
<td>$about</td>
<td class='text-end'>
 <a href='description.php?id=$id' class='btn btn-outline-dark' role='button'>
  <i class="far fa-file-alt"></i> %s
 </a>
 <a href='submit.php?id=$id' class='btn btn-outline-dark' role='button'>
  <i class="fas fa-running"></i> %s
 </a>
 <a href='question-ask.php?id=$id' class='btn btn-outline-dark' role='button'>
  <i class="fas fa-question"></i> %s
 </a>
</td>
</tr>
EOT;
echo sprintf( $text,
	$lang["contest"]["description"],
	$lang["contest"]["submit"],
	$lang["contest"]["question"]
);

}}

// end table
echo "</tbody></table></div></div>";

// close
$conn->close();

// footer
include("footer.php");

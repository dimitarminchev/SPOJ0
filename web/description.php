<?php
// current page
$page = "contests";

// header
include("header.php");

// initialization
include("init.php");

// contest number
if(!isset($_REQUEST["id"])) 
die( sprintf("<div class='card bg-danger text-light m-4 p-4'><div class='card-body'><h1 class='card-title'>%s</h1><p class='card-text'>%s</p></div></div>",
	$lang["description"]["problem"],
	$lang["description"]["info1"]
));
$id = (int)$_REQUEST["id"];

// sql
$sql = <<<EOT
SELECT p.*,
   c.set_code as c_code,
   c.name as c_name,
   c.start_time as c_start,
   c.duration as c_duration,
   c.show_sources as c_show_sources,
   UNIX_TIMESTAMP(NOW()) as `unow`,
   UNIX_TIMESTAMP(`c`.`start_time`) as `ustart`,
   UNIX_TIMESTAMP(`c`.`start_time`) + `c`.`duration` * 60 as `uend`
FROM problems as p
INNER JOIN contests as c ON p.contest_id = c.contest_id
HAVING 1=1 AND problem_id=$id
EOT;
// echo "<pre>$sql</pre>";

// execute sql
$result = $conn->query($sql);

// Problem exists check
if ($result->num_rows == 0)
die( sprintf("<div class='card bg-danger text-light m-4 p-4'><div class='card-body'><h1 class='card-title'>%s</h1><p class='card-text'>%s</p></div></div>",
        $lang["description"]["problem"],
        $lang["description"]["info2"]
));
$row = $result->fetch_assoc();

// Contest start time check
if($row["unow"] < $row["ustart"])
die( sprintf("<div class='card bg-danger text-light m-4 p-4'><div class='card-body'><h1 class='card-title'>%s</h1><p class='card-text'>%s</p></div></div>",
	$lang["description"]["problem"],
	$lang["description"]["info3"]
));

// Contest end time check
if($row["unow"] > $row["uend"])
die( sprintf("<div class='card bg-danger text-light m-4 p-4'><div class='card-body'><h1 class='card-title'>%s</h1><p class='card-text'>%s</p></div></div>",
	$lang["description"]["problem"],
        $lang["description"]["info4"]
));

// data for header
$name = $row["c_name"];
$letter = strtoupper($row["letter"]);
$about = $row["about"];

// header
$text = <<<EOT
<h1> %s </h1>
<div class="row">

<!-- 1 -->
<div class="col-md-4 mt-2">
 <div class="card">
  <div class="card-body">
   <p class="card-text"> %s </p>
   <h3 class="card-title"> $name </h3>
  </div>
 </div>
</div>

<!-- 2 -->
<div class="col-md-4 mt-2">
 <div class="card">
  <div class="card-body">
   <p class="card-text"> %s </p>
   <h3 class="card-title"> $letter </h3>
  </div>
 </div>
</div>

<!-- 3 -->
<div class="col-md-4 mt-2">
 <div class="card">
  <div class="card-body">
   <p class="card-text"> %s </p>
   <h3 class="card-title"> $about </h3>
  </div>
 </div>
</div>

</div>
<!-- /row -->
EOT;
echo sprintf( $text, 
	$lang["description"]["description"], 
	$lang["description"]["contest"], 
	$lang["description"]["letter"], 
	$lang["description"]["about"]
);

// file path
$filepath = $SETS_DIR."/".$row["c_code"]."/".$row["letter"];

// Read and print all description files
$files = glob("$filepath/description.*");
if(!empty($files))
{
	echo "<div>";
	for($i=0;$i<count($files);$i++)
	{
		$ext = pathinfo($files[$i], PATHINFO_EXTENSION);
		$file = pathinfo($files[$i], PATHINFO_FILENAME);
		echo "<a href='file.php?id=$id&ext=$ext'><img src='assets/$ext.png' style='margin:10px;'></a>";
	}
	echo "</div>";
}

// read and print description text file
$source = file_get_contents("$filepath/description.txt"); // or readfile("$filepath/description.txt");
if(!empty($source))
echo "<pre class='p-3 bg-dark text-light' style='white-space: pre-line; font-family:consolas; font-size: 16pt;'>$source</pre>";

// close
$conn->close();

// buttons
$text = <<<EOT
<p>
<a href="javascript:history.back();" class="btn btn-outline-dark btn-lg"  role="button">
 <i class="far fa-file-alt"></i> %s
</a>
<a href="submit.php?id=$id" class="btn btn-outline-dark btn-lg"  role="button">
 <i class="fas fa-running"></i> %s
</a>
<a href="question-ask.php?id=$id" class="btn btn-outline-dark btn-lg" role="button">
 <i class="fas fa-question"></i> %s
</a>
</p>
EOT;
echo sprintf( $text,
	$lang["description"]["problems"],
	$lang["description"]["submit"],
	$lang["description"]["question"]
);

// footer
include("footer.php");

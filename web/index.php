<?php
// current page
$page = "contests";

// header
include("header.php");

// container
echo <<<EOT
<!-- container -->
<div class="container-fluid">
EOT;

// welcome
echo sprintf( "<h1>SPOJ</h1><p>%s</p>", $lang["index"]["slogan"] );

// table
$text = <<<EOT
<div class="row">
<div class="col-md-12">
<table class="table table-striped">
<thead>
<tr>
<th> %s </th>
<th> %s </th>
<th> %s </th>
<th> %s </th>
<th class="text-end"> %s </th>
</tr>
</thead>
<tbody>
EOT;
echo sprintf( $text, 
	$lang["index"]["id"],
	$lang["index"]["contest"],
	$lang["index"]["start"],
	$lang["index"]["duration"],
	$lang["index"]["action"]
);

// sql
$sql = "SELECT * FROM contests ORDER BY contest_id DESC";
$result = $conn->query($sql);

// execute
if ($result->num_rows > 0)
{

// output data of each row
while($row = $result->fetch_assoc())
{
$id = $row["contest_id"];
$name = $row["name"];
$duration = $row["duration"];
$date = (new DateTime($row["start_time"]))->format("d.m.Y H:i");
// format and print
$text = <<<EOT
<tr>
<td>$id</td>
<td>$name</td>
<td>$date</td>
<td>$duration</td>
<td class='text-end'>
 <a href='contest.php?id=$id' class='btn btn-lg btn-outline-dark' role='button'> 
  <i class="far fa-file-alt"></i> %s
 </a>
 <a href='board.php?id=$id' class='btn btn-lg btn-outline-dark' role='button'> 
  <i class="far fa-star"></i> %s
 </a>
</td>
</tr>
EOT;
echo sprintf( $text, $lang["index"]["problems"], $lang["index"]["board"]);

}}

// end table
echo "</tbody></table></div></div>";

// close
$conn->close();

// footer
include("footer.php");
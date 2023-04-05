<?php
// current page
$page = "submit";

// header
include("header.php");

// init
include("init.php");

// POST
if($_SERVER['REQUEST_METHOD']=='POST')
{
	// vars
	$problem_id = $conn->real_escape_string($_REQUEST["task"]);
	$user = $conn->real_escape_string($_REQUEST["user"]);
	$pass = md5($conn->real_escape_string($_REQUEST["password"]));
	$language = $conn->real_escape_string($_REQUEST["language"]);
	$code = $conn->real_escape_string($_REQUEST["code"]);
	$submit_time = date("Y-m-d H:i:s");

	// check for user
	$sql = "SELECT user_id FROM  spoj0.users WHERE name='$user' and pass_md5='$pass'";
	$result = $conn->query($sql);
        if ($result->num_rows == 0)
	die( sprintf( "<div class='card bg-danger text-light m-4 p-4'><div class='card-body'><h1 class='card-title'>%s</h1><p class='card-text'>%s</p></div></div>",
		$lang["submit"]["problem"],
		$lang["submit"]["info1"]
	));

	$row = $result->fetch_row();
	$user_id = $row[0];

	// prepare sql
	$sql = "INSERT INTO spoj0.runs (problem_id, user_id, submit_time, language, source_code, source_name, about, status, log) ".
	       "VALUES ('$problem_id','$user_id','$submit_time','$language','$code','program.$language','','waiting','')";

	// execute and message
	if($conn->query($sql))
	echo sprintf( "<div class='card bg-success text-light m-4 p-4'><div class='card-body'><h1 class='card-title'>%s</h1><p class='card-text'>%s</p></div></div>",
		$lang["submit"]["submit"],
		$lang["submit"]["info2"]."<b>$problem_id</b>"
	);
	else echo sprintf( "<div class='card bg-danger text-light m-4 p-4'><div class='card-body'><h1 class='card-title'>%s</h1><p class='card-text'>%s</p></div></div>",
		$lang["submit"]["submit"],
		$lang["submit"]["info3"]."<b>$problem_id</b>"
	);

	// close
	$conn->close();

} else {
// GET

// problem number
$pid = 0;
if(isset($_REQUEST["id"])) $pid = (int)$_REQUEST["id"];

// The Form
$text = <<<EOT
<h1>%s</h1>
<form class="form-horizontal" action="submit.php" method="POST">
<input type="hidden" name="task" id="task" value="$pid" />

<!-- task
<div class="mb-3">
  <label class="control-label" for="task">%s</label>
  <input id="task" name="task" type="text" placeholder="%s" class="form-control input-md" required="" value="$pid">
</div>
-->
<!-- user -->
<div class="mb-3">
  <label class="control-label" for="user">%s</label>
  <input id="user" name="user" type="text" placeholder="%s" class="form-control input-md" required="">
</div>
<!-- pass -->
<div class="mb-3">
  <label class="control-label" for="password">%s</label>
  <input id="password" name="password" type="password" placeholder="%s" class="form-control input-md" required="">
</div>
<!-- language -->
<div class="mb-3">
  <label class="control-label" for="language">%s</label>
  <select onchange="javascript:showDiv(this)" class="form-control" name="language" id="language" class="form-control input-md">
    <option selected value="cpp">C++</option>
    <option value="cs">C#</option>
    <option value="java">Java</option>
  </select>
  <div id="language_note" class="alert alert-info" style="margin-top:10px; display: none;">%s</div>
</div>
<!-- code -->
<div class="mb-3">
  <label class="control-label" for="code">%s</label>
  <textarea id="code" name="code" placeholder="%s" rows="10" class="form-control input-md" required=""></textarea>
</div>
<!-- Submit Button -->
<div class="mb-3">
  <label class="control-label" for="submit"></label>
  <button id="submit" name="submit" class="btn btn-dark">
   <i class="fas fa-paper-plane"></i> %s
  </button>
</div>

</form>
<!-- Show/Hide Java Language Note -->
<script type="text/javascript">
function showDiv(item) {
if(item.value == "java") document.getElementById('language_note').style.display = "block";
else document.getElementById('language_note').style.display = "none";
}
</script>
EOT;
echo sprintf( $text,  
	$lang["submit"]["submit"], 	
	$lang["submit"]["task_id"],
	$lang["submit"]["task_sample"],
	$lang["submit"]["user"], 
	$lang["submit"]["user_sample"],
	$lang["submit"]["password"], 
	$lang["submit"]["password_sample"],
	$lang["submit"]["language"], 
	$lang["submit"]["language_note"],
	$lang["submit"]["code"], 
	$lang["submit"]["code_note"],
	$lang["submit"]["submit_btn"] 
);

}
// END GET

// footer
include("footer.php");

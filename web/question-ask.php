<?php
// current page
$page = "ask";

// header
include("header.php");

// POST
if($_SERVER['REQUEST_METHOD']=='POST')
{
	// vars
	$problem_id = $conn->real_escape_string(htmlspecialchars($_REQUEST["task"]));
	$user = $conn->real_escape_string(htmlspecialchars($_REQUEST["user"]));
	$pass = md5($conn->real_escape_string($_REQUEST["password"]));
	$question = $conn->real_escape_string(htmlspecialchars($_REQUEST["question"]));
	$submit_time = date("Y-m-d H:i:s");

	// check for user
	$sql = "SELECT user_id FROM  spoj0.users WHERE name='$user' and pass_md5='$pass'";
	$result = $conn->query($sql);
        if ($result->num_rows == 0) 
	die( sprintf("<div class='card bg-danger text-light m-4 p-4'><div class='card-body'><h1 class='card-title'>%s</h1><p class='card-text'>%s</p></div></div>",
		$lang["ask"]["problem"],
		$lang["ask"]["info1"]
	));
	$row = $result->fetch_row();
	$user_id = $row[0];

	// prepare sql
	$sql = "INSERT INTO spoj0.questions (problem_id, user_id, question_time, content, status, answer_time, answer_content) VALUES ('$problem_id','$user_id','$submit_time','$question','not answered','$submit_time','')";

	// execute and message
	if($conn->query($sql)) 
	echo sprintf("<div class='card bg-success text-light m-4 p-4'><div class='card-body'><h1 class='card-title'>%s</h1><p class='card-text'>%s</p></div></div>",
		$lang["ask"]["question"],
		$lang["ask"]["info2"]."<b>$problem_id</b>"
	);
	else die( sprintf("<div class='card bg-danger text-light m-4 p-4'><div class='card-body'><h1 class='card-title'>%s</h1><p class='card-text'>%s</p></div></div>",
		$lang["ask"]["problem"],
		$lang["ask"]["info3"]
	));

	// close
	$conn->close();


} else {
// GET

// problem number
$pid = "";
if(isset($_REQUEST["id"])) $pid = (int)$_REQUEST["id"];


// The Form
$text = <<<EOT
<h1>%s</h1>
<form class="form-horizontal" action="question-ask.php" method="POST">

<!-- task -->
<div class="mb-3">
  <label class="control-label" for="task">%s</label>
  <input id="task" name="task" type="text" placeholder="%s" class="form-control" required="" value="$pid">
</div>

<!-- user -->
<div class="mb-3">
  <label class="control-label" for="user">%s</label>
  <input id="user" name="user" type="text" placeholder="%s" class="form-control" required="">
</div>

<!-- pass -->
<div class="mb-3">
  <label class="control-label" for="password">%s</label>
  <input id="password" name="password" type="password" placeholder="%s" class="form-control" required="">
</div>

<!-- content -->
<div class="mb-3">
  <label class="control-label" for="question">%s</label>
  <textarea id="question" name="question" placeholder="%s" rows="10" class="form-control" required=""></textarea>
</div>

<!-- Submit Button -->
<div class="mb-3">
  <label class="control-label" for="submit"></label>
  <button id="submit" name="submit" class="btn btn-lg btn-outline-dark"> 
   <i class="fas fa-paper-plane"></i> %s 
  </button>
</div>

</form>
EOT;
echo sprintf( $text, 
	$lang["ask"]["question"],
	$lang["ask"]["task_id"],
	$lang["ask"]["task_sample"],
	$lang["ask"]["user"],
	$lang["ask"]["user_sample"],
	$lang["ask"]["password"],
	$lang["ask"]["password_sample"],
	$lang["ask"]["question"],
	$lang["ask"]["question_note"],
	$lang["ask"]["submit_btn"]
);

}
// END GET

// footer
include("footer.php");

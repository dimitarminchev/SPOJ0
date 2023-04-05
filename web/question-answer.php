<?php
// current page
$page = "answer";

// header
include("header.php");

// question number
$qid = "";
if(isset($_REQUEST["id"])) $qid = (int)$_REQUEST["id"];

// POST
if($_SERVER['REQUEST_METHOD']=='POST')
{
	// vars
	$problem_id = $conn->real_escape_string(htmlspecialchars($_REQUEST["task"]));
	$user = $conn->real_escape_string(htmlspecialchars($_REQUEST["user"]));
	$pass = md5($conn->real_escape_string(($_REQUEST["password"])));
	$answer = $conn->real_escape_string(htmlspecialchars($_REQUEST["answer"]));
	$submit_time = date("Y-m-d H:i:s");

	// check for user -----
	$sql = "SELECT user_id FROM spoj0.users WHERE name='$user' and pass_md5='$pass'";
	$result = $conn->query($sql);
    if ($result->num_rows == 0) 
	die( sprintf("<div class='card bg-danger text-light m-4 p-4'><div class='card-body'><h1 class='card-title'>%s</h1><p class='card-text'>%s</p></div></div>",
		$lang["answer"]["problem"],
		$lang["answer"]["info1"]
	));
	$row = $result->fetch_row();
	$user_id = $row[0];
	
	if ($user_id != 1) 
	die( sprintf("<div class='card bg-danger text-light m-4 p-4'><div class='card-body'><h1 class='card-title'>%s</h1><p class='card-text'>%s</p></div></div>",
		$lang["answer"]["problem"],
		$lang["answer"]["info2"]
	));

	// prepare sql
	$sql = "update spoj0.questions SET status = 'answered', answer_content='$answer', answer_time='$submit_time' where question_id=$qid";
	
	// execute and message
	if($conn->query($sql)) 
	echo sprintf("<div class='card bg-success text-light m-4 p-4'><div class='card-body'><h1 class='card-title'>%s</h1><p class='card-text'>%s</p></div></div>",
		$lang["answer"]["answer"],
		$lang["answer"]["info3"]."<b>$problem_id</b>"
	);
	else die( sprintf("<div class='card bg-danger text-light m-4 p-4'><div class='card-body'><h1 class='card-title'>%s</h1><p class='card-text'>%s</p></div></div>",
		$lang["answer"]["problem"],
		$lang["answer"]["info4"]
	));

	// close
	$conn->close();

} else {
// GET

// mysql
$sql = "SELECT content FROM questions WHERE question_id=$qid";
$result = $conn->query($sql);
if ($result->num_rows == 0) 
die( sprintf("<div class='card bg-success text-light m-4 p-4'><div class='card-body'><h1 class='card-title'>%s</h1><p class='card-text'>%s</p></div></div>",
     $lang["answer"]["problem"],
     $lang["answer"]["info5"]
));
$row = $result->fetch_row();
$content = $row[0];

// The Form
$text = <<<EOT
<h1>%s</h1>
<form class="form-horizontal" action="question-answer.php?id=$qid" method="POST">

<!-- question -->
<div class="mb-3">
  <label class="control-label" for="user">%s</label>
  <textarea id="answer" name="answer" rows="10" class="form-control" readonly> $content </textarea>
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
  <label class="control-label" for="answer">%s</label>
  <textarea id="answer" name="answer" placeholder="%s" rows="10" class="form-control" required=""></textarea>
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
	$lang["answer"]["answer"],
	$lang["answer"]["question"],
	$lang["answer"]["user"],
	$lang["answer"]["user_sample"],
	$lang["answer"]["password"],
	$lang["answer"]["password_sample"],
	$lang["answer"]["answer"],
	$lang["answer"]["answer_note"],
	$lang["answer"]["submit_btn"]
);

}
// END GET

// close
$conn->close();

// footer
include("footer.php");

<?php
// current page
$page = "register";

// header
include("header.php");

// init
include("init.php");

// POST
if($_SERVER['REQUEST_METHOD']=='POST')
{
	// data
	$user = $conn->real_escape_string($_REQUEST["user"]);
	$pass = md5($conn->real_escape_string($_REQUEST["password"]));
	$name = $conn->real_escape_string($_REQUEST["name"]);
	$email = "email:".$conn->real_escape_string($_REQUEST["email"]);

	// sql and execute
	$sql = "INSERT INTO spoj0.users (name, pass_md5, display_name, about) VALUES ('$user','$pass','$name','$email')";
	$result = $conn->query($sql); 

	// message
	if($result) 
        echo sprintf( "<div class='card bg-success text-light m-4 p-4'><div class='card-body'><h1 class='card-title'>%s</h1><p class='card-text'>%s</p></div></div>",
                      $lang["register"]["register"], $lang["register"]["info1"] );
	else
        echo sprintf( "<div class='card bg-danger text-light m-4 p-4'><div class='card-body'><h1 class='card-title'>%s</h1><p class='card-text'>%s</p></div></div>",
                      $lang["register"]["problem"], $lang["register"]["info2"] );
	
	// close
	$conn->close();

} else {

// GET
$text = <<< EOT
<!-- header -->
<h1> %s </h1>
<p> %s </p>

<!-- form -->
<form class="form-horizontal" action="register.php" method="POST">

<!-- name -->
<div class="mb-3">
  <label class="control-label" for="name">%s</label>
  <input id="name" name="name" type="text" placeholder="%s" class="form-control" required="" />
</div>
<!-- email -->
<div class="mb-3">
  <label class="control-label" for="email">%s</label>
  <input id="email" name="email" type="text" placeholder="%s" class="form-control" required="" />
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
<!-- Google Recapcha -->
<div class="mb-3">
 <label class="control-label" for="recapcha"></label>
 <div class="g-recaptcha" data-sitekey="%s"></div>
</div>

<!-- Submit Button -->
<div class="mb-3">
  <label class="control-label" for="submit"></label>
  <button id="submit" name="submit" class="btn btn-outline-dark">
   <i class="fas fa-paper-plane"></i> %s
  </button>
</div>

</form>
EOT;
echo sprintf( $text,
	$lang["register"]["register"], 
	$lang["register"]["register_info"],
	$lang["register"]["name"],
	$lang["register"]["name_note"],
	$lang["register"]["email"],
	$lang["register"]["email_note"],
	$lang["register"]["user"], 
	$lang["register"]["usern_note"], 
	$lang["register"]["password"], 
	$lang["register"]["password_note"], 
	$RECAPTCHA_KEY,
	$lang["register"]["submit"]
);

}
// END GET

// footer
include("footer.php");

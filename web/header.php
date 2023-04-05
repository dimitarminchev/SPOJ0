<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="author" content="Dimitar Minchev">
<title>SPOJ</title>

<!-- jquery 3.6.0 -->
<!-- 
<script src="https://code.jquery.com/jquery-3.6.0.min.js"
		integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
-->
<script src="assets/jquery/jquery-3.6.0.min.js"></script> 

<!-- bootstrap 5.1.3 -->
<!--
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
		integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" 
		integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
-->
<link  href="assets/bootstrap/bootstrap.min.css" rel="stylesheet" />
<script src="assets/bootstrap/bootstrap.bundle.min.js"></script> 

<!-- fontawesome 5.15.4 -->
<!--
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" 
		integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" 
		crossorigin="anonymous" referrerpolicy="no-referrer" />
 -->
<link href="assets/fonts/fontawesome-5.15.4.css" rel="stylesheet" />

<!-- google fonts -->
<link rel="preconnect" href="https://fonts.gstatic.com">
<link href="https://fonts.googleapis.com/css2?family=Raleway&display=swap" rel="stylesheet">
<style>
body {
  font-family: 'Raleway', sans-serif;
  font-size: 16px;
  font-weight: 300;
  line-height: 1.6em;
  position: relative;
}
.success {
   background-color: #C8E6C9 !important;
}
.danger {
   background-color: #FFCDD2 !important;
}
.warning {
   background-color: #FFF9C4 !important;
}
</style>

<!-- google recapcha api -->
<script src="https://www.google.com/recaptcha/api.js"></script>

</head>
<body>
<?php
// Session Start
session_start();

// Initialization
include("init.php");

// Select Language
$language = "bulgarian"; // default language
if(isset($_REQUEST["lang"])) {
if($_REQUEST["lang"] == "en") $_SESSION["spoj0"]["lang"] = "english";
else $_SESSION["spoj0"]["lang"] = "bulgarian";
}
if(isset($_SESSION["spoj0"]["lang"])) $language = $_SESSION["spoj0"]["lang"];

// Loading Language Settings
$lang = parse_ini_file("$language.ini",true);

// Create Language Url
$url = parse_url($_SERVER["REQUEST_URI"]);
$query = $url['query'];
parse_str($query, $params);
// unset($params['lang']);
$params['lang'] = "bg";
$url_bg = $url['path']."?".http_build_query($params);
$params['lang'] = "en";
$url_en = $url['path']."?".http_build_query($params);
?>

<!-- navigation -->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php"> SPOJ </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar"
            aria-controls="navbar" aria-expanded="false" aria-label="<?php echo $lang["nav"]["nav"]; ?>">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbar">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">

            <!-- news -->
            <li class="nav-item <?php if($page=="news") echo 'active'; ?>">
               <a class="nav-link" href="news.php">
                  <?php echo $lang["nav"]["news"]; ?>
               </a>
            </li>

            <!-- register -->
            <li class="nav-item <?php if($page=="register") echo 'active'; ?>">
               <a class="nav-link" href="register.php">
                  <?php echo $lang["nav"]["register"]; ?>
               </a>
            </li>

            <!-- contests -->
            <li class="nav-item <?php if($page=="contests") echo 'active'; ?>">
               <a class="nav-link" href="index.php">
                  <?php echo $lang["nav"]["contests"]; ?>
               </a>
            </li>

            <!-- submit
            <li class="nav-item <?php if($page=="submit") echo 'active'; ?>">
               <a class="nav-link" href="submit.php">
                  <?php echo $lang["nav"]["submit"]; ?>
               </a>
            </li>
            -->

            <!-- status -->
            <li class="nav-item <?php if($page=="status") echo 'active'; ?>">
               <a class="nav-link" href="status.php">
                  <?php echo $lang["nav"]["status"]; ?>
               </a>
            </li>

            <!-- questions -->
            <li class="nav-item <?php if($page=="questions") echo 'active'; ?>">
               <a class="nav-link" href="questions.php">
                  <?php echo $lang["nav"]["questions"]; ?>
               </a>
            </li>

      </ul>

     <!-- Languages, Date & Time -->
     <form class="d-flex">
         <a class="btn <?php if($language=="bulgarian") echo 'btn-outline-dark'; ?>" href="<?php echo $url_bg; ?>"><img src="assets/bg.png" width="25px" /> Български</a>
         <a class="btn <?php if($language=="english") echo 'btn-outline-dark'; ?>" href="<?php echo $url_en; ?>"><img src="assets/uk.png" width="25px" /> English</a>
         <div class="btn text-muted"><?php echo date("d.m.y H:i:s"); ?></div>
      </form>

      </div>
  </div>
</nav>
<!-- /navigation -->

<!-- container -->
<div class="container-fluid">

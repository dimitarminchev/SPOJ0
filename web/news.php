<?php
// current page
$page = "news";

// header
include("header.php");

// title
echo sprintf( "<h1>%s</h1>", $lang["nav"]["news"] );

// query
$result = $conn->query("SELECT * FROM news ORDER BY new_id DESC");

// execute
if ($result->num_rows > 0)

// print
while($row = $result->fetch_assoc()) {
$text=<<<EOT
<div class="card mb-3">
  <div class="card-body">
    <h3 class="card-title"> %s </h3>
    <p class="card-text">
      <h5>%s</h5>
      <i>%s</i>
    </p>
  </div>
</div>
EOT;
echo sprintf( $text, $row["topic"], $row["content"], (new DateTime($row["new_time"]))->format("d.m.Y H:i:s"));
}

// close
$conn->close();

// footer
include("footer.php");

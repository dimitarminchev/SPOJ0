<?php
// refresh
header("refresh: 5;");

// current page
$page = "questions";

// header
include("header.php");

// title
echo sprintf( "<h1>%s</h1>", $lang["questions"]["questions"] );

// var
$limit = 200;
$order = " ORDER BY q.question_id desc LIMIT $limit";

// sql
$sql =<<<EOT
SELECT
q.question_id,
q.user_id,
q.problem_id,
u.display_name as uname,
u.hidden as hidden,
c.set_code as ccode,
p.letter as pletter,
c.contest_id,
q.question_time,
q.content,
q.status,
q.answer_time,
q.answer_content
FROM questions as q
INNER JOIN users as u ON q.user_id = u.user_id
INNER JOIN problems as p ON q.problem_id = p.problem_id
INNER JOIN contests as c ON p.contest_id = c.contest_id
EOT;

// check
if(isset($_REQUEST["id"])) {
$qid = (int)$_REQUEST["id"];


// Part 1. single question status
$sql .= " WHERE q.question_id=".(int)$_REQUEST["id"].$order;

// execute
$result = $conn->query($sql);
if ($result->num_rows == 0)
die( sprintf("<div class='jumbotron alert-danger'><h1> %s </h1><p> %s.<p></div>",
        $lang["questions"]["problem"],
        $lang["questions"]["info1"]
));	
$row = $result->fetch_assoc();

// status 
$status = strtoupper($row["status"]);
if($status == "ANSWERED") $status = "label label-success";
else $status = "label label-warning";

// table row
$text = <<<EOT
<div class="row">

<!-- 1 -->
<div class="col-md-6 mt-2">
 <div class="card">
  <div class="card-body">
   <p class="card-text"> %s </p>
   <h3 class="card-title"> %s </h3>
  </div>
 </div>
</div>

<!-- 2 -->
<div class="col-md-6 mt-2">
 <div class="card">
  <div class="card-body">
   <p class="card-text"> %s </p>
   <h3 class="card-title"> %s </h3>
  </div>
 </div>
</div>

<!-- 3 -->
<div class="col-md-6 mt-2">
 <div class="card">
  <div class="card-body">
   <p class="card-text"> %s </p>
   <h3 class="card-title"> %s </h3>
  </div>
 </div>
</div>
<!-- 4 -->
<div class="col-md-6 mt-2">
 <div class="card">
  <div class="card-body">
   <p class="card-text"> %s </p>
   <h3 class="card-title"> <span class="$status">%s</span> </h3>
  </div>
 </div>
</div>

<!-- 5 -->
<div class="col-md-6 mt-2">
 <div class="card">
  <div class="card-body">
   <p class="card-text"> %s </p>
   <h3 class="card-title"> %s </h3>
  </div>
 </div>
</div>

<!-- 6 -->
<div class="col-md-6 mt-2">
 <div class="card">
  <div class="card-body">
   <p class="card-text"> %s </p>
   <h3 class="card-title"> %s </h3>
  </div>
 </div>
</div>

</div>
<!-- /end -->
EOT;
echo sprintf( $text,
	$lang["questions"]["contest"], $row["ccode"], // 1. contest
	$lang["questions"]["problem"], strtoupper($row["pletter"]), // 2. problem	
	$lang["questions"]["user"], $row["uname"], // 3. user
	$lang["questions"]["status"], strtoupper($row["status"]), // 4. status	
	$lang["questions"]["question"], $row["content"], // 5. question
	$lang["questions"]["date"], (new DateTime($row["question_time"]))->format("d.m.y H:i:s") // 6. date
);

// ANSWERED
if(strtoupper($row["status"])=="ANSWERED") 
{
$text = <<<EOT
<div class="row">

<!-- 7 -->
<div class="col-md-6 mt-2">
 <div class="card">
  <div class="card-body">
   <p class="card-text"> %s </p>
   <h3 class="card-title"> %s </h3>
  </div>
 </div>
</div>

<!-- 8 -->
<div class="col-md-6 mt-2">
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
	$lang["questions"]["answer"], $row["answer_content"], // 7. answer
	$lang["questions"]["date"], (new DateTime($row["answer_time"]))->format("d.m.y H:i:s") // 8. date
);
} 
else echo sprintf("<a href='question-answer.php?id=$qid' class='btn btn-outline-dark' role='button'>%s</a>", $lang["questions"]["answer"]);

} else {
// 2. multiple runs status

// table
$text = <<<EOT
<div class="row">
<div class="col-md-12">
<table class="table table-striped">
<thead>
<tr>
<th>%s</th>
<th>%s</th>
<th>%s</th>
<th>%s</th>
<th>%s</th>
<th>%s</th>
<th class="text-end">%s</th>
</tr>
</thead>
<tbody>
EOT;
echo sprintf( $text,
        $lang["questions"]["number"],
        $lang["questions"]["user"],
        $lang["questions"]["contest"],
        $lang["questions"]["problem"],
        $lang["questions"]["date"],
        $lang["questions"]["status"],
        $lang["questions"]["action"]
);

// execute
$sql .= $order;
$result = $conn->query($sql);

// process
if ($result->num_rows > 0)
// output data of each row
while($row = $result->fetch_assoc()) 
{
// status
$stat = strtoupper($row["status"]);
if($stat == "ANSWERED") $stat = "badge bg-success";
else $stat = "badge bg-warning";

// table row
$text = <<<EOT
<tr>
<td>%s</td>
<td>%s</td>
<td>%s</td>
<td>%s <span class='badge bg-dark'>%s</span></td>
<td>%s</td>
<td><span class='$stat'>%s</span></td>
<td class='text-end'>
 <a href='questions.php?id=%s' class='btn btn-outline-dark' role='button'>
  <i class="fas fa-question"></i> %s
 </a>
</td>
</tr>
EOT;
echo sprintf( $text,
	$row["question_id"], // id
	$row["uname"], $row["ccode"], // user
	strtoupper($row["pletter"]), $row["problem_id"], // problem
	(new DateTime($row["question_time"]))->format("d.m.y H:i:s"), // date
	strtoupper($row["status"]), // status
	$row["question_id"], // id
	$lang["questions"]["info"] // info
);
}

// end table
echo "</tbody></table></div></div>";

// note
echo sprintf( "<p>%s <b>$limit</b></p>", $lang["status"]["note"]  );
}

// close
$conn->close();

// footer
include("footer.php");

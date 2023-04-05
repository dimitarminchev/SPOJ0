<?php
// current page
$page = "status";

// header
include("header.php");

// init
include("init.php");

// title
echo sprintf( "<h1>%s</h1>", $lang["status"]["status"] );

// sql
$sql =<<<EOT
SELECT
r.run_id,
r.user_id,
r.problem_id,
u.display_name as uname,
c.set_code as ccode,
p.letter as pletter,
c.contest_id,
r.submit_time,
r.language,
r.status,
c.show_sources
FROM runs as r
INNER JOIN users as u ON r.user_id = u.user_id
INNER JOIN problems as p ON r.problem_id = p.problem_id
INNER JOIN contests as c ON p.contest_id = c.contest_id
EOT;


// check
if(isset($_REQUEST["id"])) {


	// Part 1. single run status
	$sql .= " WHERE r.run_id=".(int)$_REQUEST["id"]." ORDER BY r.run_id desc LIMIT $SQL_LIMIT";
	
	// execute
	$result = $conn->query($sql);
	if ($result->num_rows == 0)
	die( sprintf( "<div class='card bg-danger text-light m-4 p-4'><div class='card-body'><h1 class='card-title'>%s</h1><p class='card-text'>%s</p></div></div>",
		$lang["status"]["problem"],
		$lang["status"]["info1"]
	));
	
	// row
	$row = $result->fetch_assoc();
	
	// data
	$contest = $row["ccode"]."&nbsp;<span class='badge bg-dark'>".$row["contest_id"]."</span>";
	$problem = strtoupper($row["pletter"])."&nbsp;<span class='badge bg-dark'>".$row["problem_id"]."</span>";
	$user = $row["uname"]."&nbsp;<span class='badge bg-dark'>".$row["user_id"]."</span>";
	$date = (new DateTime($row["submit_time"]))->format("d.m.y H:i:s");
	$language = $row["language"];
	if($row["show_sources"] == "1") 
	$language.="&nbsp;<a target='_blank' class='btn btn-outline-dark' href='code.php?id=".(int)$_REQUEST["id"]."'><i class='fas fa-download'></i> Download</a>";
	$stat = strtoupper($row["status"]);
	switch($stat)
	{
		case "OK": $stat = "<span class='badge bg-success'>$stat</span>"; break;
		case "WA": case "PE": $stat = "<span class='badge bg-warning'>$stat</span>"; break;
		default:  $stat = "<span class='badge bg-danger'>$stat</span>"; break;
	}
	
	// print
	$text = <<<EOT
	<div class="row">
	
	<!-- 1 -->
	<div class="col-md-6 mt-2">
	  <div class="card">
		<div class="card-body">
		  <p class="card-text"> %s </p>
		  <h3 class="card-title">$contest</h3>
		</div>
	  </div>
	</div>
	
	<!-- 2 -->
	<div class="col-md-6 mt-2">
	  <div class="card">
		<div class="card-body">
		  <p class="card-text"> %s </p>
		  <h3 class="card-title">$problem</h3>
		</div>
	  </div>
	</div>
	
	</div>
	<div class="row">
	
	<!-- 3 -->
	<div class="col-md-6 mt-2">
	  <div class="card">
		<div class="card-body">
		  <p class="card-text"> %s </p>
		  <h3 class="card-title">$user</h3>
		</div>
	  </div>
	</div>
	
	<!-- 4 -->
	<div class="col-md-6 mt-2">
	  <div class="card">
		<div class="card-body">
		  <p class="card-text"> %s </p>
		  <h3 class="card-title">$date</h3>
		</div>
	  </div>
	</div>
	
	</div>
	<div class="row">
	
	<!-- 5 -->
	<div class="col-md-6 mt-2">
	  <div class="card">
		<div class="card-body">
		  <p class="card-text"> %s </p>
		  <h3 class="card-title">$language</h3>
		</div>
	  </div>
	</div>
	
	<!-- 6 -->
	<div class="col-md-6 mt-2">
	  <div class="card">
		<div class="card-body">
		  <p class="card-text"> %s </p>
		  <h3 class="card-title">$stat</h3>
		</div>
	  </div>
	</div>
	
	</div>
	<!-- /end -->
	EOT;
	echo sprintf( $text,  
		$lang["status"]["contest"], 	
		$lang["status"]["task"],
		$lang["status"]["user"], 	
		$lang["status"]["date"],	
		$lang["status"]["lang"], 
		$lang["status"]["stat"] 
	);
	
	
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
	<th>%s</th>
	<th class="text-end">%s</th>
	</tr>
	</thead>
	<tbody>
	EOT;
	echo sprintf( $text,  
		$lang["status"]["number"], 
		$lang["status"]["user"], 
		$lang["status"]["contest"], 	
		$lang["status"]["task"],		
		$lang["status"]["date"],	
		$lang["status"]["lang"], 
		$lang["status"]["score"], 
		$lang["status"]["action"] 
	);
	
	// execute
	$sql .= " ORDER BY r.run_id desc LIMIT $SQL_LIMIT";
	$result = $conn->query($sql);
	
	// process
	if ($result->num_rows > 0) {
	
	// output data of each row
	while($row = $result->fetch_assoc()) {
	
	// rid
	$rid = $row["run_id"];
	
	// status
	$stat = strtoupper($row["status"]);
	switch($stat)
	{
		case "OK": $stat = "<span class='badge bg-success'>$stat</span>"; break;
		case "WA": case "PE": $stat = "<span class='badge bg-warning'>$stat</span>"; break;
		default: $stat = "<span class='badge bg-danger'>$stat</span>"; break;
	}
	
	// table row
	$text = <<<EOT
	<tr>
	<td>$rid</td>
	<td>%s <span class='badge bg-dark'>%s</span></td>
	<td>%s <span class='badge bg-dark'>%s</span></td>
	<td>%s <span class='badge bg-dark'>%s</span></td>
	<td>%s</td>
	<td>%s</td>
	<td>$stat</td>
	<td class='text-end'>
	 <a href='status.php?id=$rid' class='btn btn-outline-dark' role='button'>
	  <i class="fas fa-info-circle"></i> %s
	 </a>
	</td>
	</tr>
	EOT;
	echo sprintf( $text,  	
		$row["uname"], $row["user_id"], // user
		$row["ccode"], $row["contest_id"], // contest
		strtoupper($row["pletter"]), $row["problem_id"], // problem
		(new DateTime($row["submit_time"]))->format("d.m.y H:i:s"), // date
		$row["language"], // language
		$lang["status"]["info"] 
	);
		
	}}
	
	// end table
	echo "</tbody></table></div></div>";
	
	// note
	echo sprintf( "<p>%s <b>$SQL_LIMIT</b></p>", $lang["status"]["note"]  );
	
	}
	
	// info
	$text = <<<EOT
	<p class="mt-2"><u>%s</u>:&nbsp;
	<span class='badge bg-success'>OK</span> = %s,&nbsp;
	<span class='badge bg-warning'>WA</span> = %s,&nbsp;
	<span class='badge bg-warning'>PE</span> = %s,&nbsp;
	<span class='badge bg-danger'>RE</span> = %s,&nbsp;
	<span class='badge bg-danger'>CE</span> = %s,&nbsp;
	<span class='badge bg-danger'>TL</span> = %s.</p>
	EOT;
	echo sprintf( $text,
		$lang["status"]["legend"],
		$lang["status"]["OK"],
		$lang["status"]["WA"],
		$lang["status"]["PE"],
		$lang["status"]["RE"],
		$lang["status"]["CE"],
		$lang["status"]["TL"]
	);
	
	// close
	$conn->close();
	
	// footer
	include("footer.php");
	

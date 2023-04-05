<?php
// refresh
header("refresh: 5;");

// current page
$page = "contests";

// header
include("header.php");

// contest number
if(!isset($_REQUEST["id"]))
die( sprintf("<div class='card bg-danger text-light m-4 p-4'><div class='card-body'><h1 class='card-title'>%s</h1><p class='card-text'>%s</p></div></div>",
	$lang["board"]["problem"],
	$lang["board"]["info1"]
));
$cid = (int)$_REQUEST["id"];

// contest info
$sql = "SELECT * FROM contests WHERE contest_id=$cid";
$result = $conn->query($sql);
if ($result->num_rows == 0)
die( sprintf("<div class='card bg-danger text-light m-4 p-4'><div class='card-body'><h1 class='card-title'>%s</h1><p class='card-text'>%s</p></div></div>",
	$lang["board"]["problem"],
	$lang["board"]["info2"]
));
$row = $result->fetch_assoc();
$name = $row["name"];
$start = new DateTime($row["start_time"]);
$start = $start->format("d.m.Y H:i");
$duration = $row["duration"]; 

// header
$text = <<<EOT
<h1>%s</h1>
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
   <h3 class="card-title"> $start </h3>
  </div>
 </div>
</div>

<!-- 3 -->
<div class="col-md-4 mt-2">
 <div class="card">
  <div class="card-body">
   <p class="card-text"> %s </p>
   <h3 class="card-title"> $duration </h3>
  </div>
 </div>
</div>

</div>
EOT;
echo sprintf( $text,  	
	$lang["board"]["board"],
	$lang["board"]["contest"],
	$lang["board"]["start"],
	$lang["board"]["duration"]
);

// sql
$sql =<<<EOT
SELECT
r.run_id,
u.display_name as u_name,
u.user_id,
u.hidden as u_hidden,
p.letter as p_letter,
p.problem_id,
c.contest_id,
UNIX_TIMESTAMP(r.submit_time) as s_time,
UNIX_TIMESTAMP(c.start_time) as c_time,
c.duration,
r.status
FROM runs as r
INNER JOIN users as u ON r.user_id = u.user_id
INNER JOIN problems as p ON r.problem_id = p.problem_id
INNER JOIN contests as c ON p.contest_id = c.contest_id
HAVING contest_id = $cid
ORDER BY r.run_id
EOT;
// execute
$result = $conn->query($sql);


// generate ratings in json format
$rate = array();
while($row = $result->fetch_assoc())
{
	// id, time & letter
    $id = $row["user_id"];
    $time = floor(($row["s_time"] - $row["c_time"]) / 60); // NEW
	$letter = strtoupper($row["p_letter"]);

	// skip solutions after the contest
	if($time > $row["duration"]) continue;

	// user, submits & run
    $rate[$id]["username"] = $row["u_name"];
    $rate[$id]["submits"] = $rate[$id]["submits"] + 1;
    $rate[$id]["runs"][$letter] = $rate[$id]["runs"][$letter] + 1;

    // times
	if(!isset($rate[$id]["answers"][$letter]) || $rate[$id]["answers"][$letter] == 0)
    if($row["status"] == "ok")
	{
		$rate[$id]["times"][$letter] = $time;
	}
    else
	{
		$rate[$id]["times"][$letter] = 0;
	}

	// answers, solved & totaltime
	if(isset($rate[$id]["times"][$letter]) && $rate[$id]["times"][$letter] > 0)
	{
		if($row["status"] == "ok" && $rate[$id]["answers"][$letter] == 0)
		{
			$rate[$id]["solved"] = $rate[$id]["solved"] + 1;
			$time = $time + ($rate[$id]["runs"][$letter]-1) * 20;
            $rate[$id]["time"] = $rate[$id]["time"] + $time;
		}
		$rate[$id]["answers"][$letter] = 1;
	}
	else
	{
		$rate[$id]["answers"][$letter] = 0;
	}
}

// reindex
$rate = array_values($rate);
//print_r($rate);

// bubble sort
for($i=0;$i<count($rate);$i++)
for($j=0;$j<count($rate);$j++)
if($rate[$i]["solved"] >=  $rate[$j]["solved"])
{
	if($rate[$i]["solved"] ==  $rate[$j]["solved"])
	{
		// order by: time
		if($rate[$i]["time"] < $rate[$j]["time"])
		{
			$temp = $rate[$i];
			$rate[$i] = $rate[$j];
			$rate[$j] = $temp;
		}
	}
	else
	{
		// order by: solved
		$temp = $rate[$i];
		$rate[$i] = $rate[$j];
		$rate[$j] = $temp;
	}
}

// json
// $json = json_encode($rate);
// print_r($json);

// tasks counter
$res = $conn->query("SELECT count(problem_id) FROM spoj0.problems WHERE contest_id=$cid");
$res = $res->fetch_row();
$taskscounter = $res[0];

// table
$text = "<div class='row'><div class='col-md-12'><table class='table table-striped'><thead><tr><th>#</th><th>%s</th><th>%s</th><th>%s</th>";
for($i=0;$i<$taskscounter;$i++) $text .= "<th>".chr($i+65)."</th>";
$text .= "<th class='text-end'>%s</th></tr></thead><tbody>";
echo sprintf( $text,  	
	$lang["board"]["user"],
	$lang["board"]["solved"],
	$lang["board"]["time"],
	$lang["board"]["submits"]
);

// process table
for ($i=0;$i<count($rate);$i++)
{
	// data
	$no = $i+1;
	$user = $rate[$i]["username"];
	$solved = $rate[$i]["solved"];
	$time = floor($rate[$i]["time"]);
	$submits = $rate[$i]["submits"];

	// forming tasks and colors
	$tasks = "";
	for($j=0;$j<$taskscounter;$j++)
	{
		if(isset($rate[$i]["answers"][chr($j+65)]))
		{
			$t = $rate[$i]["times"][chr($j+65)];
			$r = $rate[$i]["runs"][chr($j+65)];
			if($rate[$i]["answers"][chr($j+65)]==1)
			$tasks .= "<td class='success'>$t <span class='badge bg-success'>$r</span></td>";
			else
			$tasks .= "<td class='danger'>$t <span class='badge bg-danger'>$r</span></td>";
		} else $tasks .= "<td>0</td>";
	}
	// print single row
	echo "<tr><td>$no</td><td>$user</td><td>$solved</td><td>$time</td>$tasks<td class='text-end'>$submits</td>";
}

// end table
echo "</tbody></table></div></div>";

// close
$conn->close();

// final
echo sprintf( "<a href='board-offline.php?id=$cid' class='btn btn-lg btn-outline-dark' role='button'><i class='fas fa-clock'></i> %s</a></td>", $lang["board"]["offline"]);

// footer
include("footer.php");

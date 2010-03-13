#!/usr/bin/perl

use strict;
use DBI;
use spoj0;

# should be invoked with a signle agrument - the run_id
# does not check the status, so may be used to redjudge


#close STDOUT;
#open STDOUT, '>$EXEC_DIR/grade.log';
#close STDERR;
#open STERR, '>$EXEC_DIR/grade.err';

#limits a string to a reasonable amount...
#the amount is 2048 
sub Limit{
	my $in = shift or "";
	my $data = $in;
	my $MAX = 2048;
	my $len = length($data);
	warn $len;
	if($len > $MAX){
		$data = substr($data, 0, $MAX) . "\n  --truncated from $len\n";
	}
	return $data;
}


my $run_id = shift or die;


my $dbh = SqlConnect;

my $run_st = $dbh->prepare("SELECT * FROM runs WHERE run_id=?");
$run_st->bind_param(1, $run_id);
$run_st->execute() or die "Unable to execute statment : $!";
my $run = $run_st->fetchrow_hashref;
$run_st->finish;

my $problem_st = $dbh->prepare(	"SELECT * from problems where problem_id=?");
$problem_st->bind_param(1, $$run{'problem_id'});
$problem_st->execute() or die "Unable to execute statment : $!";
my $problem = $problem_st->fetchrow_hashref;
$problem_st->finish;

my $contest_st = $dbh->prepare(	"SELECT * from contests where contest_id=?");
$contest_st->bind_param(1, $$problem{'contest_id'});
$contest_st->execute() or die "Unable to execute statment : $!";
my $contest = $contest_st->fetchrow_hashref;
$contest_st->finish;


my $prob_dir = "$SETS_DIR/".$$contest{'set_code'}."/".$$problem{'letter'};


#some paths:
#my $ex = '/home/spojrun';
#my $sets = './sets';

#System "rm $EXEC_DIR/grade.log";
System "echo '==== Run $run_id ===='";


#chdir "./execute" or die "unable to chdir to execute";

#cleanup
System "rm $EXEC_DIR/program";
System "rm $EXEC_DIR/program.cpp";
System "rm $EXEC_DIR/test.in";
System "rm $EXEC_DIR/test.out";
System "rm $EXEC_DIR/run.log";
System "rm $EXEC_DIR/run.err";
#System "rm $EXEC_DIR/grade.log";
#System "rm $EXEC_DIR/grade.err";
System "rm $EXEC_DIR/*.class";
System "rm $EXEC_DIR/*.java";

sub JavaMain{
#TODO: limitation - only one top level class, default package
	$$run{'source_name'} =~ /^(.+)\.java$/ or die "What source name!?";
	return $1;
}

my $status = 'ok';
my $lang = $$run{'language'};
my $java_main = '';
if($lang eq 'cpp'){
	WriteFile "$EXEC_DIR/program.cpp", $$run{'source_code'};

	System "su spoj0run -c \"g++ -O2 $EXEC_DIR/program.cpp -o $EXEC_DIR/program\" ";
	$status = 'ce' if(not -f "$EXEC_DIR/program");

}
elsif($lang eq 'java'){
	$java_main = JavaMain;
	WriteFile "$EXEC_DIR/$java_main.java", $$run{'source_code'};

	System "su spoj0run -c \"javac $EXEC_DIR/$java_main.java\" ";
	$status = 'ce' if(not -f "$EXEC_DIR/$java_main.class");
}
else{
	die "Unsupported language $lang!";
}


#dont do it for now
#System 'rm $EXEC_DIR/*'; 

#-- ! making

#now we will determine how to check
#	if test.01.in doesnt exist, then the solution will be checked against single file
#	else the check starts with test.01.in, and continues until test.??.in is present

#run against given input infix (infix is the part between 'test' and '.in'
sub Run{
	my $infix = shift;
		
	my $set_in = "$prob_dir/test$infix.in";
	my $set_ans = "$prob_dir/test$infix.ans";
	
	print "Testing $infix\n";
	
	if(! -f $set_in){
		$status = 'ie';
		warn "can not find $set_in\n";
	}
	if(! -f $set_ans){
		$status = 'ie';
		warn "can not find $set_ans\n";
	}
	
	my $run_in = "$EXEC_DIR/test.in";
	my $run_out = "$EXEC_DIR/test.out";
	
	if($status eq 'ok'){ #run
		chdir($EXEC_DIR);
		
		#copy input
		System "cp '$set_in' '$run_in'";
		
		my $time = $$problem{'time_limit'};
		++$time if $lang eq 'java';
		#-- ! executing
		
		my $gross_time = 3*$time; #timeout time
		
		my $exec = '';
		if($lang eq 'cpp'){
			$exec = "$EXEC_DIR/program";
		}
		elsif($lang eq 'java'){
			$exec = "java -cp . $java_main";
		}
		else{
			die "Unsupported language $lang!";
		}
		
		my $run = "time timeout $gross_time $exec < $run_in >$run_out 2>>$EXEC_DIR/run.err";
		
		
		my $megarun = "launchtool --stats --tag=spoj0-grade --limit-process-count=30 "
			."--limit-open-files=60 --user=spoj0run '$run' > $EXEC_DIR/time.out";
		
		my $exit = System $megarun;
		warn $exit;
		if($exit == 35072){
			#killed - timeout
			$status = 'tl1';
		}
		elsif($exit != 0 || -s "$EXEC_DIR/run.err"){
			$status = 're';
		}
		
		System "cat $EXEC_DIR/time.out";
		if($status eq 'tl1' || $status eq 'ok'){
			#check cpu+user time
			my $time_info = ReadFile "$EXEC_DIR/time.out";
			#example: Time: running: 00:00:00 user: 0.000000s system: 0.004000s
			$time_info =~ /Time: running: \d+:\d+:\d+ user: (\d+\.\d+)s system: (\d+\.\d+)s/ or warn;
			my $runned = $1 + $2;
			$status = 'tl' if($runned > $time);
			warn "Program consumed $runned seconds cpu time.\n";
		}
		
		
	}
	
	if($status eq 'ok'){ #check
		if(-f "$prob_dir/checker"){
			print "Running checker...\n";
			my $exit = System "$prob_dir/checker $set_in $set_ans $run_out";
			print "exit=$exit\n";
			warn "checker exit=$exit\n";
			$status = 'ok' if($exit == 0);
			#bugfix - why 1 -> 256?
			$status = 'wa' if($exit == 1 || $exit == 256);
			$status = 'ie' if($exit != 0 && $exit != 1 && $exit != 256);
		}
		else{
			my $exit = System "diff $run_out $set_ans";
			#warn $exit;
			if($exit != 0){
				$exit = System "diff -w $run_out $set_ans";
				if($exit == 0) {
					$status = 'pe';
				}
				else{
					$status = 'wa';
				}
			}
		}
	}	
	
}

if($status eq 'ok'){ #run
	if(! -f "$prob_dir/test.01.in"){
		Run "";
	}
	else{
		my $i = 0;
		while($status eq 'ok'){
			++$i;
			my $infix = '.'.sprintf("%02d", $i);
			last if(!(-f "$prob_dir/test$infix.in"));
			Run $infix;
			warn $status;
		}
	}
}




my $log = "=== GRADE ===\n";
$log .= Limit ReadFile("$EXEC_DIR/grade.log");
$log .= "=== GRADE ERR ===\n";
$log .= Limit ReadFile("$EXEC_DIR/grade.err");
#$log .= "=== RUN ===\n";
#$log .= ReadFile "$ex/run.log";
$log .= "=== RUN ERR ===\n";
$log .= Limit(ReadFile("$EXEC_DIR/run.err"));


my $final_st = $dbh->prepare("UPDATE runs SET status=?, log=? WHERE run_id=?");
$final_st->bind_param(1, $status);
$final_st->bind_param(2, $log);
$final_st->bind_param(3, $run_id);
$final_st->execute;
print "Done with $run_id, status $status.\n"

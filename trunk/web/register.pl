#!/usr/bin/perl

use strict;

use lib "/home/spoj0/";
use Time::localtime;
use DBI;
use spoj0;
use CGI qw/:standard :html3/;

my $dbh = SqlConnect;

my $title = "Register";
print header(-charset=>'utf-8'),
	start_html($title),
	WebNavMenu,
	h1($title);

my $ok = 0;
if(param('username') || param('password')){
	$ok = SubmitForm();
}

$ok or PrintForm();

print end_html;


sub Field{
	my $desc = shift or die;
	my $name = shift or warn;	
	return td({-align=>'right'}, $desc).
        td({-align=>'left'}, textfield($name, param($name)));
}

sub PrintForm {
	my @lang_names = keys %LANGUAGES;
	my $language = param('language') || $DEFAULT_LANG;
	
	print qq(
		<p>
			Due to the large number of users that we had to register manually,
			we finally gave up and made this form. Everyone is now allowed to
			register and participate in the contests of the system.
			After you register you can participate in any contest of the system. 
			(There is no need to register for individual contests.)
		</p>
		<p>
			<strong>Warning:</strong> Before registering keep the folowing things in mind:
			<ul>
				<li>You have to enter valid user data.</li>
				<li>You may use cyrillic (or other unicode) for the displayed info (such as names).</li>
				<li>Because of the current security implementation you don't need to use a strong password.</li>
				<li>Registration is performed only once, so please fill in valid data. You will not be able to update the data later.</li>
				<li>Users that do not look like real users will be removed.</li>
				<li>The system is mostly protype quality, so please do not abuse it and report problems you discover.</li>
				<li>From the fields below, fill those which are appropriate for you.</li>
				<li>It is required to enter at least one contact informaction.</li>
			</ul>
		</p>

	);

	print start_form(),
		table(
			{'-border'=>0},
        	undef,
        	Tr({-align=>'center'}, [
				Field("Username:", 'name'),
        		
        		td({-align=>'right'}, "Password:").
        			td({-align=>'left'}, password_field('password', param('password'))),
        		
				Field("Full name (e.g. first and last name):", "display_name"),
				Field("Country/City:", "city"),
				Field("School/Organization:", "inst"),
				Field("Faculty number (if applicable):", "fn"),
				Field("email:", "email"),
				Field("icq:", "icq"),
				Field("skype:", "skype"),
				Field("other (contact, notes, etc.):", "other"),
        			
        		td({-align=>'center'}, submit(-label=>'Submit')).
        			td({-align=>'left'}, "")
        	]
        	
        	)
    	),
    	end_form;
    	
}

sub SubmitForm{
	my $error = "Успяхме (май) :)";
	my $r = 0;
	
	#TODO: no check for duplicates!!! (not concurent safe)
	if(Login($dbh, param('name'), param('password'))){
		$error = "Username is already registered!";
	}
	elsif(length(param('display_name')) < 5){
		$error = "You have not entered full name!";
	}
	else{
		my %user_data = (
			'name' => param('name'),
			'password' => param('password'),
			'display_name' => param('display_name'),
			'about' => ""
		);
		$user_data{'about'} .= " city:".param('city') if(param('city'));
		$user_data{'about'} .= " inst:".param('inst') if(param('inst'));
		$user_data{'about'} .= " fn:".param('fn') if(param('fn'));
		$user_data{'about'} .= " email:".param('email') if(param('email'));
		$user_data{'about'} .= " icq:".param('icq') if(param('icq'));
		$user_data{'about'} .= " skype:".param('skype') if(param('skype'));
		$user_data{'about'} .= " other:".param('other') if(param('other'));
		$r = RegisterUser $dbh, \%user_data;
		$r or $error = "Error (some)";
	}
	print p(strong($error));
	return $r;
}

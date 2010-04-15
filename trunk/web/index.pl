#!/usr/bin/perl

use strict;

use lib "/home/spoj0/";
use Time::localtime;
use DBI;
use spoj0;
use CGI qw/:standard :html3/;

print header(-charset=>'utf-8'),
	start_html($NAME),
	WebNavMenu,
	h1($NAME),
	qq^
		<p>
			This is the minimalistic online judge named <strong>$NAME</strong>.
			Use the menu on top to navigate.
		</p>
		
		<p>
			Currently most of the things are manual. To register an account contact the 
			administrator.
		</p>
		<p>
			The source code of the system is available at <a href="http://code.google.com/p/spoj0/">http://code.google.com/p/spoj0/</a>
		</p>
	^;
	
print end_html;


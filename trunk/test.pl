#!/usr/bin/perl

use strict;
use Time::localtime;
use DBI;
use spoj0;

my $dbh = SqlConnect;

warn $dbh->quote("Don't\""), " -- should be quoted correctly";

my $r = System "./test.sh";
warn $r;
#!/usr/bin/perl
#
use strict;
use warnings;
use Data::Dumper;

my $ps_result = `ps aux|grep -P "trading.main.robot.php"`;

my @lines = split /\n/, $ps_result;

foreach my $line(@lines){
	my @fields = split /\s+/,$line;
	my $pid = $fields[1];
	# kill it
	`kill $pid`;
}

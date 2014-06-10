#!/usr/bin/perl
#
use strict;
use warnings;

my $num_bots = `ps aux|grep trading.main.robot.php|wc -l`;
# $num_bots = chomp $num_bots;
my $subject = "robots died";
if($num_bots != 7){
	print "sending notification email\n";
	my $email_cmd = "echo '' |mail -s \"$subject\" manazhao@" . "gmail.com";
	print $email_cmd . "\n";
	`$email_cmd`;
}

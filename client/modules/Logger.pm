package Logger;

use strict;
use warnings;
use 5.012;
use DateTime;
use Exporter 'import';
no warnings 'utf8';
#use UTF8;
#no UTF8;

our @EXPORT = qw(log);
our $VERSION = '1.00';
our $DEBUG=0;
#our $linux=!$^O=~/^MSWin/;

sub log(@)
{
 local $_ = shift;
 my $d = shift;
 return if(!$DEBUG&&defined($d)&&$d eq '2');
 if($DEBUG||!$d)
 {
  print $_."\n"
 }else{
  print $d if($d ne '1');
 }
 open LOG,'>>log';
 binmode(LOG,':utf8');
 my $dt   = DateTime->now;
 my $date = $dt->dmy;
 my $time = $dt->hms;
 print LOG "$date $time $_\n";
 close LOG;
}

sub debug(){$DEBUG=1;}

1;
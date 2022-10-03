#perl -w
use strict;
use warnings;
#use UTF8;
#no UTF8;
use 5.012;
use Term::ReadKey;
use lib 'modules';
use Logger;
use Getter qw(getf $UserAgent);
use API;
use Info;
use Encode qw(:all);

unless($^O=~/^MSWin/)
{
binmode(STDOUT,':utf8');
}else{
binmode(STDOUT,':encoding(cp866)');
API::issecond() if($#ARGV!=-1);
}

my $version=2210030;

my $id;
my @list;
my %outbox;
my $errcount=0;
my $PAUSE=900;
my $WAIT=700;
my $RAND=2200;
my $UA='Web browser 8.5';
my %config;
my $send=1;


if(-f 'update.lock')
{
 my @tmp=stat('update.lock');
 unlink 'update.lock' if(time()-$tmp[9]>333);
}

sub send()
{
 my $res=0;
 if(keys %outbox)
 {
  my $tags="{\"id\":\"$id\",\"list\":[".
     join(",\n",values %outbox).']}';
#  log($tags,1);
  $res=API::put_list($id,$tags) if($send);
  %outbox=();
 }
 return $res;
}

$|=1;
ReadMode 4;

mkdir 'tmp' unless(-d 'tmp');
if(-f 'tmp/id')
{
 open F,'tmp/id';
 my $tmp=<F>;
 close F;
 chomp $tmp;
 $id=$tmp if(length($tmp)>1);
}
unless($id)
{
 $id=API::new_user();
 die "Can't get a user" unless($id);
 open F,'>tmp/id';
 print F $id;
 close F;
}

if(-f 'client.config')
{
 open F,'client.config';
 while(<F>)
 {
  chomp;
  $config{uc $1}=$2 if(/\s*(.+)\s*=\s*(.+)\s*/);
  $config{uc $1}=1 if(/^\s*(\w+)\s*$/);
 }
 close F;
}

$UA=$config{'UA'} if(defined $config{'UA'});
$WAIT=$config{'WAIT'} if(defined $config{'WAIT'});
$WAIT=0 if($WAIT<0);
$RAND=$config{'RAND'} if(defined $config{'RAND'});
$RAND=0 if($RAND<0);
$PAUSE=$config{'PAUSE'} if(defined $config{'PAUSE'});
$PAUSE=2 if($PAUSE<2);
Logger::debug() if(defined $config{'DEBUG'});
$send=0 if(defined $config{'NOSEND'});
$UserAgent=$UA;

print "Ver=$version, ID=$id, waiting=(".($WAIT).'+(0..'.($RAND).'))s, pause='.($PAUSE)."s, ua=$UA\n";

while(1)
{
 my $list=API::get_list($id);
 if($list)
 {
  my @list=@$list;
  Logger::log("Server version ".API::version(),1);
  if(API::version()>$version)
  {
   Logger::log("New version ".API::version());
   $version=-1;
   last;
  }
  unlink 'update.lock' if(-f 'update.lock');
  my $N=0;
  foreach my $val (@list)
  {
   if($val)
   {
    my $file=Getter::getf($val);
    my %file=%$file;
    my $stat=$file{'status'};
    if(defined($stat)&&($stat==0))
    {
     my $fn=$file{'filename'};
     #$fn=Encode::decode_utf8($fn);
     Logger::log("ID=$val STATUS=$stat EXT=$file{'ext'} FILE=$fn",chr(13).'#'.(++$N));
     my $tags=Info::get("tmp/$val$file{'ext'}",$fn);
     $outbox{$val}="{\"id\":\"$val\",\"status\":0,\"list\":[$tags]}";
    }else{
     Logger::log("ID=$val STATUS=$stat",'File #'.(++$N).' error: '.$stat);
     $outbox{$val}="{\"id\":\"$val\",\"status\":$stat}";
    }
    last if(API::lsleep($WAIT,$RAND,1));
   }
  }
  last if(!&send());
 }else{last;}
 last if(API::lsleep($PAUSE));
 last if(API::hasKey());
}
&send();
ReadMode 0;
if($version<0)
{
 if(-f 'update.lock')
 {
  open F,'update.lock';
  my $v=<F>;
  close F;
  if(API::version() eq $v)
  {
   Logger::log("Update error");
   exit 1;
  }
 }
 sleep(1);
 API::update() && API::upbatch();
 exit 7;
}
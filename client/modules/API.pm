package API;

use strict;
use warnings;
use 5.012;
use Exporter 'import';
#use UTF8;
#no UTF8;
use LWP::UserAgent;
use Logger;
use Archive::Tar;
use File::Copy;
use Term::ReadKey;

our @EXPORT = qw(new_user get_list put_list ext);
our $VERSION = '1.00';
our %exts = ();
our $ver=0;
our $wait=64;
our $itercount=7;
our $primary=1;

sub geta($)
{ 
 my $url='https://kartel.declarator.org/iapi/iapi.php?act='.$_[0];
 Logger::log("API $url",1);
 my $iter=$itercount;
 while($iter--)
 {
  my $response = LWP::UserAgent->new->get($url);
  if ($response->is_success) 
  {
    my $content=$response->decoded_content;
    if (length $content)
    {
      Logger::log($content,2);
      return $content;
    }
  }else{Logger::log("Get error ".Encode::decode("cp1251",$response->status_line));}
  lsleep($wait*($itercount*1.5-$iter));
 }
 return 0;
}

sub posta($$)
{ 
 my $url='https://kartel.declarator.org/iapi/iapi.php?act='.$_[0];
 Logger::log("API $url",1);
 my $tmp = Encode::encode('utf8',$_[1]);
 Logger::log($_[1],2);
 my $iter=$itercount;
 while($iter--)
 {
  my $response = LWP::UserAgent->new->post( $url, 'Content-type' => 'application/json;charset=utf-8', Content => $tmp );
  if ($response->is_success) 
  {
   my $content=$response->decoded_content;
   Logger::log("Post content: $content",chr(13).'Post '.($content?'OK':'Error').' 'x9);
   return 1;
  }
  Logger::log("Post error ".Encode::decode("cp1251",$response->status_line));
  lsleep($wait*($itercount*1.5-$iter));
 }
 return 0;
}

sub new_user()
{
 my $res=geta("new");
 return ($res=~/\{"result":\{"id":"([\w\d\-]+)"\}\}/)?$1:0;
}

sub get_list($)
{
 if(-f 'list')
 {
  open F,'list';
  my $e=<F>;
  chomp $e;
  foreach(split(/,/,$e))
  {
   $exts{'.'.$_}=1;
  }
  my @a=<F>;
  close F;
  move('list','list.bak');
  chomp @a;
  return \@a;
 }
 my $iter=5;
 while(--$iter)
 {
  my $res=geta("get&id=".$_[0]);
  my @list;
  if($res=~s/"list":\[([^\]]+)\]//)
  {
   my @a=split(',',$1);
   foreach(@a)
   {
    if(/"([\w]+)"/)
    {
     push(@list,$1);
    }
   }
  Logger::log('Got IDs '.join(',',@list),1) if($#list!=-1);
  }
  if($res=~s/"exts":\[(.+)\]//)
  {
   foreach(split(/,/,$1))
   {
    $exts{'.'.$1}=1 if(/"(\w+)"/);
   }
  }
  if($res=~s/"ver":"(\d+)"//)
  {
   $ver=$1;
  }
  Logger::log('Got '.($#list+1).' file(s)');
  return \@list if($#list>=0);
  return 0 if(API::lsleep(7-$iter));
 }
 return 0;
}

sub put_list($$)
{
 return posta("put&id=".$_[0],$_[1]);
}

sub ext($)
{
 return defined($exts{lc $_[0]});
}

sub version(){return $ver;}

sub update()
{
 open F,'>update.lock';print F $ver;close F;
 my $url='https://kartel.declarator.org/iapi/up.php?what=tar';
 Logger::log("API $url","Updating\n");
 my $response = LWP::UserAgent->new->get($url);
 if ($response->is_success) 
 {
   my $content=$response->decoded_content;
   if (length $content)
   {
     open F,'>tmp/client.tar';
     binmode F;
     print F $content;
     close F;
     my $tar=Archive::Tar->new('tmp/client.tar');
#     $tar->read('/path/of/tar/test.tar.gz');
     $tar->extract();
     return 1;
   }
 }else{Logger::log("Get error ".$response->status_line);}
 return 0;
}

sub upbatch()
{
 return 1 unless($primary);
 my $url='https://kartel.declarator.org/iapi/up.php?what='.($^O=~/^MSWin/?'run':'sh');
 Logger::log("API $url",1);
 my $response = LWP::UserAgent->new->get($url);
 if ($response->is_success) 
 {
   my $content=$response->decoded_content;
   my $cdisp=$response->header("content-disposition");
   if (length $content&&$cdisp=~/filename="([^"]+(\.\w+))"/)
   {
    my $path=-d '../perl'?"../$1":"$1";
    if($path)
    {
     Logger::log("Update batch",1);
     open F,">$path";
     binmode F;
     print F $content;
     close F;
     return 1;
    }
   }
 }else{Logger::log("Get error ".$response->status_line);}
 return 0;
}

# Keypressed
sub hasKey(){return defined(ReadKey(-1));}
# Sleep $1 secs or $1+(0..$2)secs
sub lsleep(@)
{
 my $n=$_[0];

 $n+=int(rand(1+$_[1])) if($#_>0);
 while($n>0)
 {
  sleep(1);
  --$n;
  if(hasKey()){print "\n";return 1;}
  print '.';
 }
 print ($#_>1?' ':"\n");
 return hasKey()?1:0;
}

sub issecond(){$primary=0;}

1;
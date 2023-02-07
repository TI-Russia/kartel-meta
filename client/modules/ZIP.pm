package ZIP;

use strict;
use warnings;
#use UTF8;
#no UTF8;
use 5.012;
use Logger;
use Getter;
use API;
use Info;
use File::Basename qw(fileparse);
use IO::File;
use Archive::Zip qw/ :ERROR_CODES :CONSTANTS /;
use Encode qw(:all);
use if $^O=~/^MSWin/, "Win32/File";

#if($win)
#  require Win32::File;
#  Win32::File->import();

our @EXPORT = qw(unzip);
our $VERSION = '1.00';
our $fnum=1;
our $win=$^O=~/^MSWin/;

sub extractExtra($)
{
 my $v=$_[0];
 my $p=0;
 my $cycle=99;
 while($p<(length($v)-4))
 {
  my ($id,$len)=unpack('ss',substr($v,$p,4));
#  print 'Extras: '.$p.' '.$id.' '.$len."\n";
  $p+=4;
  if($id==28789)
  {
    return decode("utf8",substr($v,$p+5,$len-5));
  }
  $p+=$len;
  last unless($cycle);
 }
 return '';
}

sub unzip($) {
 my $file = $_[0];
 local $Archive::Zip::UNICODE = 1;
 my $u = Archive::Zip->new();
 unless($u->read($file) == AZ_OK) {
    return '';
 }
 my $status;
 my $result = '';
 foreach my $mem ($u->members()) 
 {
  unless($mem->isDirectory())
  {
   my $tmp=extractExtra($mem->cdExtraField());
   $tmp=$mem->fileName() unless($tmp);
   $tmp=~s-\\-/-g;
   $tmp=~s-"-'-g;
   $tmp=~s/[\x00-\x1f]+/ /g;
   unless(utf8::is_utf8($tmp)){$tmp=decode('cp866',$tmp);}
   Logger::log($tmp,1);
   my ($name, $path, $ext) = fileparse($tmp, qr/\.[^.]*$/);
   if($name&&$ext&&API::ext($ext)&&$name!~/^~\$/&&$name!~/[\\\/]~\$/)
   {
    Logger::log("$path,$name,$ext",1);
    my $destfile = "tmp/$fnum$ext";
    $u->extractMemberWithoutPaths($mem,$destfile);
    if($win)
    {
      my $attrib;
      Win32::File::GetAttributes($destfile, $attrib);
      Win32::File::SetAttributes($destfile, $attrib & !(Win32::File::READONLY()|Win32::File::HIDDEN()));
    }
    my $tempres=Info::get($destfile,$tmp);
    if($tempres)
    {
     $result.=',' if($result);
     $result.=$tempres;
    }
    ++$fnum;
   }
  }
 }
 return $result;
}

1;
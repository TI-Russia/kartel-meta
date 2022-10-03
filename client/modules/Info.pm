package Info;

use strict;
use warnings;
use 5.012;
use Exporter 'import';
use Image::ExifTool qw/:Public/;
#use UTF8;
#no UTF8;
use Logger;
use ZIP;
use Encode qw(:all);

our @EXPORT = qw(get);
our $VERSION = '1.00';

my %badtags = ('ZipCRC', 1, 'CompObjUserType', 1, 'WordDocumentBodySectPRPictBinData', 1, 'Hyperlinks', 1);

sub get($$)
{
 my $f = $_[0];
 my $fn = $_[1];
 my $result="{\"filename\": \"$fn\",\"tags\":\"";
# utf8::upgrade($result);
 if($f=~/\.(\w+)$/&&$f!~/^~\$/)
 {
  my $e=lc $1;
  if($e eq 'zip'){
   $result=ZIP::unzip($f);
#  }elsif($e eq 'rar'){
#  }elsif($e eq '7z'){
#  }elsif($e eq 'arj'){
  }else{
   my $info = ImageInfo($f);
   my %goodtags;
   foreach (keys %$info) 
   {
    my $inf=$info->{$_};
    unless(/\(\d+\)/||$goodtags{$_}||$badtags{$_}||length($_)>2048)
    {
     $inf=$fn if($_ eq 'FileName');
     $inf=~s/\000/ /;
     $goodtags{$_}=1;
     $inf=~s/&/&amp;/g;
     $inf=~s/"/&quot;/g;
     $inf=~s/</&lt;/g;
     $inf=~s/>/&gt;/g;
     $inf=~s/\\/\\\\/g;
     $inf=~s/'/''/g;
     $inf=~s/[\x00-\x1f]+/ /g;
#     utf8::upgrade($inf);
     $inf=decode_utf8($inf) unless(is_utf8($inf));
#     print utf8::is_utf8($inf).'='.$inf."\n";
     $result.="<tag t=\\\"$_\\\" v=\\\"$inf\\\"/>";
#     print utf8::is_utf8($result).'*'.$result."\n";
    }
   }
   $result.='"}';
  }
 }
 unlink $f;
 return $result;
}

1;
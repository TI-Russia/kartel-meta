package Getter;

use strict;
use warnings;
use 5.012;
use Exporter 'import';
#use UTF8;
#no UTF8;
#use URI::Escape;
use LWP::UserAgent;
use Logger;
use Tester;

our @EXPORT = qw(getf $UserAgent);
our $VERSION = '1.00';
our $UserAgent = 'LWP';

sub getf($)
{ 
 my $url = 'https://zakupki.gov.ru/44fz/filestore/public/1.0/download/priz/file.html?uid='.$_[0];
 Logger::log("Download $url",2);
 my $ua=LWP::UserAgent->new;
 $ua->timeout(333);
 $ua->agent($UserAgent);
 my $response = $ua->get($url);
 my %result;
 if ($response->is_success) 
 {
   my $ctype = $response->header("content-type");
   my $cdisp = $response->header("content-disposition");
   my $content=$response->decoded_content;
   if (length $content)
   {
      $cdisp='' unless($cdisp);
      Logger::log("Content-type: $ctype, Content-disposition: $cdisp",2);
      if($ctype=~/^application\/download/ && $cdisp=~/filename="([^"]+(\.\w+))"/)
      {
       my $e=$2;
       my $fn='tmp/'.$_[0].$e;
       #my $gfn=uri_unescape($1);
       my $gfn=$1;
       $gfn=~s/%([0-9A-Fa-f]{2})/chr(hex($1))/eg;
       $gfn=~s/[\/\\]/_/g;
       $gfn=Encode::decode('utf8', $gfn);
       $gfn=~s/\s\s+/ /g;
       while(length($gfn)>199)
       {
         $gfn=~s/^\.\.+//;
         my $diff=length($gfn)-199;
         $gfn=~/^(.+)\.([^\.]+)$/;
         my $fp=substr($1,0,length($1)-$diff);
         $gfn=~s/^.+\.([^\.]+)$/$fp.$1/;
       }
       Logger::log('Length('.$e.'): '.length($content),1);
       open F,">$fn";
       binmode F;
       print F $content;
       close F;
       %result=(status=>0,filename=>$gfn,ext=>$e);
       return \%result;
      }else{
       Logger::log("File content: $content",1);
       if(Tester::test($content))
       {
        %result=('status',0);
        return \%result;
       }
    }
    Logger::log("Unknown result");
    %result=('status',500);
    return \%result;
   }
   Logger::log("Zero-sized content");
   %result=('status',500);
   return \%result;
 } else {
   Logger::log('Failed: '.Encode::decode("cp1251",$response->status_line));
#   Logger::log($response->decoded_content);
#Logger::log( $response->headers_as_string);
   %result=('status', $response->code());
   return \%result;
 }
}

1;
<?php
require_once 'splitbrain/PHPArchive/FileInfo.php';
require_once 'splitbrain/PHPArchive/Archive.php';
require_once 'splitbrain/PHPArchive/Tar.php';
use splitbrain\PHPArchive\Tar;
use splitbrain\PHPArchive\Archive;
$w=isset($_GET['what'])?$_GET['what']:0;
if($w=='tar')
{
$tar = new Tar();
$tar->setCompression(0, Archive::COMPRESS_NONE);
$tar->create();
$tar->addFile('client/client.pl','client.pl');
$dir=scandir('client/modules/');
foreach($dir as $file)
{
 if(substr($file,0,1)!='.')
 {
  $tar->addFile('client/modules/'.$file,'modules/'.$file);
 }
}
header("Content-Type: application/tar");
header('Content-Disposition: attachment; filename="client.tar"');
echo $tar->getArchive(); // compresses and returns it
return;
}elseif($w=='cli')
{
$tar = new Tar();
$tar->setCompression(9, Archive::COMPRESS_BZIP);
$tar->create();
$dir=scandir('client/cc/');
foreach($dir as $file)
{
 if(substr($file,0,1)!='.')
 {
  $tar->addFile('client/cc/'.$file,$file);
 }
}
header("Content-Type: application/application/x-bzip2");
header('Content-Disposition: attachment; filename="client.tar.bz2"');
echo $tar->getArchive(); // compresses and returns it
return;
}elseif($w=='tlc')
{
$tar = new Tar();
$tar->setCompression(9, Archive::COMPRESS_BZIP);
$tar->create();
$dir=scandir('client/tc/');
foreach($dir as $file)
{
 if(substr($file,0,1)!='.')
 {
  $tar->addFile('client/tc/'.$file,$file);
 }
}
header("Content-Type: application/application/x-bzip2");
header('Content-Disposition: attachment; filename="tc.tar.bz2"');
echo $tar->getArchive(); // compresses and returns it
return;
}elseif($w=='7z')
{
header("Content-Type: application/octet-stream");
header('Content-Disposition: attachment; filename="7z.dll"');
echo file_get_contents("client/tc/7z.dll");
return;
}elseif($w=='cfg')
{
header("Content-Type: text/plain");
header('Content-Disposition: attachment; filename="client.config"');
echo file_get_contents("client/client.config");
return;
}elseif($w=='run')
{
header("Content-Type: text/plain");
header('Content-Disposition: attachment; filename="update.bat"');
echo file_get_contents("client/runme.bat");
return;
}elseif($w=='sh')
{
header("Content-Type: text/plain");
header('Content-Disposition: attachment; filename="runme.sh"');
echo file_get_contents("client/runme.sh");
return;
}elseif($w='url')
{
echo '<a href="https://dotnet.microsoft.com/en-us/download/dotnet/thank-you/runtime-6.0.9-windows-x64-installer">Microsoft .Net 6 for client application</a>';
return;
}
echo "WHAT?!";
?>
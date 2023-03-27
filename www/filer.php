<?php
ob_start();
function file_size($size)
{
$filesizename = array(" Bytes", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");
return $size ? round($size/pow(1024, ($i = floor(log($size, 1024)))), 2) . $filesizename[$i] : '0 Bytes';
}

function toutf( $t )
 {
  return iconv("windows-1251","utf-8",$t);
 }
function to1251( $t )
 {
  return iconv("utf-8","windows-1251",$t);
 }

function add_dir ( $dir, &$fname)
{
//   echo "checking:". $dir . "\n";
     if ($dir[strlen($dir)-1] == '.') {} else
      if ($dh = opendir($dir))
	{
	        while (($file = readdir($dh)) !== false) 
		{
		    // Если это файл ...
			if(is_file($dir . '/'. $file))
			{ // ... добавляем его в конец массива.
                array_push($fname, $dir .'/'. $file);
			}
		else { add_dir($dir . '/'. $file, $fname); }	
                 }
        closedir($dh);
        }
}
// Проверяем, каталог ли это?
function parse_tree ( $dir) 
{

if (is_dir($dir))
{
	// Если каталог успешно открыт...
    if ($dh = opendir($dir))
	{
		// ... считываем содержимое в переменную $file.
        while (($file = readdir($dh)) !== false) 
		{
//		$file=toutf($file);
		    // Если это файл ...
			if(is_file($dir . '/'. $file))
			{ 	// ... добавляем его в конец массива.
                             array_push($fname, $dir .'/'. $file);
			}
		else {
			
			echo ( $file."/\n") ;
			 add_dir($dir . '/'. to1251($file), $fname);
                     }	
        }
		// Закрываем директорию.
        closedir($dh);
		

    }
}
};
//define globals
 $myurl=$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];
 $inputstyle='style="color:#000000; margin-top:2px; font-size:16px; background-color:#ffffff; border-radius: 7px; -moz-border-radius: 7px; -webkit-border-radius: 7px;">';
 function inputstyle($width)
 { return 'style=" width:'.$width.'px; margin-top:2px; color:#000000; font-size:16px; background-color:#ffffff; border-radius: 7px; -moz-border-radius: 7px; -webkit-border-radius: 7px;">';
 }
// $submitstyle='style="color:#000000; font-size:20px; background-color:#ffb800; border-radius: 7px; -moz-border-radius: 7px; -webkit-border-radius: 7px;">';
 $submitstyle='style="color:#000000; font-size:20px; background-color:var(--main-blue); border-radius: 7px; -moz-border-radius: 7px; -webkit-border-radius: 7px;">';
 $xlshdr='<html xmlns:o="urn:schemas-microsoft-com:office:office"
xmlns:x="urn:schemas-microsoft-com:office:excel"
xmlns="http://www.w3.org/TR/REC-html40">
<header><META HTTP-EQUIV="content-language" CONTENT="ru">
<META HTTP-EQUIV="content-type" CONTENT="text/html; charset=UTF-8"><title></title><style>.msnum{mso-number-format:General;}
.mstext{mso-number-format:0;}</style></headr><body>';
?>

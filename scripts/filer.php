<?php

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
		    // ���� ��� ���� ...
			if(is_file($dir . '/'. $file))
			{ // ... ��������� ��� � ����� �������.
                array_push($fname, $dir .'/'. $file);
			}
		else { add_dir($dir . '/'. $file, $fname); }	
                 }
        closedir($dh);
        }
}
// ���������, ������� �� ���?
function scanfiles($dir) 
{
$fname=array();
if (is_dir($dir))
{
	// ���� ������� ������� ������...
    if ($dh = opendir($dir))
	{
		// ... ��������� ���������� � ���������� $file.
        while (($file = readdir($dh)) !== false) 
		{
//		$file=toutf($file);
		    // ���� ��� ���� ...
			if(is_file($dir . '/'. $file))
			{ 	// ... ��������� ��� � ����� �������.
                             array_push($fname, $dir .'/'. $file);
			}
		else {   echo ( $file."/\n") ;
			 add_dir($dir . '/'. to1251($file), $fname);
                     }	
        }
		// ��������� ����������.
        closedir($dh);
    }
} return $fname;
};

?>

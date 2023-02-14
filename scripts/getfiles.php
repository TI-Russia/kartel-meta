<?php
echo "zip parser ver 1.1\r\n";
parse_str(implode('&', array_slice($argv, 1)), $_GET);
 if (!isset($_GET['dir']))
{
$dir="./ftp.zakupki.gov.ru/fcs_regions/";} else 
{$dir=$_GET['dir'];}

//$dir="./ftp.zakupki.gov.ru/fcs_regions/";
 $fname = array();

include 'filer.php';
include 'getfilesxml.php';
include 'getfilessql.php';
//$f=fopen('n.txt','a+');
$fname=scanfiles($dir);
echo $dir .' '. count($fname);
$sql_id=sql_connect("meta_zakupki");
   $d=count($fname);
   for($n=0; $n<count($fname); $n++) 
    {         $t = $fname[$n];
    if ((strpos($t,"notifica")!==false)&&(strpos($t,"Month")==false))
{
        $t1 = pathinfo($t);$fx=$t1['basename'];	
	echo $n.":".$fx."\r";
     $d=checkAddFile($sql_id,$fx,0,3);
//  $d=0;
 if ($d==0) 
    { //      zip parser
         $zip = new ZipArchive();
         if ($zip->open($t, ZIPARCHIVE::CHECKCONS)==true)
           { for ($i = 0 ; $i < $zip->numFiles; $i++) 
		{ $fn=$zip->getNameIndex($i);
	           if (strpos($fn,".xml")!==false)
	             { 
		
		if (
			(strpos($fn,"icationZK")!==false)
			||(strpos($fn,"OK504")!==false)
			||(strpos($fn,"icationEA")!==false)
			||(strpos($fn,"icationZ")!==false)
		        ||(strpos($fn,"icationOKU")!==false)
			||(strpos($fn,"onEF2020")!==false)
			||(strpos($fn,"onEZK2020")!==false)
			||(strpos($fn,"onEOK2020")!==false)
			||(strpos($fn,"onOK44")!==false)
			||(strpos($fn,"onINM")!==false)
			||(strpos($fn,"onPO44")!==false)
			||(strpos($fn,"onEP44")!==false)
	         	)
			
                { // echo  $fn.PHP_EOL;
		
		 $buf=$zip->getfromindex($i);
		 $fd=XmlNotifyParse($buf,$fn);
    	      if (count($fd[2])==0) //lots cnt.
					 { 
			//			$f=fopen('bug/'.$fn,'a+');fwrite($f,$buf);fclose($f);
	   				} else
		 {
		     exec_sql($sql_id,$fd,$fn);
		 }
                }
               }
             }
           if ($zip->numFiles>0) {$zip->close();};
	  }
	unset($zip);
	$d=checkAddFile($sql_id,$fx,1,3);
       };	
};
};
//        zip_close($zp);
  
   
  sql_close($sql_id);
  echo "\nfinished OK";
?>
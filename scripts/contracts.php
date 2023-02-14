<?php
echo "zip parser ver 1.1\r\n";
echo 'php version: [' . phpversion() . "]\n";
parse_str(implode('&', array_slice($argv, 1)), $_GET);
 if (!isset($_GET['dir']))
{
$dir="./ftp.zakupki.gov.ru/fcs_regions";} else 
{$dir=$_GET['dir'];}

// $fname = array();
//echo $fn;
include 'filer.php';
include 'ContractsXML.php';
include 'ContractsSQL.php';
$fname=scanfiles($dir);
echo $dir .' '. count($fname)." files found\n";

$sql_id=sql_connect();
$memused=memory_get_peak_usage();
   for($n=0; $n<count($fname); $n++) 
    { 
        $t = $fname[$n];
//   $md=memory_get_peak_usage()-$memused;
//   print_r(get_defined_vars());
    if ((strpos($t,"contract_")!==false)&&(strpos($t,"Month")==false))
{
        $t1 = pathinfo($t);$fx=$t1['basename'];	
	echo $n.":".$fx."\r";
	$d=checkAddFile($sql_id,$fx,0,1);
 if ($d==0) 
    {	//      zip parser
         $zip = new ZipArchive();
         if ($zip->open($t, ZIPARCHIVE::CHECKCONS)==true)
           { for ($i = 0 ; $i < $zip->numFiles; $i++) 
		{ $fn=$zip->getNameIndex($i);
                 if (strpos($fn,".xml")!==false) 
	   		{ if (strpos($fn,"contract_")!==false)       
              		 { 
		 		$buf=$zip->getfromindex($i);
     				$fd=XmlContractParse($buf);
//				if ($fd[13]>1) {
					        exec_sql($sql_id,$fd,$fn);	
//					};
	        		unset($fd);unset($buf);
			 }
	    	}}
	    if ($zip->numFiles>0) {$zip->close();};
	  }
	unset($zip);
	$d=checkAddFile($sql_id,$fx,1,1);
       };	
   }; 
};
  sql_close($sql_id);
?>                                              +
<?php
echo "zip parser ver 1.0\r\n";
parse_str(implode('&', array_slice($argv, 1)), $_GET);
 if (!isset($_GET['dir']))
{
$dir="./ftp.zakupki.gov.ru/fcs_regions/";} else 
{$dir=$_GET['dir'];}

//$dir="./ftp.zakupki.gov.ru/fcs_regions/";
 $fname = array();

include 'filer.php';
include 'protocolsxml.php';
include 'protocols2020xml.php';
include 'protocolsef1.php';
include 'protocolsef2.php';
include 'protocolscancel.php';
include 'sqlprotocol.php';
$fname=scanfiles($dir);
echo $dir .' found: '. count($fname)."\n";
$sql_id=sql_connect();
// logging data loading 
//   logOpen($sql_id,1,'loadtorgi',1);$prc=1;
   $d=count($fname);
   for($n=0; $n<count($fname); $n++) 
    { 
        $t = $fname[$n];
if ((strpos($t,"rotoco")!==false)&&(strpos($t,"Month")==false))
 {
   $t1 = pathinfo($t);$fx=$t1['basename'];	
	echo $n.":".$fx."\r";
	$d=checkAddFile($sql_id,$fx,0,2);
     if ($d==0) 
      {
        $zip = new ZipArchive();
         if ($zip->open($t, ZIPARCHIVE::CHECKCONS)==true)
           { for ($i = 0 ; $i < $zip->numFiles; $i++) 
		{ $fn=$zip->getNameIndex($i);
                 if (strpos($fn,".xml")!==false) 
	   		{ 
//cho $fn."\n";
	if (
 				(strpos($fn,"EF3_")!==false)||(strpos($fn,"EFSing")!==false)||(strpos($fn,"lPRO_")!==false)||(strpos($fn,"lPPI")!==false)
				||(strpos($fn,"lOK1")!==false)||(strpos($fn,"lOK2")!==false)||(strpos($fn,"lZK")!==false)||(strpos($fn,"lOU2")!==false)
				)
              		 { 
		 		$buf=$zip->getfromindex($i);
     				$fd=XmlProtocolParse($buf);
			        exec_sql($sql_id,$fd);		
	        		unset($fd);unset($buf);
			 } else
				if (
 				(strpos($fn,"2020Final")!==false)
				)
              		 { 
		 		$buf=$zip->getfromindex($i);
     				$fd=XmlProtocol2020Parse($buf);
			        exec_sql2020($sql_id,$fd);		
	        		unset($fd);unset($buf);
			 } else
			        if ((strpos($fn,"lPP_")!==false)
				||(strpos($fn,"lEF2")!==false))
                         { 
		 		$buf=$zip->getfromindex($i);
     				$fd=XmlProtocolEf2Parse($buf);
			        exec_sql2020($sql_id,$fd);		
	        		unset($fd);unset($buf);
			 } else
			        if ((strpos($fn,"lCancel")!==false)
				||(strpos($fn,"Cancel")!==false))
                         { 
		 		$buf=$zip->getfromindex($i);
     				$fd=XmlProtocolCancelParse($buf);
			        exec_sql_cancel($sql_id,$fd);		
	        		unset($fd);unset($buf);
			 }  else
				if (
 				(strpos($fn,"EF1")!==false)
				)
              		 { 
		 		$buf=$zip->getfromindex($i);
     				$fd=XmlProtocolEf1Parse($buf);
			        exec_sql2020($sql_id,$fd);		
	        		unset($fd);unset($buf);
			 }
	    	}
		}
	    if ($zip->numFiles>0) {$zip->close();};
	  }
	unset($zip);
	$d=checkAddFile($sql_id,$fx,1,2);
       };	

      } //true file
  } //files for

  sql_close($sql_id);

?>
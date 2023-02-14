<?php 
//$parser = xml_parser_create();
global $flag,$nno,$curtag;
global $date;
global $inn,$wp,$id;
global $rn;
global $taglevel;
    function startElement($parser, $name, $attrs)
    {
        global $flag;
	global $curtag;
	global $taglevel;
	$taglevel=$taglevel+1;
//notificationAttachments
        if (strpos("xx".$name,'NOTIFICATION')>0) { $flag=1;};
        if (strpos("xx".$name,'ATTACHMENTS')>0) {$flag=40;};
        if (strpos("xx".$name,'ATTACHMENTINFO')>0) {$flag=40;};
        if (strpos("xx".$name,'PURCHASERESPONSIBLE')>0) { $flag=2;};
        if (strpos("xx".$name,'PURCHASEOBJECTS')>0) { $flag=22;};
        if (strpos("xx".$name,'PROTOCOLZK')>0) {$flag=1;};
//        if (strpos("xx".$name,'OKEI')>0) { $flag=99;}; //нахуй
        if (strpos("xx".$name,'REQUIREMENTS')>0) { $flag=99;}; //нахуй
	if ($flag>0) { $curtag=$name; 
	             };
//	
//echo $name;
//        print_r($attrs);
    }
    function endElement($parser, $name)
    {
    global $flag;
    global $curtag;	
    global $taglevel;
    global $lots,$lot,$costs,$wp,$pnames,$pname;
    global $url,$publishedId,$fileName,$docDescription,$filn,$pd,$dd;
    $taglevel=$taglevel-1;
    $curtag="";
    if ($flag==40) 
	{ 
//	echo $fileName."\n";
     if ((strpos("xx".$name,'ATTACHMENT')>0)&&($url!=''))
	        { //array push
                array_push($filn,to1251($fileName));
                array_push($dd,to1251($docDescription));
                $n=strpos($url,'?uid=');

		if ($n>0) {$url=substr($url,$n+5);};
		if ($publishedId=='') {$publishedId=$url;};
		array_push($pd,$publishedId);
		$url='';$publishedId='';
//                <url>http://zakupki.gov.ru/44fz/filestore/public/1.0/download/priz/file.html?uid=EFE8E64E95250042E043AC110725B074</url>

		};
	}
    if ($flag==2)
      {
     if (strpos("xx".$name,'PURCHASEOBJECTS')>0) 
         { $flag=0; };
      }	
    }

  function parseData ($parser, $data)
    {
        global $curtag,$flag;
	global $inn,$wp;
	global $lot,$pname,$zktype;
	global $id,$rn;
	global $nno;
	global $date;
	global $taglevel;
       global $url,$publishedId,$fileName,$docDescription,$filn,$pd,$dd;
       if (strpos("xx".$curtag,'NS2:')>0) {$curtag=substr($curtag,4);};
       if (strpos("xx".$curtag,'NS3:')>0) {$curtag=substr($curtag,4);};
       if (strpos("xx".$curtag,'NS4:')>0) {$curtag=substr($curtag,4);};
       if (strpos("xx".$curtag,'NS6:')>0) {$curtag=substr($curtag,4);};
       if (strpos("xx".$curtag,'NS8:')>0) {$curtag=substr($curtag,4);};
       if (strpos("xx".$curtag,'NS7:')>0) {$curtag=substr($curtag,4);};
       if (strpos("xx".$curtag,'NS9:')>0) {$curtag=substr($curtag,4);};
       //  echo $flag.':'.$taglevel.':'.$curtag.":".$data. "\n";
       switch ($flag) 
	{
	case 1:{                          
		  switch ($curtag)
		 {
		  case 'ID':if (($id=="")&&($taglevel==3)) {$id=$data;} break;
		  case 'LOTNUMBER':$lot=$data;break;
		  case 'PURCHASEOBJECTINFO':
		  case 'LOTOBJECTINFO':$pname=$pname.' '.$data;break;
	//purchaseObjectInfo
		  case 'MAXPRICE':if ($wp<$data) {$wp=$data;}; break;
 		  case 'OOS:REGNUM':
		  case 'REGNUM':if (($rn=="")&&($taglevel==3)) {$rn=$data;} break;
		  case 'DOCPUBLISHDATE':
                  case 'PUBLISHDTINEIS':
					   $date=$data;
					break;

      		  case 'OOS:PURCHASENUMBER':
		  case 'PURCHASENUMBER':if (strlen($nno)<10) {$nno=$data;};break;
			 }
                };break;
        case 40:{  switch ($curtag)
		 {
		  case 'URL':$url=$data; break;
		  case 'DOCDESCRIPTION':$docDescription=$data;break;
		  case 'FILENAME':$fileName=$data;break;
		  case 'PUBLISHEDCONTENTID':$publishedId=$data;break;
		 }
                
		};break;

	};
    }

function XmlNotifyParse (&$d)
{
$parser = xml_parser_create();
global $flag,$inn,$wp,$nno;
global $id,$rn,$taglevel,$date,$lot;
global $lots,$lot,$costs,$wp,$pnames,$pname,$zktype;

global $url,$publishedId,$fileName,$docDescription,$filn,$pd,$dd;

$flag=0;$id='';$taglevel=0;$date='1';$lot=0;
$inn="123";$wp=0;$nno="";$rn="";$zktype="";
$lots=array();$costs=array();$pnames=array();
$filn=array();$pd=array();$dd=array();$url='';$publishedId='';
 xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, true);
  // указываем какие функции будут работать при открытии и закрытии тегов
  xml_set_element_handler($parser, "startElement", "endElement");
   // указываем функцию для работы с данными
  xml_set_character_data_handler($parser,"parseData");

 xml_parse($parser, $d, true);
 //echo "inn:".$inn." email:".$email." phone:".$phone." name:".$orgname." contract:".$id." rn:".$rn." purchase:".$nno."\n";
xml_parser_free($parser);

 return array($id,to1251($nno),$pd,$filn,$dd,$date,$zktype);
}
?>
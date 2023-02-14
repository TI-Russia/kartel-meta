<?php 
//$parser = xml_parser_create();
global $flag,$nno,$curtag,$email,$phone;
global $date;
global $inn,$wp,$id;
global $rn;
global $orgname;
global $fullname;
global $taglevel;
    function startElement($parser, $name, $attrs)
    {
        global $flag,$oktmoflag,$itemflag;
	global $curtag;
	global $taglevel;
	$taglevel=$taglevel+1;
	
        if (strpos("xx".$name,'PURCHASERESPONSIBLE')>0) { $flag=2;};
	if ($name=='LOT') {$flag=1;}; //ищем начало лота.
	if (($name=='OKPD')||($name=='OKPD2')||($name=='NS3:OKPD2')
	    ) {$flag=3;};
	if (($name=='PLACINGWAY')||($name=='NS3:PLACINGWAYINFO')
	    ||($name=='NS9:PLACINGWAY')) {$flag=33;};
        if (strpos("xx".$name,'PURCHASEOBJECTS')>0) { $flag=22;$itemflag=1;
	};
        if (strpos("xx".$name,'NOTIFICATION')>0) { $flag=1;};
        if (strpos("xx".$name,'PROTOCOLZK')>0) {$flag=1;};
        if (strpos($name,'OKTMOINFO')>0) {$oktmoflag=1;};
                     

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
    global $flag,$oktmoflag;
    global $curtag;	
    global $taglevel;
    global $lots,$lot,$costs,$wp,$pnames,$pname,$okpds,$okpd,$ip,$itemprice;
    $taglevel=$taglevel-1;
    $curtag="";
    if (strpos($name,'OKTMOINFO')>0) {$oktmoflag=0;};
    if ($name=='LOT') {
	  //закрываем лот 
	  if ($lot==0) {$lot=1;};
	  array_push($lots,$lot);
	  array_push($costs,$wp);$wp=0;                
	  array_push($pnames,to1251($pname));$pname='';
	  array_push($okpds,$okpd);
	  array_push($ip,$itemprice);
	 };
    if ($flag==2)
      {
     if (strpos("xx".$name,'PURCHASEOBJECTS')>0) 
         { $flag=0; };
      }	
    }

  function parseData ($parser, $data)
    {
        global $curtag,$flag;
	global $inn,$wp,$orgname,$fullname;
	global $lot,$okpd,$pname,$zktype;
	global $id,$rn;
	global $nno;
	global $date;
	global $taglevel,$bl;
	global $email,$phone;
	global $oktmoflag,$oktmo,$itemflag,$itemprice;
       if (strpos("xx".$curtag,'NS6:')>0) {$curtag=substr($curtag,4);};
       if (strpos("xx".$curtag,'NS8:')>0) {$curtag=substr($curtag,4);};
       if (strpos("xx".$curtag,'NS7:')>0) {$curtag=substr($curtag,4);};
       if (strpos("xx".$curtag,'NS9:')>0) {$curtag=substr($curtag,4);};
       if (strpos("xx".$curtag,'NS3:')>0) {$curtag=substr($curtag,4);};
       if (strpos("xx".$curtag,'NS2:')>0) {$curtag=substr($curtag,4);};
       if (strpos("xx".$curtag,'NS4:')>0) {$curtag=substr($curtag,4);};
//echo $flag.':'.$taglevel.':'.$curtag.":".$data. "\n";
       switch ($flag) 
	{
	case 1:{                          
		  switch ($curtag)
		 {
		  case 'ID':if (($id=="")&&(($taglevel==3)||($taglevel==2))) {$id=$data;} break;
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
		  case 'PURCHASENUMBER':if (strlen($nno)<5) {$nno=$data;};break;
		  case 'OOS:INN':
		  case 'NS3:INN':
		  case 'INN':$inn=$data;break;
		  case 'OOS:CONTACTEMAIL':
		  case 'CONTACTEMAIL':$email=$data;break;
		  case 'OOS:CONTACTPHONE':
		  case 'CONTACTPHONE':$phone=$phone.$data;break;
		  case 'NS3:TOTALSUM':
		  case 'TOTALSUM':if (($itemprice<$data)&&($itemflag==1)) {$itemprice=$data;};break;
//		  case 'MAXPRICE':
//		  case 'NS3:TOTALSUM':
		  case 'TOTALSUM':if ($wp<$data) {$wp=$data;}; break;

		 }
                };break;
	case 3:{
		if ($curtag=='OKPDCODE') {$okpd=$data;$flag=1;};	
		if ($curtag=='CODE') {$okpd=$data;$flag=1;};
		};break;
	case 33:{
		if ($curtag=='CODE') {$zktype=$data;$flag=1;};
		};break;

        case 99://парсим говнища
		{
		   switch ($curtag)
		 {
  			case 'OKPDCODE':$okpd=$data;break;
			case 'CODE':if ($oktmoflag==1) {$oktmo=$data;};break;
   			  case 'MAXPRICE':if ($wp<$data) {$wp=$data;}; break;
			case  'BUDGETLEVEL':$bl=$data;break;
		 };
	        };break;
	case 22:{
		  switch ($curtag)
		 {
		  case 'NS3:TOTALSUM':
		  case 'TOTALSUM':if (($itemprice<$data)&&($itemflag==1)) {$itemprice=$data;};break;		  
		  case 'MAXPRICE':
//		  case 'NS3:TOTALSUM':
//		  case 'TOTALSUM':
				if ($wp<$data) {$wp=$data;}; break;
 		  default: ;//echo "<-".$curtag."->";
		 };
                }; break;

	case 2:{
		  switch ($curtag)
		 {
		  
		  case 'MAXPRICE':
		  case 'NS3:TOTALSUM':
		  case 'TOTALSUM':if ($wp<$data) {$wp=$data;}; break;
		  case 'OOS:ORGANIZATIONNAME':
		  case 'ORGANIZATIONNAME':
		  case 'FULLNAME':if ($taglevel<6) {$fullname=$fullname.$data;}; break;
  		  case 'SHORTNAME':$orgname=$orgname.$data;break;
		  case 'OOS:INN':
		  case 'NS3:INN':
		  case 'INN':$inn=$data;break;
		  case 'OOS:CONTACTEMAIL':
		  case 'CONTACTEMAIL':$email=$data;break;
		  case 'OOS:CONTACTPHONE':
		  case 'CONTACTPHONE':$phone=$phone.$data;break;

 		  default: ;//echo "<-".$curtag."->";
		 };
	      };break;
	};
    }

function XmlNotifyParse (&$d)
{
$parser = xml_parser_create();
global $flag,$inn,$wp,$orgname,$fullname,$nno,$oktmo,$oktmoflag,$itemflag;
global $id,$rn,$taglevel,$email,$phone,$date,$lot;
global $lots,$lot,$costs,$wp,$pnames,$pname,$okpds,$okpd,$zktype,$bl,$ip,$itemprice;

$flag=0;$id='';$taglevel=0;$email='';$phone='';$date='1';$lot=0;$bl=0;$oktmoflag=0;$oktmo='';
$inn="123";$wp=0;$orgname="";$fullname="";$nno="";$rn="";$zktype="";$itemflag=0;
$lots=array();$costs=array();$pnames=array();$okpds=array();$ip=array();$itemprice=0;
 xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, true);
  // указываем какие функции будут работать при открытии и закрытии тегов
  xml_set_element_handler($parser, "startElement", "endElement");
   // указываем функцию для работы с данными
  xml_set_character_data_handler($parser,"parseData");

 xml_parse($parser, $d, true);
//echo "inn:".$inn." email:".$email." phone:".$phone." name:".$orgname." contract:".$id." rn:".$rn." purchase:".$nno."\n";
xml_parser_free($parser);
 if ($orgname=="")  {$orgname=$fullname;}
 if ($wp!=0) { if ($lot==0) {$lot=1;};
	  array_push($lots,$lot);
	  array_push($costs,$wp);
	  array_push($pnames,to1251($pname));$pname='';
	  array_push($okpds,$okpd);
	  array_push($ip,$itemprice);
};
 return array($id,to1251($nno),$lots,$costs,$pnames,$okpds,$inn,$phone,$email,to1251($orgname),$date,$zktype,$bl,$oktmo,$ip);
}
?>
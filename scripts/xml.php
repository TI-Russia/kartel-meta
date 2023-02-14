<?php 
//$parser = xml_parser_create();
global $flag;
global $cflag;
global $nno;
global $curtag;
global $sqlstr;
global $inn;
//--- customer 
global $cinn;global $cfullname;global $corgname;
global $id;
global $rn;
global $ver;
global $phone;
global $email;
global $orgname;
global $fullname;
global $taglevel;
    function startElement($parser, $name, $attrs)
    {
        global $flag;
	global $cflag;
	global $curtag;
	global $taglevel;
	$taglevel=$taglevel+1;
        if (strpos("xx".$name,'SUPPLIER')>0) {if ($flag<4) {$flag=2;} else {$flag++;};};
	if (strpos("xx".$name,'CONTACTINFO')>0) {$flag=0;}; //skip it;
        if (strpos("xx".$name,'CUSTOMER')>0) {if ($cflag<4) {$cflag=2;$flag=7;} else {$cflag++;};};
        if (strpos("xx".$name,'CONTRACT')>0) 
		{ if (strpos("xx".$name,'CONTRACTP')<1) 
		  { if (strpos("xx".$name,'CONTRACTS')<1) 
			{$flag=1;}}};
	if ($flag>0) { $curtag=$name; 
	             };
//	echo $name;
//        print_r($attrs);
    }
    function endElement($parser, $name)
    {
    global $flag;
    global $cflag;
    global $curtag;	
    global $taglevel;
    $taglevel=$taglevel-1;
    $curtag="";
    if (strpos("xx".$name,'CONTACTINFO')>0) {$flag=2;}; //stop skip it;
    if (($flag==2)
	||
	($flag==7)
	)
      {
        if (strpos("xx".$name,'SUPPLIER')>0) { $flag=4; };
//	if (strpos("xx".$name,'CONTACTINFO')>0) {$flag=2;}; //stop skip it;
        if (strpos("xx".$name,'CUSTOMER')>0) { $flag=1; };
      }	
    }

  function parseData ($parser, $data)
    {
//        global $sqlstr;
        global $curtag;
	global $price;
        global $flag;
	global $inn;
        global $cinn;global $cfullname;global $corgname;
	global $phone;
	global $email;
	global $orgname;
	global $fullname;
	global $id;
	global $rn;
	global $date;
	global $nno;	
	global $taglevel;
	global $ver;
//       echo $taglevel.'.'.$flag.'.'.$curtag.'='.$data."\n";
       switch ($flag) 
	{
	case 1:{                          
		  //echo $taglevel.':'.$curtag."\n";
		  switch ($curtag)
		 {case 'OOS:VERSIONNUMBER':
    		  case 'VERSIONNUMBER': $ver=$data;break;
		  case 'ID':
		  case 'OOS:ID':if (($id=="")&&($taglevel==3)) {$id=$data;} break;
 		  case 'OOS:REGNUM':
		  case 'REGNUM':if (($rn=="")&&($taglevel==3)) {$rn=$data;} break;
		  case 'OOS:SIGNDATE':
		  case 'SIGNDATE': $date=$data;;break;
//		  case 'PROTOCOLDATE':	$date=$data;break;
      		  case 'OOS:NOTIFICATIONNUMBER':
		  case 'NOTIFICATIONNUMBER':$nno=$data;break;
  		  case 'OOS:PRICE':
		  case 'PRICE':
 		  case 'PRICERUR':if ($price <$data) {
					$price=$data;
					}; break;

		 }
                };break;
        case 7:{
//                 print $curtag."-->".$data."\n";
		  switch ($curtag)

		 {
		  case 'OOS:INN':
		  case 'INN':$cinn=$data;break;
		  case 'OOS:ORGANIZATIONNAME':
		  case 'ORGANIZATIONNAME':
		  case 'OOS:FULLNAME':
		  case 'FULLNAME':$cfullname=$cfullname.$data;break;
  		  case 'SHORTNAME':$corgname=$corgname.$data;break;
		}
                };break;
	case 2:{
		  switch ($curtag)

		 {case 'OOS:TAXPAYERCODE':
		  case 'TAXPAYERCODE':$inn='_'.$data;break;
		  case 'OOS:INN':
		  case 'INN':$inn=$data;break;
		  case 'OOS:CONTACTEMAIL':
		  case 'CONTACTEMAIL':$email=$data;break;
		  case 'OOS:CONTACTPHONE':
		  case 'CONTACTPHONE':$phone=$phone.$data;break;
		  case 'LASTNAME':
	          case 'FIRSTNAME':
		  case 'MIDDLENAME':	
		  case 'OOS:ORGANIZATIONNAME':
		  case 'ORGANIZATIONNAME':
		  case 'FULLNAME':$fullname=$fullname." ".$data;break;
  		  case 'SHORTNAME':$orgname=$orgname.$data;break;
  		  case 'OOS:PRICE':
 		  case 'PRICERUR':if ($price <$data) {
					$price=$data;
					}; break;
 		  default: ;//echo "<-".$curtag."=".$data."->";
		 };
	      };break;
	};
    }

function XmlContractParse (&$d)
{
$parser = xml_parser_create();
global $flag;
global $inn;
global $cinn;global $cfullname;global $corgname;
global $orgname;
global $fullname;                          
global $phone;
global $email;
global $price;
global $nno;
global $id;
global $rn;
global $ver;
global $date;
global $taglevel;                                                    
$cinn='';$cfullname='';$corgname='';$date='';
$flag=0;$sqlstr='';$id='';$taglevel=0;$ver='';$price='';
$inn="";$orgname="";$phone="";$email="";$fullname="";$nno="";$rn="";
 xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, true);
  // указываем какие функции будут работать при открытии и закрытии тегов
  xml_set_element_handler($parser, "startElement", "endElement");
   // указываем функцию для работы с данными
  xml_set_character_data_handler($parser,"parseData");

 xml_parse($parser, $d, true);
 if ($orgname=="")  {$orgname=$fullname;}
 if ($corgname=="")  {$corgname=$cfullname; };
// echo "cinn:".$cinn."corgname:".$corgname;
 //echo "inn:".$inn." email:".$email." phone:".$phone." name:".$orgname." contract:".$id." rn:".$rn." purchase:".$nno."\n";
xml_parser_free($parser);
// echo $price."\n" ;
 return array(to1251($inn),to1251(trim($orgname)), to1251($email), to1251(trim($phone)),to1251($nno),to1251($id),to1251($rn),$price,$ver,$flag,$date,$cinn,to1251($corgname),'ppz');
//xml_parser_free($parser);
//$da = new SimpleXMLElement($d);
//echo ( $da->contract -> publishDate );
//var_dump(json_decode(json_encode($da), true));
}
?>
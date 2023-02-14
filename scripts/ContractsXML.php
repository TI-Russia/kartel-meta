<?php 
//$parser = xml_parser_create();
//global $flag;global $cflag;global $nno;
//global $curtag;global $sqlstr;global $inn;
//--- customer 
//global $cinn;global $cfullname;global $corgname;
//global $id;global $rn;global $ver;global $phone;global $email;global $orgname;
//global $fullname;global $taglevel;
    function startElement($parser, $name, $attrs)
    {
        global $flag,$cflag,$curtag,$taglevel;
	$taglevel=$taglevel+1;
        if (strpos("xx".$name,'SUPPLIER')>0) {if ($flag<4) {$flag=2;} else {$flag++;};};
	if (strpos("xx".$name,'CONTACTINFO')>0) {$flag=0;}; //skip it;
        if (strpos("xx".$name,'PRICEINFO')>0) {$flag=9;}; //price info
        if (strpos("xx".$name,'PRODUCT')>0) {$flag=8;}; //product info
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
    global $flag,$cflag,$curtag,$taglevel;
    $taglevel=$taglevel-1;
    $curtag="";
    if (strpos("xx".$name,'CONTACTINFO')>0) {$flag=2;}; //stop skip it;
    if (strpos("xx".$name,'PRICEINFO')>0) {$flag=1;}; //stop skip it;
    if (strpos("xx".$name,'PRODUCT')>0) {$flag=1;}; //product info
    if (($flag==2)||($flag==7))
      {
        if (strpos("xx".$name,'SUPPLIER')>0) { $flag=4; };
//	if (strpos("xx".$name,'CONTACTINFO')>0) {$flag=2;}; //stop skip it;
        if (strpos("xx".$name,'CUSTOMER')>0) { $flag=1; };
      }	
    }

  function parseData ($parser, $data)
    {
//        global $sqlstr;
        global $curtag,$price,$flag,$inn,$cinn,$cfullname,$corgname,$lotno;
	global $phone,$email,$orgname,$fullname,$id,$rn;
	global $date,$nno,$taglevel,$ver;
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
		  case 'OOS:LOTNUMBER':
                  case 'LOTNUMBER':$lotno=$data;break;
      		  case 'OOS:NOTIFICATIONNUMBER':
		  case 'NOTIFICATIONNUMBER':$nno=$data;break;
  		  case 'OOS:PRICE':
		  case 'PRICE':
 		  case 'PRICERUR':if ($price <$data) {
					$price=$data;
					}; break;
 		  }
                };break;
        case 9:{switch ($curtag)
		{
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
global $inn,$cinn,$cfullname,$corgname,$lotno,$orgname,$fullname,$phone,$email,$price,$nno;
global $id,$rn,$ver,$date,$taglevel;                                                    
$cinn='';$cfullname='';$corgname='';$date='';$lotno=1;
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
  xml_parser_free($parser);
  // echo $price."\n" ;
 return array(to1251($inn),to1251(trim($orgname)), to1251($email), to1251(trim($phone)),to1251($nno),to1251($id),to1251($rn),$price,$ver,$flag,$date,$cinn,to1251($corgname),$lotno,'ppz');
}
?>
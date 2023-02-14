<?php 
/*global $flag;
global $nno; //purchasenumber
global $curtag;
global $inn;
global $appd;
global $INNt;
global $wp;
global $fp;
global $rn;  //rn - флаг что мы внутри цены
global $orgname;
global $fullname;
global $taglevel;
*/
    function startEf1lement($parser, $name, $attrs)
    {
//	echo 'open:'.$name."\n";
        global $flag;
	global $curtag;
	global $taglevel;
	global $rn;
	$taglevel=$taglevel+1;
	if (($name=='NS9:APPREJECTEDREASONINFO')&&($flag<60)) {$flag=100+$flag;};
	if (($name=='APPREJECTEDREASON')&&($flag<60)) {$flag=100+$flag;};
	if ((($name=='NS9:APPLICATIONINFO')||($name=='APPLICATION'))
		&&($flag>80)) {$flag=$flag-100;};
	if (($name=='NS9:APPADMITTEDINFO')&&($flag<20)) {$flag=12;};
//        if ((strpos("xx".$name,'PROTOCOLLOT')>0)
//		||(strpos("xx".$name,'PROTOCOLINFO')>0)) 
//		{ if (($flag==40)||($flag==12)) {$flag=40;} else {$flag=2;};};
        if (strpos("xx".$name,'EF1')>0) { $flag=1;};
                                                               
//echo 'open:'.$name.":".$flag."\n";
	if ($flag>0) { $curtag=$name; 
	             };
//	
//echo $name;
//        print_r($attrs);
    }
/*    function addrecord()
    {   global $curtag,$taglevel,$flag;
	global $fp,$rn,$nno;
	global $lot,$appd,$appno,$apprate,$price,$admit;
	global $lots,$ad,$an,$ar,$wp,$adm;
	array_push($lots,$lot);
	array_push($ad,$appd);   $appd="";
	array_push($an,$appno);  $appno="";
	array_push($ar,$apprate);$apprate=0;
	array_push($wp,$price);  $price=0;
	array_push($adm,$admit); $admit="";

    };
*/
    function endEf1Element($parser, $name)
    {
    global $flag;
    global $curtag;	
    global $taglevel;
    global $appd;
    global $rn;
    $taglevel=$taglevel-1;
    $curtag="";
   if (strpos("xx".$name,'APPLICATION')>0) 
{ //post new record
    if ($appd!='') { addrecord();}
};

   if (strpos("xx".$name,'COSTCRITERIONINFO')>0) {$rn=0;};
   if (strpos("xx".$name,'CONTRACTCONDITION')>0) {$rn=0;};
    if ($flag==2)
      {
     if (strpos("xx".$name,'PROTOCOLLOT')>0) 
         { $flag=0; };
      }	
    }

  function parseEf1Data ($parser, $data)
    {
        global $curtag,$taglevel,$flag,$protocolnumber;
	global $fp,$rn,$nno;
	global $lot,$appd,$appno,$apprate,$price,$admit;


//       if (strpos("xx".$curtag,'NS3:')>0) 
//	{ $curtag=substr($curtag,4); //	  echo $curtag."\n";
//        };

//       if (strpos("xx".$curtag,'NS7:')>0) 
//	{ $curtag=substr($curtag,4); //	  echo $curtag."\n";
//        };
//if ($flag>160)
//{ echo $rn.':'.$flag.':'.$taglevel.':'.$curtag.':'.$data."\n";};
       switch ($flag) 
	{
	case 110:
	case 101:
        case 112:
	case 150:
	case 140:
		{ 
		switch ($curtag)
		 {
		 case 'NS4:NAME':
		 case 'REASON': if ($appno!=""){
	   					$admit=to1251($data);
						};break;
		 };
		};break;
       case 12:{
		  switch ($curtag)
		 {
 		  case 'LOTNUMBER':$lot=$data;break;	
		  case 'NS9:APPDT':
		  case 'NS7:APPDT':
  	          case 'APPDATE':$appd=$data;break;
	          case 'NS9:APPNUMBER':$appno=$data;break;
	          case 'NS9:APPRATING':$apprate=$data;break;
  	          case 'NS9:CODE':
		  case 'CRITERIONCODE':if ($data=='CP') {$rn=1;};break;
		  case 'INN':
		  case 'NS3:INN': $INNt=$data;break;
		  case 'OOS:ORGANIZATIONNAME':
		  case 'ORGANIZATIONNAME':
		  case 'NS3:SHORTNAME':$name=$data;break;
		  case 'NS9:FINALPRICE':$price=$data;
		  case 'NS9:OFFER':
		  case 'OFFER':
		  case 'NS9:OFFER':
		  case 'NS7:OFFER':if ($rn==1) 
				{ $price=$data;	};
			break;	
		 }
	      };break;
	case   1:
	case   10:
	case   50:
	case   40:{
		  switch ($curtag)
		 {
 		  case 'LOTNUMBER':$lot=$data;break;
		  case 'NS9:APPDT':
	          case 'APPDATE':$appd=$data;break;
		  case 'JOURNALNUMBER':
	          case 'NS9:APPNUMBER':$appno=$data;break;
	          case 'NS9:APPRATING':$apprate=$data;break;
		  case 'NS9:FINALPRICE':
		  case 'NS9:PRICE':    $price=$data;break;
		  case 'NS9:PROTOCOLNUMBER':
		  case 'PROTOCOLNUMBER':$protocolnumber=$data;break;
		  case 'NS9:PURCHASENUMBER':
      		  case 'PURCHASENUMBER':$nno=$data;break;

 		  default: ;//echo "<-".$curtag."->";
		 };
	      };break;
	};
    }
                                                
function XmlProtocolEf1Parse (&$d)
{
$parser = xml_parser_create();
global $flag,$nno,$protocolnumber,$protocolef2;
//arrays
	global $lot,$appd,$appno,$apprate,$price,$admit; //locals
	global $lots,$ad,$an,$ar,$wp,$adm;               //arrays

$admit="";$protocolnumber='';$protocolef2='';$nno='';
$lots=array();$ad=array();$an=array();$ar=array();$wp=array();$adm=array();
$flag=0;$price='';$taglevel=0;$appd="";$appno="";$apprate=0;$lot=1;$admit='';
  xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, true);
  xml_set_element_handler($parser, "startEf1lement", "endEf1Element");
  xml_set_character_data_handler($parser,"parseEf1Data");
  xml_parse($parser, $d, true);
  xml_parser_free($parser);
 return array(to1251($nno),$lots,$an,$ad,$ar,$wp,$adm,to1251($protocolnumber),to1251($protocolef2));
}
?>
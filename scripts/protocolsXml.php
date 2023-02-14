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
    function addrec()
    {   global $curtag,$taglevel,$flag;
	global $fp,$rn,$nno,$an,$appno,$ar,$apprate;
	global $INNt,$fp,$appd,$lot,$name,$admitted;
	global $lots,$ad,$wp,$inn,$admit,$oname;
//	echo '>>>'.$curtag.'--->'.$data.'<<<<';
	if (strlen($INNt)>0)
	{
	array_push($admit,to1251($admitted));
	array_push($inn,$INNt);
	array_push($wp,$fp);
	array_push($ad,$appd);
	array_push($lots,$lot);
	array_push($oname,to1251($name));
	array_push($an,$appno);
	array_push($ar,$apprate);$apprate=0;
	};
	$admitted="";$INNt="";$fp="";$appd="";$name="";
	$appno="";$apprate=0;

    };

    function startElement($parser, $name, $attrs)
    {
        global $flag;
	global $curtag,$apprate;
	global $taglevel;
	global $rn;
	$taglevel=$taglevel+1;
        if (strpos("xx".$name,'NS8:')>0) {$name=substr($name,4);};
	if (($name=='APPREJECTEDREASON')&&($flag<80)) {$flag=80+$flag;};
	if ($name=='NS7:ADMISSIONRESULTINFO') {$rn=1;};
	if ($name=='NS7:APPADMITTEDINFO') {$rn=1;};
	if ($name=='ADMITTEDINFO') {$rn=1;};
        if ($name=='NS3:INDIVIDUALPERSONRFINFO') {$flag=160+$flag;};
	if (($name=='APPLICATION')&&($flag>80)) {$flag=$flag-80;};
        if ((strpos("xx".$name,'PROTOCOLLOT')>0)
		||(strpos("xx".$name,'PROTOCOLINFO')>0)) 
		{ if (($flag==10)||($flag==12)) {$flag=12;} else {$flag=2;};};
        if (strpos("xx".$name,'EF1')>0) { $flag=1;};
        if (strpos("xx".$name,'EF3')>0) { $flag=1;};
        if (strpos("xx".$name,'EFS')>0) { $flag=1;$apprate=1; };
        if (strpos("xx".$name,'EF2')>0) { $flag=1;};
        if (strpos("xx".$name,'EF2020FI')>0) { $flag=1;};
	if (strpos("xx".$name,'COSTCRITERIONINFO')>0) {$rn=1;};
	if (strpos($name,'PPRF615PRO')>0) {$flag=1;};                                                               
	if (strpos($name,'LZK')>0) {$flag=1;};
	if (strpos($name,'LEZK')>0) {$flag=1;};
        if (strpos($name,'LOK1')>0) {$flag=10;};              //особый вид протоколов с оценками.
        if (strpos($name,'LOK2')>0) {$flag=10;};              //особый вид протоколов с оценками.
        if (strpos($name,'LEOK3')>0) {$flag=10;};              //особый вид протоколов с оценками.

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
    global $taglevel,$apprate;
    global $rn;
    $taglevel=$taglevel-1;
    $curtag="";
   
   if (strpos("xx".$name,'COSTCRITERIONINFO')>0) {$rn=0;};
   if (strpos("xx".$name,'CONTRACTCONDITION')>0) {$rn=0;};
   if ($name=='NS3:INDIVIDUALPERSONRFINFO') {$flag=$flag-160;};
    if ($flag>0)
      {
     if ((strpos("xx".$name,'APPLICATION')>0)||(strpos("xx".$name,'PROTOCOLLOT')>0))
         { 
	   if (($flag==92)||($flag==12)) $flag=12; else 
	   if (($flag==82)||($flag==2)) $flag=2;else 	$flag=0; 
	   addrec();	
	 };
      }	
    }

  function parseData ($parser, $data)
    {
        global $curtag,$protocolnumber,$protocolef2;
        global $flag;
	global $inn,$INNt,$admitted,$oname,$name;
	global $wp,$ad,$appd,$lot,$lots,$admit,$appno,$apprate;
	global $fp,$rn,$nno,$taglevel;
       if (strpos("xx".$curtag,'NS8:')>0) {$curtag=substr($curtag,4);};
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
	case 81:
        case 82:
	case 90:
	case 92:
		{ 
		switch ($curtag)
		 {
		 case 'REASON': if ($INNt!=""){
				$admitted=$data;addrec();}; 
				break;
		 };
		};break;
 	case 10:
       	case 1:{                          
		  switch ($curtag)
		 {
		  case 'NS9:PROTOCOLNUMBER':
       		  case 'PROTOCOLNUMBER':$protocolef2=$data;break;
		  case 'NS9:FOUNDATIONPROTOCOLNUMBER':
		  case 'FOUNDATIONPROTOCOLNUMBER':$protocolnumber=$data;break;

      		  case 'NS7:PURCHASENUMBER':
      		  case 'NS9:PURCHASENUMBER':
      		  case 'OOS:PURCHASENUMBER':
		  case 'PURCHASENUMBER':$nno=$data;break;
		 }
                };break;
       case 12:{
		  switch ($curtag)
		 {
 		  case 'LOTNUMBER':$lot=$data;break;	
	          case 'APPRATING':$apprate=$data;break;
		  case 'NS7:APPNUMBER':
     		  case 'JOURNALNUMBER':$appno=$data;break;
		  case 'NS9:APPDT':
		  case 'NS7:APPDT':
  	          case 'APPDATE':$appd=$data;break;
		  case 'CRITERIONCODE':if ($data=='CP') {$rn=1;};break;
		  case 'INN':
		  case 'NS3:INN': $INNt=$data;break;
		  case 'OOS:ORGANIZATIONNAME':
		  case 'ORGANIZATIONNAME':
		  case 'NS3:SHORTNAME':$name=$data;break;
	          case 'ADMITTED':
		  case 'NS7:ADMITTED': if ($rn==1) {$admitted="true";$rn=0;};break;
		  case 'OFFER':
		  case 'NS7:OFFER':if ($rn==1)
				{ 
				//echo '!!!offer:'.$INNt.':'.$data.':'.$appd.':'.$lot."\n";
				$fp=$data;$rn=0; //addrec();
			};
			break;	
		 }
	      };break;
	case 162:
	case 172:
	case   2:{
		  switch ($curtag)
		 {
 		  case 'LOTNUMBER':$lot=$data;break;
		  case 'NS7:APPNUMBER':
     		  case 'JOURNALNUMBER':$appno=$data;break;
	          case 'APPNUMBER':$appno=$data;break;
                  case 'RESULTTYPE':if ($data=="WIN_OFFER") {$apprate=1;}; 
				    if ($data=="SECOND_OFFER") {$apprate=2;}; 
				break;
	          case 'APPRATING':$apprate=$data;break;

		  case 'NS9:APPDT':
		  case 'NS7:APPDT':
	          case 'APPDATE':$appd=$data;break;
		  case 'ADMITTED':$admitted="true";break;
		  case 'NS7:ADMITTED': if ($rn==1) {$admitted="true";$rn=0;};break;
		  case 'NS3:MIDDLENAME':
		  case 'NS3:FIRSTNAME':
		  case 'NS3:LASTNAME':
					if ($flag>160) 
					{
					 if ($data!='-') {
					 $name=trim($name." ".$data);};
					};break;
//		  case 'INN':
		  case 'NS3:INN':
//				echo '!!!!!Ns3:INN:'.$data.':'.$fp.':'.$appd.':'.$lot."\n";
			        if ($fp!="")
				{ $INNt=$data;addrec();
				} else {$INNt=$data;};
				break;
		  case 'OOS:INN':
		  case 'INN':$INNt=$data;//array_push($inn,$data);
				break;
 		  case 'FINALPRICE':
		  case 'NS9:FINALPRICE': //keep;
		  case 'NS7:FINALPRICE': //keep;
				$fp=$data;break;				

		  case 'WINNERPRICE':if ($apprate==0) {$apprate=2;};
		  case 'PRICE':
       				//echo 'Winnerprice:'.$INNt.':'.$data.':'.$appd.':'.$lot."\n";
				$admitted='true';$fp=$data;
			//	addrec();
			break;

		  case 'OOS:ORGANIZATIONNAME':
		  case 'ORGANIZATIONNAME':
		  case 'NS3:SHORTNAME':$name=$data;break;
 		  default: ;//echo "<-".$curtag."->";
		 };
	      };break;
	};
    }
                                                
function XmlProtocolParse (&$d)
{
$parser = xml_parser_create();
global $flag;
//arrays
global $inn,$wp,$ad,$lots,$protocolnumber,$protocolef2;
global $nno,$fp,$rn,$lot,$an;
global $appd,$ad,$appno,$ar;
global $admitted,$admit,$oname,$name,$apprate;
$admitted="";$name="";$appno="";
$admit=array();$oname=array();$an=array();$ar=array();$apprate=0;
$flag=0;$fp='';$taglevel=0;$appd="";
$inn=array();$wp=array();$ad=array();$lots=array();$nno="";$rn="";$lot=1;
$protocolnumber='';$protocolef2='';
  xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, true);
  xml_set_element_handler($parser, "startElement", "endElement");
  xml_set_character_data_handler($parser,"parseData");
  xml_parse($parser, $d, true);
  xml_parser_free($parser);
 $d=array($inn,to1251($nno),$wp,$ad,$lots,$admit,$oname,'','',$an,$ar,to1251($protocolnumber),to1251($protocolef2));
// print_r($d);
 return $d;
}
?>
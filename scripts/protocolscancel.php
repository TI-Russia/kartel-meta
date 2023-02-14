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
    function startCancelElement($parser, $name, $attrs)
    {
//	echo 'open:'.$name."\n";
        global $flag;
	global $curtag;
	global $taglevel;
	global $rn;
	$taglevel=$taglevel+1;
        if (strpos("xx".$name,'CANCEL')>0) { $flag=1;};
	if ($flag>0) { $curtag=$name; 
	             };
//	
//echo $name;
//        print_r($attrs);
    }
    function endCancelElement($parser, $name)
    {
    global $curtag;	
    global $taglevel;
    $taglevel=$taglevel-1;
    $curtag="";
    }

  function parseCancelData ($parser, $data)
    {
        global $flag,$curtag,$taglevel,$protocolnumber,$protocolef2;
	global $fp,$rn,$nno;

       switch ($flag) 
	{
	case   1:
		  switch ($curtag)
		 {
//	foundationProtocolNumber
		  case 'NS9:CANCELEDPROTOCOLNUMBER':
		  case 'NS9:PROTOCOLNUMBER':
		  case 'PROTOCOLNUMBER':if (strlen($protocolnumber)<strlen($data)) 
						{$protocolnumber=$data;}; break;

		  case 'NS9:FOUNDATIONPROTOCOLNUMBER':
		  case 'FOUNDATIONPROTOCOLNUMBER':$protocolef2=$data;break;
		  case 'NS9:PURCHASENUMBER':
      		  case 'PURCHASENUMBER':$nno=$data;break;

 		  default: ;//echo "<-".$curtag."->";
		 };break;
	      };
	};
   
                                                
function XmlProtocolCancelParse (&$d)
{
$parser = xml_parser_create();
global $flag,$nno,$protocolnumber,$protocolef2;
//arrays
$nno='';$protocolnumber='';$protocolef2='';
  xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, true);
  xml_set_element_handler($parser, "startCancelElement", "endCancelElement");
  xml_set_character_data_handler($parser,"parseCancelData");
  xml_parse($parser, $d, true);
  xml_parser_free($parser);
 return array(to1251($nno),to1251($protocolnumber),to1251($protocolef2));
}
?>
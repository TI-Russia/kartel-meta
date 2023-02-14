<?php
 $dir="";
 include 'filer.php';
 include 'protocolsxml.php';
 include 'sqlprotocol.php';
 $sql_id=sql_connect();
//$fn1='contract_1120000073019000020_48325426.xml';
//$fn='contract_0108000000114000001_14129038.xml';
$fn='fcsProtocolEF3_0176200005515002329_7573878.xml';
$fn='fcsProtocolEF3_0177200000920000110_27644241.xml';
$fn='n/!/fcsProtocolPRO_0172300011519000007_23143454.xml';
$fn='n/!/fcsProtocolPPI_0172300005619000010_23082910.xml';
$fn='n/plots/fcsProtocolOK1_0176200005515001044_5288262.xml';
//$fn='n/plots/fcsProtocolOK2_0176200005515001044_5317465.xml'; // 1 обиженный
//$fn='n/bug/fcsProtocolEF3_0119200000119009970_26130546.xml';
//$fn='n/plots/fcsProtocolOK1_0176200005515001044_5288262.xml';
//$fn='n/p3/fcsProtocolOKOU2_0176300001814000110_224920.xml';
 $fp = fopen($fn, "r");
 $d = fread($fp,filesize($fn));
// echo $d;
 $fd=XmlProtocolParse($d);
 print_r($fd);//echo $fd[1];
// exec_sql($sql_id,$fd);
 sql_close($sql_id);
?>
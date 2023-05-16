<?php          	
 
 include "filer.php";
 include "sql.php";
//print_r($_GET);

$oid=0;$phone="";$email="";
if(count($_GET) == 0) 
  {$go='form';$params='';$uch=''; } 
else
{ 
$id=getparm('id');
$oid=getparm('oid');
$phone=to1251(getparm('phone'));
$email=to1251(getparm('email'));
$price=getparm('price');
$reg=getparm('region');
$coinn=getparm('cinn');
$coid='';
}
//include "header.php";


 $db=sql_connect();
 $p = '';
if ($coinn!='')
   { $sql="select oid,name from orgs where inn='".$coinn."';";
     $stmt = sqlsrv_query ($db, $sql);
      if( $stmt === false ) 
		{  echo ($sql.'<br>');
			    die(toutf(sqlsrv_errors()[0][2]));
                }
// echo $sql."<br>";
// echo "<table border=1 cellspacing=0 cellpadding=0>";
while($row = sqlsrv_fetch_array($stmt)) {
    $coid=$row[0];$coidname=toutf($row[1]);
   }

 sqlsrv_free_stmt($stmt);
}
// print_r($db);
//echo 'email:'.$email.'<br>';
$sql="";

       
	$sql='select purchasenumber,maxprice,discount,name,date from zakupki_work.dbo.purchasesLT where coid='.$coid.' order by date;' ;
//echo $sql;
 $stmt = sqlsrv_query ($db, $sql);
  if( $stmt === false ) { echo ($sql.'<br>'); die(toutf(sqlsrv_errors()[0][2])); }
  $rs=array();
// echo $sql."<br>";
while($row = sqlsrv_fetch_array($stmt)) {
	$d='';
	if ($row[4]!=NULL) {$d=$row[4]->format( 'd-m-Y' );};

		array_push($rs,array("Purchasenumber"=>$row[0],"maxprice"=>$row[1],
		"finalprice"=>$row[2], "name"=>toutf($row[3]),"date"=>$d
)
		);
//echo toutf($row[3]);
};

  sqlsrv_free_stmt($stmt);

 print(json_encode($rs));
 sql_close($db);
?>
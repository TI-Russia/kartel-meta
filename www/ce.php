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
include "header.php";


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

if (($oid=="")&&($phone=="")&&($email=="")&&($price=="")&&($cinn=""))
   {    
	$sql='select * from orgs';
	echo "<tr><td> Картель</td><td>к-во участников</td></tr>";
   } else
    {
     if ($oid>0) {$sql="select * from zakupki_work.dbo.contractsLT where oid=".$oid;
//echo $sql."<br>";
echo '<table border=2><tr><td><center><table width=100% border=1><tr><td> Название: <a href=orgsex.php?oid='.$oid.'>'.sql_getorgname($db,$oid).'</a></td><td> ИНН: '.sql_getorgINN($db,$oid).'</tr></table></td></tr><tr><td>';
}
//    else { $sql="select * from contracts where contactphone='".$phone."'";};
else if ($phone!='') { $sql="select [id],[contractsLT].[oid],[contractnumber],[contactphone],[contactemail],[purchasenumber],[name],[inn],[price],[date],[ver],[coid] from zakupki_work.dbo.contractsLT join orgs on orgs.oid=contractsLT.oid where contactphone='".$phone."'";}
else if ($email!='') { $sql="select [id],[contractsLT].[oid],[contractnumber],[contactphone],[contactemail],[purchasenumber],[name],[inn],[price],[date],[ver],[coid] from zakupki_work.dbo.contractsLT join orgs on orgs.oid=contractsLT.oid where contactemail='".$email."'";}
else if ($coid!='')  { $sql="select [id],[contractsLT].[oid],[contractnumber],[contactphone],[contactemail],[purchasenumber],[name],[inn],[price],[date],[ver],[coid] from zakupki_work.dbo.contractsLT join orgs on orgs.oid=contractsLT.oid where coid='".$coid."'";
	echo '<table border=2><tr><td><center><table border=1><tr><td>'.$coidname.'</td></tr></table></td></tr><tr><td>';}
else if ($price!='') { $sql="select [id],[contractsLT].[oid],[contractnumber],[contactphone],[contactemail],[purchasenumber],[name],[inn],[price],[date],[ver],[coid] from zakupki_work.dbo.contractsLT join orgs on orgs.oid=contractsLT.oid where price='".$price."'";} 
else if ($id!='')    { $sql="select [id],[contractsLT].[oid],[contractnumber],[contactphone],[contactemail],[purchasenumber],[name],[inn],[price],[date],[ver],[coid] from zakupki_work.dbo.contractsLT join orgs on orgs.oid=contractsLT.oid where contractnumber='".$id."'";};

  if ($reg>0) {   $sql=$sql. " and reg=".$reg ;	
             }	

if ($sql!="") {$sql = $sql . ' order by date ';}
    };
//echo $sql.'<br>';

 $stmt = sqlsrv_query ($db, $sql);
  if( $stmt === false ) { echo ($sql.'<br>'); die(toutf(sqlsrv_errors()[0][2])); }
// echo $sql."<br>";
 echo "<table border=1 cellspacing=0 cellpadding=0>";
if ($oid!=0) {echo "<tr><td>Орг.</td><td>Контракт</td><td>Телефон</td><td>Е-мейл</td><td>Закупка</td><td>Cумма</td><td width=80>Дата</td><td>ИНН зак.</td><td>Заказчик</td></tr>";} else
             {echo "<tr><td>Орг.</td><td>Контракт</td><td>Телефон</td><td>Е-мейл</td><td>Закупка</td><td>ИНН Исп.</td><td>Исполнитель</td><td>Cумма</td><td width=80>Дата</td><td>ИНН зак.</td><td>Заказчик</td></tr>";}
while($row = sqlsrv_fetch_array($stmt)) {
//if (($oid==0)&&($phone=""))
//{
//echo "<tr><td><a href=contractsex.php?oid=".toutf($row[0]).">".toutf($row[1])."</a></td><td>".toutf($row[2])."</td></tr>";
//}else
//{
//echo "<tr><td>".toutf($row[1])."</a></td><td><a href=https://zakupki.gov.ru/epz/order/notice/ea44/view/common-info.html?regNumber=".toutf($row[2]).">Госконтракт </a></td><td>".toutf($row[3])."</td><td>".toutf($row[4])."</td></tr>";
if ($oid==0) 
	{
	$d='';
	if ($row[9]!=NULL) {$d=$row[9]->format( 'd-m-Y' );};
	$zak=$row[5];
        if ($zak!='') 
	{	$zak="<a href=https://zakupki.gov.ru/epz/order/notice/ea44/view/common-info.html?regNumber=".toutf($row[5]).">".$zak."</a>";
          } else $zak='&nbsp;';

         echo "<tr><td><a href=orgsex.php?oid=".toutf($row[1]).">".toutf($row[1])."</a></td><td><a href=https://zakupki.gov.ru/epz/contract/contractCard/common-info.html?reestrNumber=".toutf($row[2]).">".$row[2]."</a></td><td><a href=contractsex.php?phone=".rawurlencode(toutf($row[3])).">".toutf($row[3])."</a></td><td><a href=contractsex.php?email=".toutf($row[4]).">".toutf($row[4])."</a></td><td>".$zak."</td><td><a href=orgsex.php?inn=".$row[7].">".toutf($row[7])."</a></td><td>".toutf($row[6])."</td><td>".$row[8]."</td><td>".
         $d."</td><td>".
        sql_getorgINN($db,$row[11])."</td><td>".
	sql_getorgname($db,$row[11])."</td></tr>";
	} else
   {
	$d='';
	if ($row[9]!=NULL) {$d=$row[9]->format( 'd-m-Y' );};
	$zak=$row[5];
        if ($zak!='') 
	{	$zak="<a href=https://zakupki.gov.ru/epz/order/notice/ea44/view/common-info.html?regNumber=".toutf($row[5]).">".$zak."</a>";
          } else $zak='&nbsp;';
         echo "<tr><td>".toutf($row[1])."</a></td><td><a href=https://zakupki.gov.ru/epz/contract/contractCard/common-info.html?reestrNumber=".toutf($row[2]).">".$row[2]."</a></td><td><a href=contractsex.php?phone=".toutf($row[3]).">".toutf($row[3])."</a></td><td><a href=contractsex.php?email=".toutf($row[4]).">".toutf($row[4])."</a></td><td>".$zak."</td><td>".$row[8]."</td><td>".
	  $d."</td><td>".
        sql_getorgINN($db,$row[10])."</td><td>".
	sql_getorgname($db,$row[10])."</td></tr>";

   }


};
    die( print_r( sqlsrv_errors(), true));
  if ($coid!='') {echo '</td></tr>';}
echo "</table>";
// print_r($row);
 sql_close($db);
?>
<?php          	
 
 include "filer.php";
 include "sql.php";
//print_r($_GET);

$oid=0;$phone="";$email="";
if(count($_GET) == 0) 
  {$go='form';$params='';$uch=''; } 
else
{ 
$oid=getparm('oid');
$phone=getparm('phone');
$email=getparm('email');
}
 echo 'Картель бета (c) МКЦ ver 1.0'."\n<br>";
 $db=sql_connect();
 $p = '';
// print_r($db);
//echo 'email:'.$email.'<br>';
if (($oid==0)&&($phone=="")&&($email==""))
   { $sql='select * from orgs where oid='.$oid;
	echo "<tr><td> Картель</td><td>к-во участников</td></tr>";
   } else
    {
     if ($oid>0) {$sql="select * from contracts where oid=".$oid;}
//    else { $sql="select * from contracts where contactphone='".$phone."'";};
  else  if ($phone=="") 
	{
	 $sql="select [id],[contracts].[oid],[contractnumber],[contactphone],[contactemail],[purchasenumber],[name],[inn],[ver] from contracts join orgs on orgs.oid=contracts.oid where contactemail='".$email."'";
	} else
	{$sql="select [id],[contracts].[oid],[contractnumber],[contactphone],[contactemail],[purchasenumber],[name],[inn],[ver] from contracts join orgs on orgs.oid=contracts.oid where contactphone='".$phone."'";}

    };
 $stmt = sqlsrv_query ($db, $sql);
  if( $stmt === false ) {
    echo ($sql.'<br>');
    die(toutf(sqlsrv_errors()[0][2]));
  }
// echo $sql."<br>";
 echo "<table border=1 cellspacing=0 cellpadding=0>";
while($row = sqlsrv_fetch_array($stmt)) {
if (($oid==0)&&($phone=""))
{
echo "<tr><td><a href=contracts.php?oid=".toutf($row[0]).">".toutf($row[1])."</a></td><td>".toutf($row[2])."</td></tr>";
}else
{
//echo "<tr><td>".toutf($row[1])."</a></td><td><a href=https://zakupki.gov.ru/epz/order/notice/ea44/view/common-info.html?regNumber=".toutf($row[2]).">Госконтракт </a></td><td>".toutf($row[3])."</td><td>".toutf($row[4])."</td></tr>";
if ($oid==0) 
	{
         echo "<tr><td><a href=orgs.php?oid=".toutf($row[1]).">".toutf($row[1])."</a></td><td><a href=https://zakupki.gov.ru/epz/contract/contractCard/common-info.html?reestrNumber=".toutf($row[2]).">Госконтракт </a></td><td><a href=contracts.php?phone=".rawurlencode(toutf($row[3])).">".toutf($row[3])."</a></td><td><a href=contracts.php?email=".toutf($row[4]).">".toutf($row[4])."</a></td><td><a href=https://zakupki.gov.ru/epz/order/notice/ea44/view/common-info.html?regNumber=".toutf($row[5]).">Закупка </a></td><td><a href=orgs.php?inn=".$row[7].">".toutf($row[7])."</a></td><td>".toutf($row[6])."</td><td>".$row[8]."</td></tr>";	
	} else
   {
    echo "<tr><td>".toutf($row[1])."</a></td><td><a href=https://zakupki.gov.ru/epz/contract/contractCard/common-info.html?reestrNumber=".toutf($row[2]).">Госконтракт </a></td><td><a href=contracts.php?phone=".toutf($row[3]).">".toutf($row[3])."</a></td><td><a href=contracts.php?email=".toutf($row[4]).">".toutf($row[4])."</a></td><td><a href=https://zakupki.gov.ru/epz/order/notice/ea44/view/common-info.html?regNumber=".toutf($row[5]).">Закупка </a></td><td>".$row[8]."</td></tr>";
   }
}

};
    die( print_r( sqlsrv_errors(), true));
echo "</table>";
// print_r($row);
include "footer.php";
 sql_close($db);
?>
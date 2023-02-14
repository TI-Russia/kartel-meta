<?php          	
 
 include "filer.php";
 include "sql.php";
//print_r($_GET);

$oid=0;$phone="";$email="";
if((count($_GET) == 0) && (count($_POST)==0))
  {$go='form';$params='';$uch='';$inn='';$coinn='';$reg='';$coid='';$price='';$id='';
   $download=0;$mindate='';$maxdate='';
   $empty=1; } 
else
{ 
$id=getparm('cid');
$oid=getparm('oid');
$inn=getparm('iinn');
$phone=to1251(getparm('phone'));
$maxdate=getparm('maxdate');
$mindate=getparm('mindate');

$download=getparm('download');
$email=to1251(getparm('email'));
$price=getparm('price');
$reg=getparm('region');
$coinn=getparm('coinn');
$coid='';
$empty=0;
}
include "header.php";


 $db=sql_connect();
 $p = '';
//------------ Шапка сайта
if ($download!=1) 
{

echo '<form method="get" action="/contractsex.php">
	Телефон:<input name="phone" value="'.$phone.'" '.$inputstyle.'
	Е-мейл:<input name="email" value="'.$email.'" '.$inputstyle.'
	ИНН исполнителя:<input name="iinn" value="'.$inn.'" '.$inputstyle.'
	ИНН Заказчика:<input name="coinn" value="'.$coinn.'" '.$inputstyle.'
	Регион участия:<input name="region" value="'.$reg.'" '.$inputstyle.'
		
	<p>
	id компании:<input name="oid" value="'.$oid.'" '.$inputstyle.'
	Номер контракта<input name="cid" value="'.$id.'" '.$inputstyle.'
	Дата контракта от:<input type="date" name="mindate" value="'.$mindate.'" '.$inputstyle.
	'До:<input name="maxdate" type="date" value="'.$maxdate.'" '.$inputstyle.

        '&nbsp;&nbsp;<input type=submit value="Найти" formaction="/contractsex.php" '.$submitstyle.
        '&nbsp;&nbsp;
        <input type=button value="Cкачать результат" OnClick="document.location.href=\''.$myurl.'&download=1\';" '.$submitstyle.
'</form>';
 ob_end_flush();
} else  //download mode
 {
    header("Content-Disposition: attachment; filename=contracts_".$coinn.".xls");  
    header("Content-type: application/octet-stream");
    ob_end_clean();
    echo $xlshdr;echo $style;
 }

if ($inn!='')
   { $sql="select oid,name from orgs where inn='".$inn."';";
     $stmt = sqlsrv_query ($db, $sql);
      if( $stmt === false ) 
		{  echo ($sql.'<br>');
			    die(toutf(sqlsrv_errors()[0][2]));
                }
// echo $sql."<br>";
// echo "<table border=1 cellspacing=0 cellpadding=0>";
while($row = sqlsrv_fetch_array($stmt)) {
    $oid=$row[0];//$coidname=toutf($row[1]);
   }

 sqlsrv_free_stmt($stmt);
}

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
echo '<table border=1 cellspacing=0 cellpadding=0><tr><td><center><table width=100% border=0 cellpadding=0 cellspacing=0><tr><td><h3> Название: <a href=orgsex.php?oid='.$oid.'>'.sql_getorgname($db,$oid).'</a></h3></td><td><h3> ИНН: '.sql_getorgINN($db,$oid).'</h3></td></tr></table></td></tr><tr><td>';
}
//    else { $sql="select * from contracts where contactphone='".$phone."'";};
else if ($phone!='') { $sql="select [id],[contractsLT].[oid],[contractnumber],[contactphone],[contactemail],[purchasenumber],[name],[inn],[price],[date],[ver],[coid] from zakupki_work.dbo.contractsLT join orgs on orgs.oid=contractsLT.oid where contactphone='".$phone."'";}
else if ($email!='') { $sql="select [id],[contractsLT].[oid],[contractnumber],[contactphone],[contactemail],[purchasenumber],[name],[inn],[price],[date],[ver],[coid] from zakupki_work.dbo.contractsLT join orgs on orgs.oid=contractsLT.oid where";
                     if (strpos('xx'.$email,'%')>0) 
			{
				$sql=$sql." contactemail like '".$email."'";
			} else { $sql=$sql." contactemail='".$email."'";}

                    }
else if ($coid!='')  { $sql="select [id],[contractsLT].[oid],[contractnumber],[contactphone],[contactemail],[purchasenumber],[name],[inn],[price],[date],[ver],[coid] from zakupki_work.dbo.contractsLT join orgs on orgs.oid=contractsLT.oid where coid='".$coid."'";
	echo '<table border=2><tr><td><center><table border=1><tr><td>'.$coidname.'</td></tr></table></td></tr><tr><td>';}
else if ($price!='') { $sql="select [id],[contractsLT].[oid],[contractnumber],[contactphone],[contactemail],[purchasenumber],[name],[inn],[price],[date],[ver],[coid] from zakupki_work.dbo.contractsLT join orgs on orgs.oid=contractsLT.oid where price='".$price."'";} 
else if ($id!='')    { $sql="select [id],[contractsLT].[oid],[contractnumber],[contactphone],[contactemail],[purchasenumber],[name],[inn],[price],[date],[ver],[coid] from zakupki_work.dbo.contractsLT join orgs on orgs.oid=contractsLT.oid where contractnumber='".$id."'";};
if (($oid!='')&&($coid!=''))
    { $sql=$sql . 'and coid='.$coid.' ';};
$if='';
      $ifd1=''; if ($mindate!='') {$ifd1="date >="._sql_validate_value($mindate)." ";};
      $ifd2=''; if ($maxdate!='') {$ifd2="date <="._sql_validate_value($maxdate)." ";};
      if ($ifd1!='') {$if=$if. ' and  '.$ifd1;};
      if ($ifd2!='') {$if=$if. ' and '.$ifd2;};

  if ($reg>0) {   $sql=$sql. " and reg=".$reg ;	
             }	
if ($if!='') 
	{
		$sql=$sql.' '.$if;};
if ($id!='') {$sql=$sql." and contractnumber='".$id."'"; } ;

if ($sql!="") {$sql = $sql . ' order by date ';}
    };
//echo $sql.'<br>';

 $stmt = sqlsrv_query ($db, $sql);
  if( $stmt === false ) { echo ($sql.'<br>'); die(toutf(sqlsrv_errors()[0][2])); }

// echo $sql."<br>";
 echo "<table border=1 cellspacing=0 cellpadding=0>";
if ($oid!="") {echo "<tr><th>Орг.</th><th>Контракт</th><th>Телефон</th><th>Е-мейл</th><th>Закупка</th><th>Cумма</th><th width=70>Дата</th><th>ИНН зак.</th><th>Заказчик</th></tr>";} else
             {echo "<tr><td>Орг.</td><td>Контракт</td><td>Телефон</td><td>Е-мейл</td><td>Закупка</td><td>ИНН Исп.</td><td>Исполнитель</td><td>Cумма</td><td width=80>Дата</td><td>ИНН зак.</td><td>Заказчик</td></tr>";}
while($row = sqlsrv_fetch_array($stmt)) {
//if (($oid==0)&&($phone=""))
//{
//echo "<tr><td><a href=contractsex.php?oid=".toutf($row[0]).">".toutf($row[1])."</a></td><td>".toutf($row[2])."</td></tr>";
//}else
//{
//echo "<tr><td>".toutf($row[1])."</a></td><td><a href=https://zakupki.gov.ru/epz/order/notice/ea44/view/common-info.html?regNumber=".toutf($row[2]).">Госконтракт </a></td><td>".toutf($row[3])."</td><td>".toutf($row[4])."</td></tr>";
if ($oid=="") 
	{
	$d='';
	if ($row[9]!=NULL) {$d=$row[9]->format( 'd-m-Y' );};
	$eml=$row[4];
	if ($eml!='null') {$eml="<a href=contractsex.php?email=".toutf($row[4]).">".toutf($row[4])."</a>";} else
            $eml='&nbsp';
	$zak=$row[5];

        if ($zak!='') 
	{	$zak="<a href=https://zakupki.gov.ru/epz/order/notice/ea44/view/common-info.html?regNumber=".toutf($row[5]).">&nbsp;".$zak."</a>";
          } else $zak='&nbsp;';

         echo "<tr><td><a href=orgsex.php?oid=".toutf($row[1]).">".toutf($row[1])."</a></td>".
		"<td><a href=https://zakupki.gov.ru/epz/contract/contractCard/common-info.html?reestrNumber=".toutf($row[2]).">&nbsp;".$row[2]."</a></td>".
		"<td><a href=contractsex.php?phone=".rawurlencode(toutf($row[3])).">".toutf($row[3])."</a></td>".
		"<td>".$eml."</td><td>".$zak."</td><td><a href=orgsex.php?inn=".$row[7].">".toutf($row[7])."</a></td><td>".toutf($row[6])."</td><td>".$row[8]."</td><td>".
         $d."</td><td>".
        sql_getorgINN($db,$row[11])."</td><td>".
	sql_getorgname($db,$row[11])."</td></tr>";
	} else
   {
	$d='';
	if ($row[9]!=NULL) {$d=$row[9]->format( 'd-m-Y' );};
	$eml=$row[4];
	if ($eml!='null') {$eml="<a href=contractsex.php?email=".toutf($row[4]).">".toutf($row[4])."</a>";} else
            $eml='&nbsp';

	$zak=$row[5];
        if ($zak!='') 
	{	$zak="<a href=https://zakupki.gov.ru/epz/order/notice/ea44/view/common-info.html?regNumber=".toutf($row[5]).">&nbsp;".$zak."</a>";
          } else $zak='&nbsp;';
         echo "<tr><td>".toutf($row[1])."</a></td><td><a href=https://zakupki.gov.ru/epz/contract/contractCard/common-info.html?reestrNumber=".toutf($row[2]).">&nbsp;".$row[2]."</a></td>".
		"<td><a href=contractsex.php?phone=".toutf($row[3]).">".toutf($row[3])."</a></td><td>".
		     $eml."</td><td>".$zak."</td><td>".$row[8]."</td><td>".
	  $d."</td><td>".
        sql_getorgINN($db,$row[10])."</td><td>".
	sql_getorgname($db,$row[10])."</td></tr>";

   }


};
  if ($coid!='') {echo '</td></tr>';}
echo "</table>";
include "footer.php";
// print_r($row);
 sql_close($db);
?>
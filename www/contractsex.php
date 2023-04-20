<?php          	
 
 include "filer.php";
 include "sql.php";
//print_r($_GET);

$oid=0;$phone="";$email="";
if((count($_GET) == 0) && (count($_POST)==0))
  {$go='form';$params='';$uch='';$inn='';$coinn='';$reg='';$coid='';$price='';$id='';
   $download=0;$mindate='';$maxdate='';$purnumber='';
   $empty=1; } 
else
{ 
$id=getparm('cid');
$oid=getparm('oid');
if ($oid=='0') $oid='';
$inn=getparm('iinn');
$phone=to1251(getparm('phone'));
$maxdate=getparm('maxdate');
$mindate=getparm('mindate');
$purnumber=getparm('purnumber');
$download=getparm('download');
$email=to1251(getparm('email'));
$price=getparm('price');
$reg=getparm('region');
$coinn=getparm('coinn');
$coid=getparm('c_oid');
//$coid='';
$empty=0;
}
include "header.php";


 $db=sql_connect();
 $p = '';
//------------ Шапка сайта
if ($download!=1) 
{
echo '<div class="hdrgray">Поиск по контрактам</div>'; 
echo '<form method="get" action="contractsex.php">
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

        '&nbsp;&nbsp;<input type=submit value="Найти" formaction="contractsex.php" '.$submitstyle.
        '&nbsp;&nbsp;
        <input type=button value="Cкачать результат" OnClick="document.location.href=\''.$myurl.'&download=1\';" '.$submitstyle.
        '&nbsp;&nbsp;
        <input type=button value="Очистить форму" OnClick="document.location.href=\''.$_SERVER['PHP_SELF']."'\"".$submitstyle.
'</form><br>';
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
    $oid=$row[0];
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

$sql="select [id],[contractsLT].[oid],[contractnumber],[contactphone],[contactemail],[purchasenumber],
	o.[name],o.[inn],
	[price],[date],[ver],[coid],c.name,c.inn,c.epzorg from ".$zakupkibase.".dbo.contractsLT 
	join orgs as o on o.oid=contractsLT.oid 
	join orgs as c on c.oid=contractsLT.coid 
	where date>'2014-01-01' ";
if ($oid>0) {
echo '<table width=100% border=1 cellspacing=0 cellpadding=0><tr><td><center>
      <table width=100% border=0 cellpadding=0 cellspacing=0><tr><td><h3> Название поставщика: <a href=orgsex.php?oid='.$oid.'>'.sql_getorgname($db,$oid).'</a></h3></td><td><h3> ИНН: '.sql_getorgINN($db,$oid).'</h3></td></tr></table></td></tr><tr><td>';
};
if ($coid!='')  { 

	echo '<table width=100% border=2><tr><td><center>
	      <table width=100% border=0><tr><td>Название заказчика: <a href=orgsex.php?oid='.$coid.'>'.sql_getorgname($db,$coid).'</a></td><td><h3> ИНН: '.sql_getorgINN($db,$coid).'</h3></td></tr></table></td></tr><tr><td>';};



$if='';
      $ifd1=''; if ($mindate!='') {$ifd1="date >="._sql_validate_value($mindate)." ";};
      $ifd2=''; if ($maxdate!='') {$ifd2="date <="._sql_validate_value($maxdate)." ";};
      if ($ifd1!='') {$if=$if. ' and  '.$ifd1;};
      if ($ifd2!='') {$if=$if. ' and '.$ifd2;};
if ($oid>0)	  $if=$if. " and contractslt.oid=".$oid;
if ($price>0)     $if=$if. " and price='".$price."' ";
if ($phone>0)     $if=$if. " and contactphone='".$phone."' ";
if ($purnumber>0) $if=$if. " and purchasenumber='".$purnumber."' ";
if ($id>0) 	  $if=$if. " and contractnumber='".$id."' "; 
if ($coid>0)      $if=$if. " and coid=".$coid." ";
if ($email!='') { ;
                     if (strpos('xx'.$email,'%')>0) 
			{
				$if=$if." and contactemail like '".$email."'";
			} else { $if=$if." and contactemail='".$email."'";}

                    }
if ($reg>0) {   $if=$if. " and reg=".$reg;}	
if ($if!='') $sql=$sql.' '.$if;


if ($sql!="") {$sql = $sql . ' order by date ';}

//echo $sql.'<br>';
if (strlen($if)>0)
{ 
  $stmt = sqlsrv_query ($db, $sql);
  if( $stmt === false ) { echo ($sql.'<br>'); die(toutf(sqlsrv_errors()[0][2])); }

 echo "<table width=100% border=1 cellspacing=0 cellpadding=0>"; 
if ($oid>0)
echo "<thead><th>Контракт</th><th>Телефон</th><th>Е-мейл</th><th>Закупка</th><th>Cумма</th><th width=70>Дата</th><th>ИНН зак.</th><th>Заказчик</th></thead>";
else  
 echo "<thead><th>ИНН поставщика</th><th>Наименование </th><th>Контракт</th><th>Телефон</th><th>Е-мейл</th><th>Закупка</th><th>Cумма</th><th width=70>Дата</th><th>ИНН зак.</th><th>Заказчик</th></thead>";
while($row = sqlsrv_fetch_array($stmt)) {

 $d='';if ($row[9]!=NULL) {$d=$row[9]->format( 'd-m-Y' );};
 $eml=$row[4];
 if ($eml!='null') {$eml="<a href=contractsex.php?email=".toutf($row[4]).">".toutf($row[4])."</a>";} else
         $eml='&nbsp';
 $zak=$row[5];
        if ($zak!='') 
	{	$zak="<a href=https://zakupki.gov.ru/epz/order/notice/ea44/view/common-info.html?regNumber=".toutf($row[5]).' target="_blank">&nbsp;'.$zak."</a>";
          } else $zak='&nbsp;';

if ($oid=="")         
echo "<tr><td><a href=orgsex.php?oid=".toutf($row[1]).">".$row[7]."</td>".
		"<td>".toutf($row[6])."</td>";

 	echo	"<td><a href=https://zakupki.gov.ru/epz/contract/contractCard/common-info.html?reestrNumber=".toutf($row[2]).' target="_blank">&nbsp;'.$row[2]."</a></td>".
		"<td><a href=contractsex.php?phone=".toutf($row[3]).">".toutf($row[3])."</a></td><td>".
		     $eml."</td><td>".$zak."</td><td>".$row[8]."</td><td>".
	  $d."</td><td>".
        '<a href="https://zakupki.gov.ru/epz/organization/view/info.html?organizationCode='.$row[14].'">'.
	$row[13]."</a></td><td>".
	toutf($row[12])."</td></tr>";

   


};
  if ($coid!='') {echo '</td></tr>';}
echo "</table>";
}
if ($oid!='') echo "</table>";
if ($coid!='') echo "</table>";
include "footer.php";
 sql_close($db);
?>
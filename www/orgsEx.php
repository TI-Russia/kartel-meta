<?php
 
 include "filer.php";
 include "sql.php";
 include "header.php";
ob_end_flush();
if(count($_GET) == 0) 
  {$go='form';$params='';$inn='';$oid='';$cid='';$gid='';$sql='';$name='';$phone=''; } 
else
{ 
   $oid=getparm('oid');
   $cid=getparm('cid');
   $gid=getparm('gid');
   $inn=getparm('inn');
   $phone=getparm('phone');
   $sql='';
   $name=getparm('name');	
   $name=str_replace('+',' ',$name);
}

function findregs($db,$id)
{
 global $zakupkibase;	
 $r="<table border=1 cellpadding=0 cellspacing=0><tr>";
// $sql="select phone from finddoublephonesbyorg(".$id.")";
 $sql="select count(*) as cnt,reg from ".$zakupkibase.".dbo.contractslt where oid=".$id." group by reg";
 $stmt = sqlsrv_query ($db, $sql);
  if( $stmt === false ) {
     die( print_r( sqlsrv_errors(), true));};
  while($row = sqlsrv_fetch_array($stmt)) 
  {
 $r=$r."<tr><td width=40><a href=contractsex.php?oid=".$id."&region=".$row[1].">".$row[1]."</a></td><td width=20>".$row[0]."</td></tr>";
 };

 $r=$r."</tr></table>";
 return $r;
}


 $db=sql_connect();
 $p = '';

//------------ Шапка сайта
echo '<div class="hdrgray">Поиск по организациям</div>'; 
echo '<form method="get" action="orgsex.php">'.
//	Телефон:<input name="phone" value='.$phone.'>
//	Е-мейл:<input name="email" value='.$email.'>
'	ИНН организации:<input name="inn" value="'.$inn.'" placeholder="Поиск по ИНН" '.$inputstyle .
'	Название организации:<input name="name" value="'.$name.'" placeholder="Поиск по названию" '.$inputstyle .
'	Телефон организации:<input name="phone" value="'.$phone.'" placeholder="Поиск по телефону" '.$inputstyle .
'        <input type=submit value="Найти" formaction="orgsex.php" '.$submitstyle.
'</form>';
/*
<input type="text" name="ИНН" class="t-input js-tilda-rule js-tilda-mask " value="" placeholder="Поиск по ИНН" data-tilda-mask="9999999999" style="color:#000000; background-color:#ffffff; border-radius: 7px; -moz-border-radius: 7px; -webkit-border-radius: 7px;">
*/
if ($oid!='') {$sql="select * from orgs where oid='".$oid."'";};
if ($cid!='') {$sql="select * from orgs where cid='".$cid."'";};
if ($gid!='') {$sql="select * from orgs where gid='".$gid."'";};
if ($inn!='') {$sql="select * from orgs where inn='".$inn."'";};
if ($name!='') {$sql="select * from orgs where name like '%".to1251($name)."%'";};
if ($phone!='') {$sql="select a.[oid]
      ,[name]
      ,[inn]
      ,[email]
      ,[phone]
      ,[n_mails]
      ,[n_phones]
      ,[cid]
      ,[n_phonesLT]
      ,[contracts]
      ,[summ]
      ,[gid],cnt from 
 (select count(*) as cnt,oid as oid from ".$zakupkibase.".dbo.contractsLT where contactphone='".$phone.
	"' group by oid) as a inner join orgs on a.oid=orgs.oid order by cnt desc;";
};


 $stmt = sqlsrv_query ($db, $sql);
  if( $stmt === false ) {
    echo ($sql.'<br>');
    die(toutf(sqlsrv_errors()[0][2]));
     }
// https://zakupki.gov.ru/epz/organization/view/info.html?organizationCode=611922
$p="<table border=1 cellspacing=0 cellpadding=0>";
$p=$p."<tr><th> id </th><th>ИД Группы</th><th>ИД картеля</th><th>ИНН</th><th>Название<th align=center>Е-мейлы,<br>по которым есть связи</th><th align=center>Телефоны, <br>по которым есть связи</th><th>Кол-во<br>контрактов</th><th>На сумму</th><th>Регионы<br>присутствия</th></tr>";    
while($row = sqlsrv_fetch_array($stmt)) {
$p=$p."\n".
"<tr><td><a href=orgsex.php?oid=".$row[0].">".$row[0]."</a></td>".
	"<td align=center><a href=orgsex.php?gid=".$row[11].">".$row[11]."</td>".
	"<td align=center><a href=cartels.php?cid=".$row[7].">".$row[7]."</td><td>".toutf($row[2])."</td><td>".toutf($row[1])."</td><td>".
        findalle($db,$row[0],$cid)."</td><td>".
	findallp($db,$row[0],$cid)."</td><td align=center><a href=contractsex.php?oid=".$row[0].">".$row[9]."</a></td><td>".
	$row[10]."</td><td>".findregs($db,$row[0])."</td></tr>";

//    print_r($row);

};
$p=$p."</table>";
 sql_close($db);
echo($p);
include "footer.php";
?>
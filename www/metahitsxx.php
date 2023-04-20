<?php
 
 include "filer.php";
 include "sql.php";
 include "header.php";
 include "pager.php";
// functions part


$oid=0;

if(count($_GET) == 0) 
  {$go='form';$inn='';$pname='';$oid='';$cid='';$xoid='';$page=1;$reg=0;$order='';$sortorder=0; } 
else
{ 
   $oid=getparm('oid');
   $cid=getparm('cid');
   $gid=getparm('gid');
   $xoid=getparm('xoid');
   $page=getparm('page');
   $reg=getparm('region');
   $inn=getparm('inn');
   $pname=getparm('pname');
   if ($reg=='') {$reg=0;};
        if ($page=='') {$page=1;};
//	if ($xoid=='') {$xoid=0;};	
//	if ($oid=='') {$oid=0;};
//	if ($cid=='') {$cid=0;};
//	if ($reg=='') {$reg=0;};
	$order=getparm('order');
	$sortorder=getparm('so');
}

 $db=sql_connect();


 $p = '';
 if ($sortorder==0) {$so='';$sortorder=1;} else {$so='desc';$sortorder=0;};
 $sql='
SELECT a.foid,a.name,a.value,a.tag,b.foid,o.inn,b.name
  FROM [meta_work].[dbo].[MultiTags_z]   as a 
  inner join [meta_work].[dbo].uniquetags as b on a.value=b.value and a.tag=b.tag
  inner join zakupki.dbo.orgs as o on o.oid=b.foid
  where a.value in (select value from [meta_work].[dbo].uniquetags)'
  ;

$if='';
if ($inn>0) $if=' and o.inn=\''.$inn.'\'';
if ($pname>0) $if=$if." and o.name like '%".to1251($pname)."%'";
 $sql='
 select max(c.cnt) as name,1,c.value,tag,oid2,inn2,name2 from

(SELECT a.foid, a.name, a.value,a.tag,b.foid as oid2,o.inn as inn2 ,b.name as name2,a.cnt
  FROM [meta_work].[dbo].[MultiTags_z]   as a 
  inner join [meta_work].[dbo].uniquetags as b on a.value=b.value and a.tag=b.tag
  inner join zakupki.dbo.orgs as o on o.oid=b.foid
  where a.value in (select value from [meta_work].[dbo].uniquetags)
 and a.tag<>'."'Title' ".$if.'
) as c
  group by c.value,c.tag,c.oid2,c.inn2,c.name2 '  ;

echo '<div class="hdrgray">Сотрудники поставщиков в метаданных</div>'; 

echo '<form method="get" action="metapairs.php">'.
//	Телефон:<input name="phone" value='.$phone.'>
//	Е-мейл:<input name="email" value='.$email.'>
//'	ИНН организации:<input name="inn" value="'.$inn.'" placeholder="Поиск по ИНН" '.$inputstyle .
'	Название поставщика:<input name="pname" value="'.$pname.'" placeholder="Поиск по названию поставщика" '.inputstyle(250)
 .
'	ИНН поставщика:<input name="inn" value="'.$inn.'" placeholder="Введите инн " '.inputstyle(100) .
'        <input type=submit value="Найти" formaction="metahitsxx.php" '.$submitstyle.
        '&nbsp;&nbsp;<input type=button value="Очистить форму" OnClick="document.location.href=\''.$_SERVER['PHP_SELF']."'\"".$submitstyle.
'</form><br>';



	echo "<table border=1 cellspacing=0 cellpadding=0 width='100%'>";

echo '
 <colgroup>
       <col span="1" style="width: 10%;">
       <col span="1" style="width: 20%;">
       <col span="1" style="width: 15%;">
       <col span="1" style="width: 10%;">
       <col span="1" style="width: 40%;">
    </colgroup>';
	switch  ($order) 
	{
	case 'name':{$sql=$sql .' order by name '.$so.' ;';break;};
	case 'value':{$sql=$sql .' order by value '.$so.' ';break;};
	case 'metatag':{$sql=$sql .' order by tag '.$so.' ';break;};
	case 'inn':{$sql=$sql .' order by inn2 '.$so.' ';break;};
	case 'cname':{$sql=$sql .' order by name2 '.$so.' ';break;};
	default: ;
	}
	echo '<thead><th> <a href=metahitsxx.php?order=name>Количество заказчиков</a></td>
	<th><a href=metahitsxx.php?order=value&so='.$sortorder.'>Метаданные</a></th>
        <th><a href=metahitsxx.php?order=metatag&so='.$sortorder.'>МетаТэг</a></th>
        <th><a href=metahitsxx.php?order=inn&so='.$sortorder.'>ИНН</a></th>
        <th><a href=metahitsxx.php?order=cname&so='.$sortorder.'>Поставщик</a></th>
	</thead>';

  
// echo $sql;
 $stmt = sqlsrv_query ($db, $sql);
  if( $stmt === false ) {
    echo ($sql.'<br>');
    die(toutf(sqlsrv_errors()[0][2]));
  }
//'SELECT a.foid,a.name,a.value,a.tag,b.foid,o.inn,b.name 
while($row = sqlsrv_fetch_array($stmt)) {

 $url='meta.php?metatag='.urlencode(toutf($row[2])).'&oid='.toutf($row[4]);
echo "<tr><td>".$row[0].
    "</td><td><a href=".$url.">".substr(toutf($row[2]),0,71)."</a></td><td>".toutf($row[3])."</td><td>".
     '<a href=orgsEx.php?oid='.toutf($row[4]).'>'.$row[5]."</a></td><td>".
     toutf($row[6]).
"</td></tr>";


};
echo "</table>";
// print_r($row);
include "footer.php";
 sql_close($db);
?>
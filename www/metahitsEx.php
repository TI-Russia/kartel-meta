<?php
 
 include "filer.php";
 include "sql.php";
 include "header.php";
 include "pager.php";
// functions part


$oid=0;

if(count($_GET) == 0) 
  {$go='form';$params='';$oid='';$cid='';$xoid='';$page=1;$reg=0;$order=''; } 
else
{ 
   $oid=getparm('oid');
   $cid=getparm('cid');
   $gid=getparm('gid');
   $xoid=getparm('xoid');
   $page=getparm('page');
   $reg=getparm('region');
   if ($reg=='') {$reg=0;};
        if ($page=='') {$page=1;};
//	if ($xoid=='') {$xoid=0;};	
//	if ($oid=='') {$oid=0;};
//	if ($cid=='') {$cid=0;};
//	if ($reg=='') {$reg=0;};
	$order=getparm('order');
}

 $db=sql_connect();


 $p = '';
 $sql='SELECT a.foid,a.name,a.value,a.tag,b.foid,o.inn,b.name
  FROM [meta_work].[dbo].[uniqueTags_z]   as a 
  inner join [meta_work].[dbo].[byuser] as b on 
  a.value=b.value and 
  a.tag=b.Metatag
  inner join zakupki.dbo.orgs as o on o.oid=b.foid
  where a.value in (select value from [meta_work].[dbo].byuser)';

	echo "<table border=1 cellspacing=0 cellpadding=0>";

	switch  ($order) 
	{
	case 'name':{$sql=$sql .' order by a.name;';break;};
	case 'value':{$sql=$sql .' order by a.value';break;};
	case 'metatag':{$sql=$sql .' order by a.tag';break;};
	case 'inn':{$sql=$sql .' order by o.inn';break;};
	case 'cname':{$sql=$sql .' order by b.name';break;};
	default: ;
	}
	echo "<tr><td> <a href=metahitsEx.php?order=name>Заказчики</a></td>
	<td><a href=metahitsEx.php?order=value>Метаданные</a></td>
        <td><a href=metahitsEx.php?order=metatag>МетаТэг</a></td>
        <td><a href=metahitsEx.php?order=inn>ИНН</a></td>
        <td><a href=metahitsEx.php?order=cname>Название исполнителя</a></td>
	</tr>";

  
// echo $sql;
 $stmt = sqlsrv_query ($db, $sql);
  if( $stmt === false ) {
    echo ($sql.'<br>');
    die(toutf(sqlsrv_errors()[0][2]));
  }
//'SELECT a.foid,a.name,a.value,a.tag,b.foid,o.inn,b.name 
while($row = sqlsrv_fetch_array($stmt)) {

 $url='meta.php?metatag='.urlencode(toutf($row[2])).'&oid='.toutf($row[4]);
echo "<tr><td><a href=orgsEx.php?oid=".toutf($row[0]).">".toutf($row[1]).
    "</td><td><a href=".$url.">".toutf($row[2])."</a></td><td>".toutf($row[3])."</td><td>".
     '<a href=orgsEx.php?oid='.toutf($row[4]).'>'.$row[5]."</a></td><td>".
     toutf($row[6]).
"</td></tr>";


};
echo "</table>";
// print_r($row);
include "footer.php";
 sql_close($db);
?>
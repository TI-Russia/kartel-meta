<?php
 
 include "filer.php";
 include "sql.php";
 include "header.php";
 include "pager.php";
// functions part

function findallregs($db,$id)
{
 $r="";
 $sql="select cnt,reg from groupregs where gid=".$id." order by reg";
 $stmt = sqlsrv_query ($db, $sql);
  if( $stmt === false ) { die( FormatErrors( sqlsrv_errors()));};
  while($row = sqlsrv_fetch_array($stmt)) {
      $r=$r." ".$row[1];
    };
   sqlsrv_free_stmt($stmt);
 return $r;
}

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
if ($cid=='')
{
echo '<form method="get" action="/cartels.php">
	Регион присутствия:<input name="region" value='.$reg.'>
        <input type=submit value="Найти" formaction="/groups.php">
</form>';
} else //Шапка картеля

{

}


 $p = '';
 $limit=5;
// print_r($db);
if ($page>0) {
		echo '<strong style="color: #ff0036">Страница № ' . $page . 
		'</strong><br />'; 
		};
if (($oid=='')&&($cid=='')&&($xoid=='')) 

   {    $total=sql_getcount($db,'cartels');
        $pages=ceil($total/40);$pages=$pages++;
        if ($page>$pages) {$page=1;};
        printpages($pages,$page,$order,$reg);
	echo "<table border=1 cellspacing=0 cellpadding=0>";

	switch  ($order) 
	{
	case 'sum':{$sql='select id,oid,count,total,sum,name,comment from ft_getpagegroups_sum('.$page.',40)';break;};
	case 'cnt':{$sql='select id,oid,count,total,sum,name,comment from ft_getpagegroups_cnt('.$page.',40)';break;};
	case 'total':{$sql='select id,oid,count,total,sum,name,comment from ft_getpagegroups_total('.$page.',40)';break;};
	default: $sql='select id,oid,count,total,sum,name,comment from ft_getpagegroupsREG('.$page.',40,'.$reg.')';
	}
	echo "<tr><td> <a href=groups.php>Группа компаний</a></td><td><a href=groups.php?order=cnt>Участники</a></td><td><a href=groups.php?order=total>к-во контрактов</a></td><td><a href=groups.php?order=sum>сумма контрактов</a></td><td>Регионы присутствия</td></tr>";

   } else
    {
    echo "<table border=1 cellspacing=0 cellpadding=0>";	
     if ($xoid>0) {$sql="select * from groups where oid=".$xoid; } else
     if ($cid=='') {$sql="exec getclusterEx ".$oid;} else

     { $sql="select * from orgs where gid=".$cid;};
     };

// echo $sql;
 $stmt = sqlsrv_query ($db, $sql);
  if( $stmt === false ) {
    echo ($sql.'<br>');
    die(toutf(sqlsrv_errors()[0][2]));
  }
 
while($row = sqlsrv_fetch_array($stmt)) {
if (($oid=='')&&($cid==''))
{
echo "<tr><td><a href=groups.php?cid=".toutf($row[0]).">".toutf($row[0])."</td><td><a href=orgsex.php?gid=".toutf($row[0]).">".toutf($row[2])."</a></td><td>".toutf($row[3])."</td><td>".toutf($row[4])."</td><td>".
     findallregs($db,$row[0])."</td></tr>";
}else
{
echo "<tr><td><a href=orgsex.php?oid=".toutf($row[0]).">".toutf($row[0])."</a></td><td>".toutf($row[1])."</td><td>".toutf($row[2])."</td><td>".toutf($row[3])."</td><td>".toutf($row[4])."</td><td>".
      findallregs($db,$row[0])."</td></tr>";
}

};
echo "</table>";
// print_r($row);
include "footer.php";
 sql_close($db);
?>
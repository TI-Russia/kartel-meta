<?php
 
 include "filer.php";
 include "sql.php";
 include "header.php";
 include "pager.php";
$oid=0;
//------------ Шапка сайта

if(count($_GET) == 0) 
  {$go='form';$params='';$oid='';$cid='';$xoid='';$page=1;$reg=0;$order='';} 
else
{ 
   $oid=getparm('oid');
   $cid=getparm('cid');
   $xoid=getparm('xoid');
   $page=getparm('page');
   $order=getparm('order');
   $reg=getparm('region');
   if ($reg=='') {$reg=0;};
   if ($page=='') {$page=1;};
}

 $db=sql_connect();
//site head
if ($cid=='')
{
echo '<form method="get" action="/cartels.php">
	Регион присутствия:<input name="region" value='.$reg.'>
        <input type=submit value="Найти" formaction="/cartels.php">
</form>';
} else //Шапка картеля

{

}
function findallpp($db,$id)
{
// $r="<table border=1 cellspacing=0 cellpadding=0><tr>";
 $r="<table width=100% border=0 cellspacing=0 cellpadding=0>
	<tr><td>Номер</td><td>связи</td><td>вес</td></tr>";
 $sql="select * from finddoublephonesbyorg(".$id.")";
 $stmt = sqlsrv_query ($db, $sql);
  if( $stmt === false ) {
     die( FormatErrors( sqlsrv_errors()));};
  while($row = sqlsrv_fetch_array($stmt)) 
    {
      $r=$r."<tr><td width=140><a href=contractsex.php?phone=".rawurlencode(toutf($row[0])).">".toutf($row[0])."</td><td width=20>".$row[1]."</td><td width=30>".$row[2]."</td></tr>";
    };
   $r=$r."</tr></table>";
   sqlsrv_free_stmt($stmt);
 return $r;
}

function findallregsbyC($db,$id)
{
// $r="<table border=1 cellspacing=0 cellpadding=0><tr>";
 $r="";
 $sql="select count(*) as cnt,reg from zakupki_work.dbo.contractslt where oid=".$id." group by reg order by reg";
// $sql="select cnt,reg from cartelregs where cid=".$id." order by reg";
 $stmt = sqlsrv_query ($db, $sql);
  if( $stmt === false ) {
    echo ($sql.'<br>');
    die(toutf(sqlsrv_errors()[0][2])); };
  while($row = sqlsrv_fetch_array($stmt)) 
    {
      $r=$r." ".$row[1];
    };
   sqlsrv_free_stmt($stmt);
 return $r;
}
function getcartelinfo($db,$cid)
{
$sql='select gid,id,total,sum,NMCKsum from cartels where id='.$cid;
 $stmt = sqlsrv_query ($db, $sql);
  if( $stmt === false ) { echo ($sql.'<br>');die(toutf(sqlsrv_errors()[0][2]));};
  while($row = sqlsrv_fetch_array($stmt)) 
    { echo '<table width=100% border=1 cellspacing=0 cellpadding=0><tr>';
	  echo "<th>Ид. Картеля:<a href=orgsex.php?cid=".$row[1].">".$row[1]."</a></th>";
          echo "<th>ИД. группы компаний:<a href=orgsex.php?gid=".$row[0].">".$row[0]."</a></td>";
          echo "<th>Пересекающихся закупок: <a href=concurents.php?cid=".$row[0].">".$row[2]."</th>";
          echo "<th>по ним сумма НМЦК:".$row[4]."</th>";
          echo "<th>по ним сумма контрактов:".$row[3]."</th>";
	  echo "</tr><tr><td colspan=5>";
    };
   sqlsrv_free_stmt($stmt);
	
}

function findallregs($db,$id)
{
// $r="<table border=1 cellspacing=0 cellpadding=0><tr>";
 $r="";
 $sql="select cnt,reg from cartelregs where cid=".$id." order by reg";
 $stmt = sqlsrv_query ($db, $sql);
  if( $stmt === false ) {
    echo ($sql.'<br>');
    die(toutf(sqlsrv_errors()[0][2]));
};
  while($row = sqlsrv_fetch_array($stmt)) 
    {
      $r=$r." ".$row[1];
    };
   sqlsrv_free_stmt($stmt);
 return $r;
}


 $p = '';
 $limit=5;
if ($page>0) { echo '<strong style="color: #df0000">Страница № '.$page.'</strong><br />'; 
	     };
if (($oid=='')&&($cid=='')&&($xoid=='')) 
  { 
    if ($reg==0) {$total=sql_getcount($db,'cartels ');}
         else
  	    {   
		$total=sql_getcount($db,' cartels where (cartels.id in (select cid from cartelregs where reg='.$reg.'))');
            };
        $pages=ceil($total/40);$pages=$pages++;
        if ($page>$pages) {$page=1;};
        printpages($pages,$page,$order,$reg);
	echo "<table width=100% border=1 cellspacing=0 cellpadding=0>";
	switch  ($order) 
	{
	case 'sum':{$sql='select id,oid,count,total,sum,gid,NMCKsum,name,comment from ft_getpagecartels_sum('.$page.',40)';break;};
	case 'cnt':{$sql='select id,oid,count,total,sum,gid,NMCKsum,name,comment from ft_getpagecartels_cnt('.$page.',40)';break;};
	case 'total':{$sql='select id,oid,count,total,sum,gid,NMCKsum,name,comment from ft_getpagecartels_total('.$page.',40)';break;};
	default: $sql='select id,oid,count,total,sum,gid,NMCKsum,name,comment from ft_getpagecartelsREG('.$page.',40,'.$reg.')';
	}
	echo "<tr><th>ИД Группы</th>
		  <th> <a href=cartels.php>ИД Картеля</a></th>
		  <th><a href=cartels.php?order=cnt>Участники</a></th>
		  <th><a href=cartels.php?order=total>к-во пересечений в<br>конкурентных закупках</a></th>
		  <th>Сумма НМЦК</th> 
		  <th><a href=cartels.php?order=sum>сумма контрактов в пересекающихся закупках</a></td><td>Регионы присутствия</td></tr>";
   } else
    {
    $n=sql_get_Gid_by_Cid($db,$cid);
    echo "<a href=cartels.php>Вернуться к списку картелей</a><br>";
    getcartelinfo($db,$cid);

    echo "<table width=100% border=1 cellspacing=0 cellpadding=0>";	
    echo "<tr><th>ID компании</th><th>Название</th><th>ИНН</th><th>связи по телефону</th><th>Регионы присутствия</th></tr>";
     if ($xoid>0) {$sql="select * from cartels where oid=".$xoid; } else
//     if ($cid=='') {$sql="exec getclusterEx ".$oid;} else

     { $sql="select * from orgs where cid=".$cid;};
     };
 $stmt = sqlsrv_query ($db, $sql);
  if( $stmt === false ) {
    echo ($sql.'<br>');
    die(toutf(sqlsrv_errors()[0][2]));
  }
 
while($row = sqlsrv_fetch_array($stmt)) {
if (($oid=='')&&($cid==''))
{
echo "<tr><td><a href=orgsEx.php?gid=".$row[5].">".$row[5]."</a></td>
<td><a href=cartels.php?cid=".toutf($row[0]).">".toutf($row[0]).
     "</td><td><a href=orgsex.php?cid=".toutf($row[0]).">".toutf($row[2])."</a></td>".
     "<td><a href=concurents.php?gid=".$row[5].">".
      toutf($row[3])
      ."</a></td><td>".$row[6]."</td><td>"
      .toutf($row[4])."</td><td>".
      findallregs($db,$row[0])."</td></tr>";
}else
{
echo "<tr><td><a href=orgsex.php?oid=".toutf($row[0]).">".toutf($row[0])."</a></td><td>".toutf($row[1])."</td><td>".toutf($row[2])."</td><td>".findallpp($db,$row[0])."</td><td>".
      findallregsbyC($db,$row[0])."</td></tr>";
}

};
echo "</table>";
include "footer.php";
 sql_close($db);
?>
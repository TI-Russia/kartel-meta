<head>
<link rel="stylesheet" href="css/styles.css"> 
</head>
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


 $p = '';
 $limit=5;
// print_r($db);
if ($page>0) {
		echo '<strong style="color: #ff0036">Страница № ' . $page . 
		'</strong>'; 
		};
if (($oid=='')&&($cid=='')&&($xoid=='')) 

   {    $total=sql_getcount($db,'meta_work.dbo.ByUser');
        $pages=ceil($total/40);$pages=$pages++;
        if ($page>$pages) {$page=1;};
        printpages($pages,$page,$order,$reg);
	echo "<table border=1 cellspacing=0 cellpadding=0>";

	switch  ($order) 
	{
	case 'cnt':{$sql='select name,foid,cnt,value,metatag from meta_work.dbo.ft_getpageUsers_cnt('.$page.',40)';break;};
	case 'value':{$sql='select name,foid,cnt,value,metatag from meta_work.dbo.ft_getpageUsers_value('.$page.',40)';break;};
	default: $sql='select name,foid,cnt,value,metatag from meta_work.dbo.ft_getpageUsers('.$page.',40)' ;
	}
	echo "<tr><td> <a href=metahits.php?order=foid>Исполнители</a></td>
	<td><a href=metahits.php?order=cnt>Совпадений</a></td>
	<td><a href=metahits.php?order=value>Метаданные</a></td>
        <td><a href=metahits.php?order=MetaTag>МетаТэг</a></td>
	</tr>";

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
 $url='meta.php?metatag='.urlencode(toutf($row[3])).'&oid='.toutf($row[1]);
echo "<tr><td><a href=orgs.php?oid=".toutf($row[1]).">".toutf($row[0]).
    "</td><td><a href=".$url.">".toutf($row[2])."</a></td><td>".toutf($row[3])."</td><td>".toutf($row[4])."</td><td>".
"</td></tr>";
}else
{
echo "<tr><td><a href=orgs.php?oid=".toutf($row[1]).">".toutf($row[0])."</a></td><td>".toutf($row[1])."</td><td>".toutf($row[2])."</td><td>".toutf($row[3])."</td><td>".toutf($row[4])."</td><td>".
      findallregs($db,$row[0])."</td></tr>";
}

};
echo "</table>";
// print_r($row);
include "footer.php";
 sql_close($db);
?>
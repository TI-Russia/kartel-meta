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
  {$go='form';$params='';$inn='';$oid='';$cid='';$xoid='';$page=1;$reg='';$order='';$sortorder=0; } 
else
{ 
   $oid=getparm('oid');
   $cid=getparm('cid');
   $gid=getparm('gid');
   $xoid=getparm('xoid');
   $page=getparm('page');
   $inn=getparm('inn');
   $reg=str_replace('+',' ',getparm('name'));
//   if ($reg=='') {$reg=0;};
        if ($page=='') {$page=1;};
//	if ($xoid=='') {$xoid=0;};	
//	if ($oid=='') {$oid=0;};
//	if ($cid=='') {$cid=0;};
//	if ($reg=='') {$reg=0;};
	$order=getparm('order');
	$sortorder=getparm('so');
}

 $db=sql_connect();
echo '<div class="hdrgray">Победители с совпадающими метаданными</div>'; 
echo '<form method="get" action="orgsex.php">'.
//	Телефон:<input name="phone" value='.$phone.'>
//	Е-мейл:<input name="email" value='.$email.'>
'	ИНН организации:<input name="inn" value="'.$inn.'" placeholder="Поиск по ИНН" '.$inputstyle .
'	Название организации:<input name="name" value="'.$reg.'" placeholder="Поиск по названию поставщика" '.inputstyle(300) .
'        <input type=submit value="Найти" formaction="metahits.php" '.$submitstyle.
        '&nbsp;&nbsp;<input type=button value="Очистить форму" OnClick="document.location.href=\''.$_SERVER['PHP_SELF']."'\"".$submitstyle.
'</form>';

 $p = '';
 $limit=10;
// print_r($db);
//if ($page>0) {echo '<strong style="color: #ff0036">Страница № ' . $page . '</strong>'; };
if (($oid=='')&&($cid=='')&&($xoid=='')) 

   {      $rr='';if ($reg>0) {$rr=" where (name like '%".to1251($reg)."%')";};
	  if ($inn>0) {$rr=" where (inn like '".$inn."%')";};
	  $total=sql_getcount($db,'meta_work.dbo.ByUser'.$rr);

        $pages=ceil($total/40);$pages=$pages++;
        if ($page>$pages) {$page=1;};
	printpages($pages,$page,$order.'&so='.$sortorder.'&inn='.$inn,$reg,'name');
	if ($sortorder==0) {$so='';$sortorder=1;} else {$so='desc';$sortorder=0;};

	echo "<table border=1 cellspacing=0 cellpadding=0 width=100%>";
	
	switch  ($order) 
	{
	case 'cnt':
	case 'inn':
	case 'value':
	case 'name':	
	case 'metatag':{$sql='select inn,name,foid,cnt,value,metatag from meta_work.dbo.ft_getUsersUni'.$so.'('.$page.",40,'".to1251($reg)."','".to1251($inn)."','".$order."')" ;break;};
	default: $sql='select inn,name,foid,cnt,value,metatag from meta_work.dbo.ft_getUsersUni'.$so.'('.$page.",40,'".to1251($reg)."','".to1251($inn)."','name')" ;
	}
        if ($reg!='') $namelink='name='.$reg.'&'; else $namelink='';
        if ($inn!='') $namelink='inn='.$inn.'&'.$namelink;
	echo "<thead>
	<th> <a href=metahits.php?".$namelink."order=inn&so=".$sortorder.">ИНН поставщика</a></th>
	<th> <a href=metahits.php?".$namelink."order=name&so=".$sortorder.">Поставщик</a></th>
	<th><a href=metahits.php?".$namelink."order=cnt&so=".$sortorder.">Совпадения</a></th>
	<th><a href=metahits.php?".$namelink."order=value&so=".$sortorder.">Метаданные</a></th>
        <th><a href=metahits.php?".$namelink."order=metatag&so=".$sortorder.">МетаТэг</a></th>
	</thead>";

   };

// echo toutf($sql);
 $stmt = sqlsrv_query ($db, $sql);
  if( $stmt === false ) {
    echo ($sql.'<br>');
    die(toutf(sqlsrv_errors()[0][2]));
  }
 
while($row = sqlsrv_fetch_array($stmt)) {
 $url='meta.php?metatag='.urlencode(toutf($row[4])).'&oid='.toutf($row[2]);
echo "<tr><td>".$row[0]."</td>
	<td><a href=orgsex.php?oid=".toutf($row[2]).">".toutf($row[1]).
    "</td><td><a href=".$url.">".toutf($row[3])."</a></td><td>".toutf($row[4])."</td><td>".toutf($row[5])."</td>".
"</tr>";

};
echo "</table>";
// print_r($row);
include "footer.php";
 sql_close($db);
?>
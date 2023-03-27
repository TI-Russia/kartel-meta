<head>
<link rel="stylesheet" href="css/styles.css"> 
</head>
<?php
 include "filer.php";
 include "sql.php";
 include "header.php";
 include "pager.php";
// functions part


$oid=0;

if(count($_GET) == 0) 
  {$go='form';$params='';$cname='';$oid='';$cid='';$xoid='';$page=1;$reg='';$order='';$sortorder=0; } 
else
{ 
   $oid=getparm('oid');
   $page=getparm('page');
   $reg=str_replace('+',' ',getparm('pname'));
   $cname=str_replace('+',' ',getparm('cname'));

   if ($page=='') {$page=1;};
    $order=getparm('order');
    $sortorder=getparm('so');if ($sortorder=='') {$sortorder=0;};
}

 $db=sql_connect();
echo '<div class="hdrgray">Просмотр пар Поставщик-Заказчик</div>'; 
echo '<form method="get" action="metapairs.php">'.
//	Телефон:<input name="phone" value='.$phone.'>
//	Е-мейл:<input name="email" value='.$email.'>
//'	ИНН организации:<input name="inn" value="'.$inn.'" placeholder="Поиск по ИНН" '.$inputstyle .
'	Название поставщика:<input name="pname" value="'.$reg.'" placeholder="Поиск по названию поставщика" '.inputstyle(250)
 .
'	Название заказчика:<input name="cname" value="'.$cname.'" placeholder="Введите часть названия заказчика" '.inputstyle(250) .
'        <input type=submit value="Найти" formaction="metapairs.php" '.$submitstyle.
'</form>';

 $p = '';
 $limit=10;
// print_r($db);
//if ($page>0) {	echo '<strong style="color: #ff0036">Страница № ' . $page . '</strong>';};


         $rr='';$where='';
	 if ($reg>0) 
		{$rr=" inner join orgs as co1 on co1.oid=metapairs.poid"; $where=" (co1.name like '%".to1251($reg)."%')";
		};
	if ($cname>0)
	     { $rr=$rr ." inner join orgs as co2 on co2.oid=metapairs.coid";
		if (strlen($where)>0) $where=$where.' and ';
		$where=$where. " (co2.name like '%".to1251($cname)."%')";
		};	
	if (strlen($rr)>0) $rr=$rr.' where '.$where;
	  $total=sql_getcount($db,'meta_work.dbo.metapairs '.$rr);

        $pages=ceil($total/40);$pages=$pages++;
        if ($page>$pages) {$page=1;};
	if ($order=='') {$order='supp';};
        printpages($pages,$page,$order.'&so='.$sortorder,$reg,'pname');
	if ($sortorder==0) {$so='';$sortorder=1;} else {$so='desc';$sortorder=0;};
	echo "<table border=1 cellspacing=0 cellpadding=0>";
	
	$sql='select name,poid,cnt,metatag,coname,coid from meta_work.dbo.ft_getpairsUni2'.$so.'('.$page.",40,'".to1251($reg)."','".to1251($cname)."','".$order."')" ;
	
        if ($reg!='') $namelink='pname='.$reg.'&'; else $namelink='';
        if ($cname!='') 
 	{  //if (strlen($namelink)>0) $namelink=$namelink.'&';
 	   $namelink=$namelink.'cname='.$cname.'&';
	}
	echo '<thead><th> <a href="metapairs.php?'.$namelink.'order=supp&so='.$sortorder.'">Поставщик</a></th>
	<th><a href="metapairs.php?'.$namelink.'order=cnt&so='.$sortorder.'">Совпадения</a></th>
	<th><a href="metapairs.php?'.$namelink.'order=value&so='.$sortorder.'">Метаданные</a></th>
        <th><a href="metapairs.php?'.$namelink.'order=cust&so='.$sortorder.'">Заказчик</a></th>
	</thead>';


 $stmt = sqlsrv_query ($db, $sql);
  if( $stmt === false ) {
    echo ($sql.'<br>');
	    die(toutf(sqlsrv_errors()[0][2]));
  }
 
while($row = sqlsrv_fetch_array($stmt)) {

 $url='meta.php?metatag='.urlencode(toutf($row[3])).'&oid='.toutf($row[1]).'&coid='.toutf($row[5]);
echo "<tr><td><a href=orgsex.php?oid=".toutf($row[1]).">".toutf($row[0]).
    "</td><td><a href=".$url.">".toutf($row[2])."</a></td><td>".toutf($row[3])."</td>
 <td><a href=orgsex.php?oid=".$row[5].">".toutf($row[4])."</td><td>".
"</td></tr>";
};


echo "</table>";
// print_r($row);
include "footer.php";
 sql_close($db);
?>
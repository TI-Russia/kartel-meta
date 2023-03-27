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
  {$go='form';$params='';$oid='';$cid='';$xoid='';$page=1;$reg='';$order='';$tagname='Author';$sortorder=1; } 
else
{ 
   $oid=getparm('oid');
   $cid=getparm('cid');
   $gid=getparm('gid');
   $xoid=getparm('xoid');
   $tagname=getparm('tagtype');
   $page=getparm('page');
   if ($tagname=='') $tagname='Author';
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
$q='<input name="tagtype" type="radio" value="Author" </input>Author&nbsp;
                 <input name="tagtype" type="radio" value="Title" onchange="document.getElementById(\'formid\').submit()"</input>Title
		 &nbsp;<input name="tagtype" type="radio" value="Creator" onchange="document.getElementById(\'formid\').submit()"</input>Creator
		 &nbsp;<input name="tagtype" type="radio" value="Initial-creator" onchange="document.getElementById(\'formid\').submit()"</input>Initial-creator
		 &nbsp;<input name="tagtype" type="radio" value="Company" onchange="document.getElementById(\'formid\').submit()"</input>Company
		 &nbsp;<input name="tagtype" type="radio" value="LastAuthor" onchange="document.getElementById(\'formid\').submit()"</input>LastAuthor
		 &nbsp;<input name="tagtype" type="radio" value="LastModifiedBy" onchange="document.getElementById(\'formid\').submit()"</input>LastModifiedBy
		 &nbsp;<input name="tagtype" type="radio" value="Subject" onchange="document.getElementById(\'formid\').submit()"</input>Subject';
$l=strpos($q,'value="'.$tagname);
if ($l>0) 
$q = substr($q, 0, $l) . ' checked ' . substr($q, $l);
echo '<div class="hdrgray">Рейтинги метаданных</div>'; 
echo '<form id="formid" method="get" action="metafun.php">'.
//	Телефон:<input name="phone" value='.$phone.'>
//	Е-мейл:<input name="email" value='.$email.'>
//'	ИНН организации:<input name="inn" value="'.$inn.'" placeholder="Поиск по ИНН" '.$inputstyle .
'       Метатэг: '.$q.
'<br>	Содержание метаданных:<input name="name" value="'.$reg.'" placeholder="Поиск по содержанию метаданных" '.inputstyle(300) .
'        <input type=submit value="Найти" formaction="metafun.php" '.$submitstyle.
'</form>';

 $p = '';
 $limit=10;
// print_r($db);
//if ($page>0) {	echo '<strong style="color: #ff0036">Страница № ' . $page . '</strong>'; };
if (($oid=='')&&($cid=='')&&($xoid=='')) 

   {
$sql=" (
	select count(*) as cnt from
	meta_work.dbo.metatags where (tag ='".$tagname."' and value like '%".to1251($reg)."%') group by value) as a";

     // $rr='';if ($reg>0) {$rr=" where (tag ='".to1251($tagname)."') group by value";};
	  $total=sql_getcount($db,$sql);
        $pages=ceil($total/40);$pages=$pages++;
        if ($page>$pages) {$page=1;};
	printpages($pages,$page,$order.'&so='.$sortorder.'&tagtype='.$tagname,$reg,'name');
	if ($sortorder==0) {$so='';$sortorder=1;} else {$so='desc';$sortorder=0;};

	echo "<table border=1 cellspacing=0 cellpadding=0>";

	switch  ($order) 
	{
	case 'cnt':
	case 'value':
	case 'name':	
	case 'metatag':{$sql='select cnt,value from meta_work.dbo.ft_getRatingsUni'.$so.'('.$page.",40,'".to1251($reg)."','".$tagname."','".$order."')" ;break;};
	default:  {$sql='select cnt,value from meta_work.dbo.ft_getRatingsUni'.$so.'('.$page.",40,'".to1251($reg)."','".$tagname."','value')" ;break;};
	}
        if ($reg!='') $namelink='name='.$reg.'&'; else $namelink='';
	if ($tagname!='') $namelink='tagtype='.$tagname.'&'.$namelink;
	echo "<thead><th> <a href=metafun.php?".$namelink."order=cnt&so=".$sortorder.">Количество</a></th>
	<th><a href=metafun.php?".$namelink."order=value&so=".$sortorder.">Метаданные</a></th>
	</thead>";

   };

// echo toutf($sql);
 $stmt = sqlsrv_query ($db, $sql);
  if( $stmt === false ) {
    echo ($sql.'<br>');
    die(toutf(sqlsrv_errors()[0][2]));
  }
 
while($row = sqlsrv_fetch_array($stmt)) {
{
echo "<tr><td><a href=meta.php?metatag=".urlencode(toutf($row[1])).">".toutf($row[0])."</a></td><td>".toutf($row[1])."</td></tr>";
}

};
echo "</table>";
// print_r($row);
include "footer.php";
 sql_close($db);
?>
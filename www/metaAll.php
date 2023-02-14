<?php
 
 include "filer.php";
 include "sql.php";
 include "header.php";
$oid=0;

if(count($_GET) == 0) 
  {$go='form';$params='';$oid='';$cid='';$xcid='';$gid=''; 
   $maxlist=200;
   $cname='';		
   $mindate='';
   $maxdate='';$ptype='';
   $mindiscount='';
   $maxdiscount='';	
   $purnumber='';
   $metatag='';
   $download=0;
   $okpd='';$oid=0;$oidname='';$purname='';
   $cinn='';$iinn='';$coid=0;
   } 
else
{ 
   $xcid=getparm('xcid');
   $gid=getparm('gid');
   $maxlist=getparm('maxlist');
   if ($maxlist=='') {$maxlist=200;};
   $mindiscount=getparm('mindiscount');
   $cname=getparm('cname');
   $maxdiscount=getparm('maxdiscount');
   $okpd=getparm('okpd');
   $download=getparm('download');
   $maxdate=getparm('maxdate');
   $mindate=getparm('mindate');
   $ptype=getparm('ptype');
   $purnumber=getparm('purnumber');
   $purname=getparm('subject');
   $metatag=getparm('metatag');
   $metatag=str_replace('+',' ',$metatag);
   //delete ++ from purname
$purname=str_replace('+',' ',$purname);
   $iinn=getparm('iinn');
   $cinn=getparm('cinn');	
   $coid=getparm('coid');  if ($coid=='') {$coid=0;};
   $oid=getparm('oid');    if ($oid=='') {$oid=0;};
   if ($gid!='') {$xcid=$gid;};
}
/*
	   select * from zakupki.dbo.purchasesLt as a inner join meta_zakupki.dbo.datafiles as d on d.purchasenumber=a.purchasenumber 
	   inner join meta_zakupki.dbo.metafiles as m on d.contentid=m.contentid
	   inner join meta_zakupki.dbo.metatags as t on m.id=t.[file]
	   where a.purchasenumber in (
    select purchasenumber from
	datafiles where contentid in
	(select contentid from metafiles where id in (select [file] FROM [meta_zakupki].[dbo].[Metatags] where value='НПП "Гарант-Сервис"')))
*/
function getmetatags($db,$purchase)
{ $res='';
  $sql='select m.filename,b.tag,b.value,a.contentid
	from meta_zakupki.dbo.datafiles as a  
          inner join meta_zakupki.dbo.metafiles as m on a.contentid=m.contentid
	   inner join meta_zakupki.dbo.metatags as b on b.[file]=m.id where a.purchasenumber='."'".$purchase."'";
//echo $sql;
$stmt = sqlsrv_query ($db, $sql);
      if( $stmt === false ) 
		{  echo ($sql.'<br>');
			    die(toutf(sqlsrv_errors()[0][2]));
                }
$res='<table border=1>';
while($row = sqlsrv_fetch_array($stmt)) {
    $res=$res.'<tr><td><a href="https://zakupki.gov.ru/44fz/filestore/public/1.0/download/priz/file.html?uid='.$row[3].'">'.toutf($row[0]).'</a></td><td>'.
	 toutf($row[1]).'</td><td>'.
	 toutf($row[2]).'</td></tr>';
   }
$res=$res.'</table>';
 sqlsrv_free_stmt($stmt);
  
  return $res;
};



 $db=sql_connect();
 $p = '';
// print_r($db);
//echo $purnumber.'<br>';
echo "<table border=1 cellspacing=0 cellpadding=0>".
  getmetatags($db,$purnumber)."</td></tr>";

//    die( print_r( sqlsrv_errors(), true));
$p=$p."</table></body></html>";
echo $p;
// print_r($row);
include "footer.php";
 sql_close($db);
?>
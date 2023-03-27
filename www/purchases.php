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


 $db=sql_connect();
 $p = '';
// print_r($db);
if (($cinn!='')&&($cinn!="''")) 
   { $sql="select oid,name from orgs where inn="._sql_validate_value($cinn).";";
     $stmt = sqlsrv_query ($db, $sql);
      if( $stmt === false ) 
		{  echo ($sql.'<br>');
			    die(toutf(sqlsrv_errors()[0][2]));
                }
while($row = sqlsrv_fetch_array($stmt)) {
    $coid=$row[0];$coidname=toutf($row[1]);
   }
 sqlsrv_free_stmt($stmt);
};
if (($iinn!='')&&($iinn!="''")) 
   { $sql="select oid,name from orgs where inn="._sql_validate_value($iinn).";";
     $stmt = sqlsrv_query ($db, $sql);
      if( $stmt === false ) 
		{  echo ($sql.'<br>');
			    die(toutf(sqlsrv_errors()[0][2]));
                }
 while($row = sqlsrv_fetch_array($stmt)) { $oid=$row[0];$oidname=toutf($row[1]);}
	 sqlsrv_free_stmt($stmt);
	};

if ($download!=1) 
{
//------------ Шапка сайта
//echo ">>>>>>".htmlspecialchars($metatag)."<<<<<<";
echo '<form method="get" action="purchases.php">
	Способ закупки   :   <input name="ptype" list="ptype" value="'.$ptype.'" '.
 $inputstyle.
 $ptype_datalist.
	'Обьект закупки:<input name="subject" value="'.htmlspecialchars($purname).'" '.$inputstyle.
	'<p>Процент снижения НМЦК(Цены единицы) от:<input name="mindiscount" value="'.$mindiscount.'" '.$inputstyle.
	'До:<input name="maxdiscount" value="'.$maxdiscount.'" '.$inputstyle.
	'Номер закупки:<input name="purnumber" value="'.$purnumber.'" '.$inputstyle.
	'<p>'.
	'Дата закупки от:<input type="date" name="mindate" value="'.$mindate.'" '.$inputstyle.
	'До:<input name="maxdate" type="date" value="'.$maxdate.'" '.$inputstyle.

' ИНН Заказчика/(разместителя заказа):<input name="cinn" value="'.$cinn.'" '.$inputstyle.
' ИНН Исполнителя:<input name="iinn" value="'.$iinn.'" '.$inputstyle.'<br><br>
        Вывести список не более чем из <input name="maxlist" value="'.$maxlist.'"'.$inputstyle.' строк
        <input type=submit value="Найти" formaction="purchases.php" '.$submitstyle."\n".
        '<input type=button value="Cкачать результат" OnClick="document.location.href=\''.$myurl.'&download=1\';" '.$submitstyle.
'</form>';
 ob_end_flush();
} else  //download mode
 {
    header("Content-Disposition: attachment; filename=purchases.xls");  
    header("Content-type: application/octet-stream");
    ob_end_clean();
    echo $xlshdr;
 }
echo "<table border=1 cellspacing=0 cellpadding=0>";

   $if1=''; if (($mindiscount!='')&&($mindiscount!="''")) 
 //{$if1='discount>=maxprice*('.$mindiscount.'/100.0) ';};
   {$if1='percentsex>='.$mindiscount.' '; };
//$percent=($row[2]-$row[8])/$row[2]*100;
      $if2=''; if (($maxdiscount!='')&&($maxdiscount!="''")) 
 {$if2='percentsex<='.$maxdiscount.' '; };
//{$if2='discount<=maxprice*('.$maxdiscount.'/100.0) ';};
      $ifd1=''; if ($mindate!='') {$ifd1="a.date >="._sql_validate_value($mindate)." ";};
      $ifd2=''; if ($maxdate!='') {$ifd2="a.date <="._sql_validate_value($maxdate)." ";};
      $if3=''; if ($coid!=0) {$if3='a.coid = '.$coid;};

      if (($if1!='')&&($if2!='')) {$if =$if1. 'and '.$if2; } else
	  {$if=$if1.' '.$if2;}; 
	if ($if!=' ') {$if=$if . 'and ';};
      if ($if3!='') {$if=$if. ' '.$if3 .' and ';};
      if ($ifd1!='') {$if=$if. ' '.$ifd1.' and ';};
      if ($ifd2!='') {$if=$if. ' '.$ifd2.' and ';};
      if ($purname!='') {$if=$if. " a.name like '%".to1251($purname)."%' and ";};
      if ($okpd!='') {$if=$if. " a.okpd like '%".$okpd."%' and ";};
      if ($oid!=0)   {$if=$if. ' a.oid='.$oid.' and ';};
      if ($purnumber!='') {$if=$if . " a.purchasenumber='".$purnumber."' and ";};

      if ($ptype!='') {
        if (strpos($ptype,'%')>0) 
		{ $if=$if. " a.type like("._sql_validate_value($ptype).") and ";
		}
        else {
		$if=$if. ' a.type='._sql_validate_value($ptype).' and ';
	}

}; //     echo $if;
if (trim($if)=="") {$sql='select 1';} else	
{
//echo '>>'.$if.'<<';
      $sql="select top ".$maxlist. " * from (select
a.purchasenumber,a.coid,a.maxprice,a.date,a.status,a.cphone,a.cemail,a.lot,a.discount,a.offers,a.rejected,a.okpd,a.type,a.oid,a.name,a.percents,orgs.inn,orgs.name as oname,o2.inn as oinn,o2.name as o2name,p.sum,a.itemprice,

iif(a.itemprice>0 and 

 (((a.maxprice-a.discount)<a.itemprice)or(a.itemprice-p.sum>=0)) ,
(a.itemprice-iif(p.sum=0,iif(a.itemprice>0,a.itemprice,a.maxprice),p.sum ) )/a.itemprice*100,iif(a.maxprice=0, NULL ,(a.maxprice-iif(p.sum=0,maxprice,p.sum ))/a.maxprice*100)) as percentsex
 
 from (
 select *,(case when maxprice=0 then NULL else (maxprice-discount)/maxprice*100 end) as percents from ".$zakupkibase.".dbo.purchasesLT
 ) as a 
 inner join orgs on (a.coid=orgs.oid) left join orgs o2 on (a.oid=o2.oid) 
 left join zakupki.dbo.concurents as p on (p.purchasenumber=a.purchasenumber and p.place=1 and p.active=1 and a.lot=p.lot )
 ) as a where ".$if."  a.date > '2014-01-01' order by a.date";

//echo htmlentities($sql);
//header	
$p="<tr><th> Дата </th><th>Способ закупки</th><th>№ закупки </th><th>№ лота</th><th>Количество заявок</th><th>Отклонено</th><th>ОКПД(2)</th><th>НМЦК </th><th>Цена единицы</th><th>Цена победителя</th><th>Цена контракта</th><th>% сни-жения</th><th>Предмет закупки</th><th>ИНН Заказчика<br>(Организатора закупки)</th><th>Заказчик<br>(организатор закупки)</th><th>ИНН поставщика</th><th>Поставщик</th></tr>";
   
//echo $sql;
//0 - purchasenumber
//1 - coid
//2 - maxprice
//3 - date
//4 - status
//5 - phone
//6 - mail
//7 - lot
//8 - discount
//9 offers
//10 rejected
//11 - okpd

//12 - type
//13 - oid
//14 - name
//15 - percent
//16 - co inn
//17 - co name
//20 - цп.
//21 - itemprice
//22 - percentsx
 $stmt = sqlsrv_query ($db, $sql);
  if( $stmt === false ) {
    echo ($sql.'<br>');
    die(toutf(sqlsrv_errors()[0][2]));
  }
while($row = sqlsrv_fetch_array($stmt)) {
{
	$d='';
	if ($row[3]!=NULL) {$d=$row[3]->format( 'd-m-Y' );};
$percent='-';
if ($row[21]==0) {$row[21]='';};

$cp=$row[20];if ($cp=="") {
	$cp="<span class='c_missed'>".$row[2]."</span>";
	$row[20]=$row[2];
	};
if ($row[8]!='')
   { //$percent=$row[15];//($row[2]-$row[8])/$row[2]*100;
		
//      $percent=($row[2]-$row[20])/$row[2];
//      $percent=$percent*100; //ы	
//$percent=
      if ($row[9]==0) {$row[9]=1;}; //для пустых выигранных заявок
$percent=$row[22];		
      $percent=round(($percent)*100)/100;
	$percent=$percent.'%';	

	};
$href="<a href=meta.php?cinn=".trim($row[16]).'&metatag='.urlencode($metatag).' a>'.$row[16];
$p=$p."\n"."<tr><td>".$d. "</td><td>".
  $row[12]."</td><td>".
  "<a href=https://zakupki.gov.ru/epz/order/notice/ea44/view/common-info.html?regNumber=".toutf($row[0]). ' target="_blank">&nbsp;'.toutf($row[0])."</a></td><td>".
  $row[7]."</td><td>". //lot
  
  $row[9]."</td><td>". //offers
  $row[10]."</td><td>". //rejected
  $row[11]."</td><td>". //okpd
  toutf($row[2])."</td><td>". //nmck
  toutf($row[21])."</td><td>".
  toutf($cp)."</td><td>". //сp
  toutf($row[8])."</td><td>".
  $percent."</td><td>".
  toutf($row[14])."</td><td>".
  $href."</td><td>".		//inn 

  toutf($row[17])//sql_getorgname($db,$row[1])
  ."</td><td>".
  toutf($row[18])."</td><td>".
  toutf($row[19])."</td></tr>";
}
};
}
//    die( print_r( sqlsrv_errors(), true));
$p=$p."</table></body></html>";
echo $p;
// print_r($row);
include "footer.php";
 sql_close($db);
?>
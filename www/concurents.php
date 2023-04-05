
<?php
 
 include "filer.php";
 include "sql.php";
 include "header.php";
$oid=0;

if(count($_GET) == 0) 
  {$go='form';$params='';$oid='';$cid='';$xcid='';$mindate='';$maxdate='';$maxlist=2000;
   $mindiscount='';
   $maxdiscount='';	
   $download=0;$pn='';

} 
else
{ 
   $xcid=getparm('xcid');
   $cid=getparm('cid');
   $maxlist=getparm('maxlist');
   $pn=getparm('purchasenumber');
   if ($maxlist=='') {$maxlist=1000;};
   $mindate=getparm('mindate');
   if (strlen($mindate)<8) {$mindate='';};
   $download=getparm('download');
   $maxdate=getparm('maxdate');
   if (strlen($maxdate)<8) {$maxdate='';};
   $mindiscount=getparm('mindiscount');
   $maxdiscount=getparm('maxdiscount');
   if ($cid!='') {$gid=$cid;} else {
   $gid=getparm('gid');}

//   if ($gid!='') {$xcid=$gid;};
}

 $db=sql_connect();
 $p = '';
// print_r($db);
//------------ Шапка сайта
if ($download!=1) 
{
echo '<form method="get" action="/concurents.php">'.
	'Дата закупки от:<input type="date" name="mindate" value="'.$mindate.'" '.$inputstyle.
	'&nbsp;До:<input name="maxdate" type="date" value="'.$maxdate.'" '.$inputstyle.
	'&nbsp;ИД Группы компаний:<input name="gid" value="'.$gid.'" '.$inputstyle.'<br>'.
	'Процент снижения цены относительно НМЦК от:<input name="mindiscount" value="'.$mindiscount.'" '.$inputstyle.
	'До:<input name="maxdiscount" value="'.$maxdiscount.'" '.$inputstyle.
	'<br>'.

        'Вывести список не более чем из <input name="maxlist" value="'.$maxlist.'" '.$inputstyle.' строк
        <input type=submit value="Найти" formaction="/concurents.php" '.$submitstyle."\n".
        '<input type=button value="Cкачать результат" OnClick="document.location.href=\''.$myurl.'&download=1\';" '.$submitstyle.
'</form>';
 ob_end_flush();
} else  //download mode
 {
    header("Content-Disposition: attachment; filename=concurents.xls");  
    header("Content-type: application/octet-stream");
    ob_end_clean();
    echo $xlshdr;echo $style;
 }

 
echo '<table border=1 cellspacing=0 cellpadding=0>
<tr><th colspan=2><h3>Конкурсы в которых участники <a href=orgsex.php?gid='.$gid.'>группы</a> проявили признаки картельного сговора </h3></th></tr>';
$p="<tr><td colspan=2><table border=1 cellspacing=0 cellpadding=0>";
if (($xcid=='')and($gid=='')) 
   {
    // $sql='select count(*) as cnt,cartelid  from doubleconcurents group by cartelid ';
    //	echo "<tr><td> кол-во конкурсов </td><td>Картель </td></tr>";
    $p=$p. ' не задан ID группы компаний, поиск невозможен ';die();
   } else
    {
     { 
 //$sql="select * from doubleconcurents where cartelid=".$gid;

$if='';$if1='';$ifd1='';$ifd2=''; 
if (($mindiscount!='')&&($mindiscount!="''")) {$ifd1=' B.disc>='.$mindiscount ;};
if (($maxdiscount!='')&&($maxdiscount!="''")) {$ifd2=' B.disc<='.$maxdiscount ;};

if ($mindate!='') { $if="B.date>"._sql_validate_value($mindate)." ";};
if ($maxdate!='') { $if1="B.date<"._sql_validate_value($maxdate)." ";};
if (($if1!='')&&($if!='')) {$if=$if . ' and '.$if1;} else
    if ($if1!='') {$if=$if1;};

      if (($ifd1!='')&&($ifd2!='')) {$if2 =$ifd1. 'and '.$ifd2; } else
	  {$if2=$ifd1.' '.$ifd2;}; 

if ($if2!=' ') { if ($if!='') {$if2=' and '.$if2;};
	$if=$if .' '.$if2; }

if ($if!='') { $if=' where '.$if;};
if ($maxlist>0) { $top=' top '.$maxlist.' ';} else {$top='';};
$sql="
select ".$top."B.*,contractslt.contractnumber,contractslt.price,orgs.gid,orgs.inn,orgs.name,o2.inn,o2.name from (
	select A.date,A.purchaseNumber,A.lot,A.Maxprice,concurentsLT.sum,((A.maxprice-concurentsLT.sum)/A.maxprice*100) as disc,concurentsLT.oid,a.coid,concurentsLT.appdate,a.discount from 
	(
		select purchasesLT.date,purchasesLT.purchasenumber,purchasesLT.lot,purchasesLT.maxprice,purchasesLT.discount,purchasesLT.coid
		from DoubleConcurents inner join zakupki_work.dbo.purchasesLT on purchasesLT.purchasenumber=DoubleConcurents.purchasenumber 
  		where doubleconcurents.cartelid=".$gid.") as A  inner join concurentsLT on (concurentsLT.purchasenumber=A.purchasenumber and concurentsLT.lot=A.Lot)
                where A.purchasenumber<>'' and A.maxprice>0)  as B 
 left join zakupki_work.dbo.contractsLT on (B.oid=contractslt.oid and B.purchasenumber=contractslt.purchasenumber and B.lot=contractslt.lot) 
 inner join orgs on (B.oid=orgs.oid)
 inner join orgs O2 on (B.coid=O2.oid)
".$if." order by B.date, B.lot, disc desc";

};
    if ($xcid>0)
     { $sql="select * from concurents  join orgs on orgs.oid=concurents.oid where 
       (purchasenumber in (select purchasenumber from doubleconcurents where cartelid=".$xcid.")) 
 and(cartelid=".$xcid.") order by purchasenumber";
	$p=$p. "<tr><td> закупка </td><td>ИД организации</td><td>ИД группы</td><td>Название организации</td></tr>";
    }
     };
//echo $sql.'<br>';
//  print_r($gid);  
 $stmt = sqlsrv_query ($db, $sql);
  if( $stmt === false ) {
    echo ($sql.'<br>');
    die(toutf(sqlsrv_errors()[0][2]));
  }
//$d=((((int)$xcid)==0)&&((int)$gid==0));
//echo $xcid.'..'.$d.'||';
$middiscount=0;$n=0; $totsum=0;$totcontr=0;
$p=$p. "<tr><th>Дата</th><th>Закупка</th><th>Лот</th><th>МЦ</th><th>Итоговая цена</th><th>Цена в заявке</th><th>Время подачи заявки</th><th>% снижения</th><th>Ид группы</th><th>ИНН Участника</th><th>Участник</th><th>Контракт</th><th>ИНН заказчика<br>(разместившего заказ)</th><th>Заказчик<br>(разместивший заказ)</th></tr>";

while($row = sqlsrv_fetch_array($stmt)) {
	$d='';
	if ($row[0]!=NULL) {$d=$row[0]->format( 'd-m-Y' );
	};
	$d1='';if ($row[8]!=NULL) {$d1=$row[8]->format( 'd-m-Y h:i:s' );
 	if ($d1=='01-01-1900 12:00:00') {$d1='---';}; };
$t='<td>';
if ($row[5]==0) {$row[4]=$row[11];
		 $row[5]=($row[3]-$row[4])/$row[3];
	};
if ($row[10]!='') 
	    { $t='<td class="c_green">';} 
	else
	     {$row[9]='';};

//$isp=sql_getorgInfo($db,$row[6]);
$orgd=
"</td>".$t.$row[12].
"</td>".$t."<a href=/orgsex.php?oid=".$row[6].">".toutf($row[13])."</a>".
"</td>".$t.toutf($row[14]);

if (($row[10]!='')&&($gid==$row[12])) {
          $n++; $middiscount=$middiscount+$row[5];
		$totsum=$totsum+$row[3];
		$totcontr=$totcontr+$row[4];
	};


$p=$p."\n".
"<tr><td width='75pt'>".$d."</td>".$t."<a href=https://zakupki.gov.ru/epz/order/notice/ea44/view/common-info.html?regNumber=".toutf($row[1]).">&nbsp;".toutf($row[1]).
"</a></td>".$t.toutf($row[2]).
"</td>".$t.$row[3].  //мц
"</td>".$t.$row[9].
"</td>".$t.$row[4].  //торг
"</td>".$t.$d1.
"</td>".$t . round(($row[5])*100)/100 .
$orgd.
"</td>".$t.$row[10].
"</td>".$t."<a href=/orgsex.php?oid=".$row[7].">".toutf($row[15])."</a>".
"</td>".$t.toutf($row[16])."</td></tr>\n";

//"</td>".$t."<a href=/orgsex.php?oid=".$row[7].">".sql_getorgINN($db,$row[7])."</a>".
//"</td>".$t.sql_getorgname($db,$row[7])."</td></tr>\n";

};
$p=$p."</table>";
 sql_close($db);
if ($n==0) {$n=1;};$middiscount=$middiscount/$n;
if ($totsum==0) {$totsum=1;};
echo '<tr><td><h3>Средний % снижения цены:'.$middiscount. 
     '</td><td><h3>Суммарный % снижения цены:'.(($totsum-$totcontr)/$totsum*100.0).
     '</h3></td></tr>';

echo $p;
include "footer.php";
?>
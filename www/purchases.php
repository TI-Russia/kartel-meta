<?php
 
 include "filer.php";
 include "sql.php";
 include "header.php";
$oid=0;

if(count($_GET) == 0) 
  {$go='form';$params='';$oid='';$cid='';$xcid='';$gid=''; 
   $maxlist=2000;	
   $mindate='';
   $maxdate='';$ptype='';
   $mindiscount='';
   $maxdiscount='';	
   $purnumber='';
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
   $maxdiscount=getparm('maxdiscount');
   $okpd=getparm('okpd');
   $download=getparm('download');
   $maxdate=getparm('maxdate');
   $mindate=getparm('mindate');
   $ptype=getparm('ptype');
   $purnumber=getparm('purnumber');
   $purname=getparm('subject');
   //delete ++ from purname
$purname=str_replace('+',' ',$purname);
   $iinn=getparm('iinn');
   $cinn=getparm('cinn');	
   $coid=getparm('coid');
   if ($coid=='') {$coid=0;};
   $oid=0;
   if ($gid!='') {$xcid=$gid;};
}

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
echo '<form method="get" action="/purchases.php">
	Вид конкурсной процедуры    :   <input name="ptype" list="ptype" value="'.$ptype.'" '.
 $inputstyle.'

   <datalist id="ptype">
    <option> </option>
    <option>	EA44	</option>
<option>	EA615	</option>
<option>	EAB44	</option>
<option>	EAO44	</option>
<option>	EAP44	</option>
<option>	EEA44	</option>
<option>	EK44	</option>
<option>	EOK44	</option>
<option>	EOKU44	</option>
<option>	OK	</option>
<option>	OK44	</option>
<option>	OKA44	</option>
<option>	OKP44	</option>
<option>	OKU44	</option>
<option>	OKUP44	</option>
<option>	PK44	</option>
<option>	POKU44	</option>
<option>        POP44   </option>
<option>        ZA111   </option>
<option>	ZK44	</option>
<option>	ZKB44	</option>
<option>	ZKBGP44	</option>
<option>	ZKBIG44	</option>
<option>	ZKE44	</option>
<option>	ZKI44	</option>
<option>	ZKK44	</option>
<option>	ZKKD44	</option>
<option>	ZKKDP44	</option>
<option>	ZKKP44	</option>
<option>	ZKKU44	</option>
<option>	ZKKUP44	</option>
<option>	ZKOO44	</option>
<option>	ZKOP44	</option>
<option>	ZKP44	</option>
<option>	ZP44	</option>
<option>	ZPP44	</option>
<option>	ОКА44	</option>
 </datalist>'.
	'ОКПД:<input name="okpd" value="'.$okpd.'" '.$inputstyle.
	'Обьект закупки:<input name="subject" value="'.$purname.'" '.$inputstyle.
	'<p>Процент снижения НМЦК от:<input name="mindiscount" value="'.$mindiscount.'" '.$inputstyle.
	'До:<input name="maxdiscount" value="'.$maxdiscount.'" '.$inputstyle.
	'Номер закупки:<input name="purnumber" value="'.$purnumber.'" '.$inputstyle.
	'<p>'.
	'Дата закупки от:<input type="date" name="mindate" value="'.$mindate.'" '.$inputstyle.
	'До:<input name="maxdate" type="date" value="'.$maxdate.'" '.$inputstyle.

' ИНН Заказчика/(разместителя заказа):<input name="cinn" value="'.$cinn.'" '.$inputstyle.
' ИНН Исполнителя:<input name="iinn" value="'.$iinn.'" '.$inputstyle.'<br><br>
        Вывести список не более чем из <input name="maxlist" value="'.$maxlist.'"'.$inputstyle.' строк
        <input type=submit value="Найти" formaction="/purchases.php" '.$submitstyle."\n".
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
if (($xcid=='')and($gid=='')) 


   {  $if1=''; if (($mindiscount!='')&&($mindiscount!="''")) 
 //{$if1='discount>=maxprice*('.$mindiscount.'/100.0) ';};
   {$if1='a.percents>='.$mindiscount.' '; };
//$percent=($row[2]-$row[8])/$row[2]*100;
      $if2=''; if (($maxdiscount!='')&&($maxdiscount!="''")) 
 {$if2='a.percents<='.$maxdiscount.' '; };
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

};//      echo $if;

      $sql="select top ".$maxlist." a.purchasenumber,a.coid,a.maxprice,a.date,a.status,a.cphone,a.cemail,a.lot,a.discount,a.okpd,a.type,a.oid,a.name,a.percents,orgs.inn,orgs.name,o2.inn,o2.name 
      from (select  *,(case when maxprice=0 then NULL else  (maxprice-discount)/maxprice*100 end) as percents  from zakupki_work.dbo.purchasesLT) as a inner join orgs on (a.coid=orgs.oid) left join orgs o2 on (a.oid=o2.oid)   where ".$if."  a.date > '2014-01-01' order by a.date";
//echo $sql;
//header	
$p="<tr><th> Дата </th><th>Процедура</th><th> Закупка </th><th>№ лота</th><th>ОКПД</th><th>НМЦК </th><th>Цена контракта</th><th>% снижения</th><th>Предмет закупки</th><th>ИНН Заказчика<br>(Разместившего заказ)</th><th>Название заказчика<br>(разместившего заказ)</th><th>Инн поставщика</th><th>Название поставщика</th></tr>";
   } else
    {
     { $sql="select * from doubleconcurents where cartelid=".$gid;};
    if ($xcid>0)
     { $sql="select * from concurentsLT  join orgs on orgs.oid=concurentsLT.oid where 
       (purchasenumber in (select purchasenumber from doubleconcurents where cartelid=".$xcid.")) 
 and(cartelid=".$xcid.") order by purchasenumber";
	echo "<tr><th> закупка </th><th>ИД организации</th><th>ИД группы</th><th>Название организации</th></tr>";
    }
     };
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
//9 - okpd
//10 - type
//11 - oid
//12 - name
//13 - percent
//14 - co inn
//15 - co name
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
if ($row[8]!='')
   { $percent=$row[13];//($row[2]-$row[8])/$row[2]*100;
      $percent=round(($percent)*100)/100;
	$percent=$percent.'%';	
	};
$p=$p."\n"."<tr><td>".$d. "</td><td>".
  $row[10]."</td><td>".
  "<a href=https://zakupki.gov.ru/epz/order/notice/ea44/view/common-info.html?regNumber=".toutf($row[0]).">&nbsp;".toutf($row[0])."</a></td><td>".
  $row[7]."</td><td>". //lot
  $row[9]."</td><td>". //okpd
  toutf($row[2])."</td><td>". //nmck
  toutf($row[8])."</td><td>".
  $percent."</td><td>".
  toutf($row[12])."</td><td>".
  toutf($row[14])//sql_getorgINN($db,$row[1])
   ."</td><td>".
  toutf($row[15])//sql_getorgname($db,$row[1])
  ."</td><td>".
  toutf($row[16])."</td><td>".
  toutf($row[17])."</td></tr>";
}
};

//    die( print_r( sqlsrv_errors(), true));
$p=$p."</table></body></html>";
echo $p;
// print_r($row);
include "footer.php";
 sql_close($db);
?>
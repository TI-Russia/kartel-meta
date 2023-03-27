<?php
 
 include "filer.php";
 include "sql.php";
$script='<script type="text/javascript" src="meta.js"></script><link rel="stylesheet" href="css/oktmo.css" /><script type="text/javascript" src="oktmo.js"></script>';
$onload=' onload="floaded();"';
 include "header.php";
$oid=0;

if(count($_GET) == 0) 
  {$go='form';$params='';$oid='';$cid='';$xcid='';$gid=''; 
   $maxlist=200;
   $cname='';$pname='';		
   $mindate='';
   $maxdate='';$ptype='';
   $mindiscount='';
   $maxdiscount='';	
   $maxcontract=0;$mincontract=0;
   $purnumber='';
   $metatag='';
   $download=0;
   $okpd='';$oid=0;$oidname='';$purname='';$oktmo='';
   $cinn='';$iinn='';$coid=0;
   } 
else
{ 
   $xcid=getparm('xcid');
   $gid=getparm('gid');
   $maxlist=getparm('maxlist');
   if ($maxlist=='') {$maxlist=200;};
   $mindiscount=getparm('mindiscount');
   $cname=str_replace('+',' ',getparm('cname'));
   $pname=str_replace('+',' ',getparm('pname'));
   $maxdiscount=getparm('maxdiscount');
   $okpd=getparm('okpd');
   $oktmo=getparm('oktmo');
   $download=getparm('download');
   $maxdate=getparm('maxdate');
   $mindate=getparm('mindate');
   $maxcontract=getparm('maxcp');
   $mincontract=getparm('mincp');
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
function safestr($a)
{
 return "'".toutf(trim(str_replace("\r",' ',str_replace("\n",' ',str_replace("'","\"",$a)))))."'";
}

function getmetatags($db,$purchase,$meta="" )
{ $res='';
  $sql='select a.filename,b.tag,b.value,b.code,a.contentid
	from meta_work.dbo.datafiles as a  
	   inner join meta_work.dbo.metatags as b on b.[fileid]=a.fileid where a.purchasenumber='."'".$purchase."'";
 if ($meta!="") {$sql=$sql ." and b.value like '". to1251($meta) ."'";};

//echo $sql;
$stmt = sqlsrv_query ($db, $sql);
      if( $stmt === false ) 
		{  echo ($sql.'<br>');
			    die(toutf(sqlsrv_errors()[0][2]));
                }
while($row = sqlsrv_fetch_array($stmt)) {
    if($res) $res.=',';
    $res=$res.'['.implode(',',array(safestr($row[4]),safestr($row[0]),safestr($row[1]),safestr($row[2]),safestr($row[3])))."]\n";
   }
 sqlsrv_free_stmt($stmt);
  return '['.$res.']';
};

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

if (($xcid=='')and($gid=='')) 

   {  $if1=''; if (($mindiscount!='')&&($mindiscount!="''")) 
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
      if ($mincontract>0) {$if=$if.' a.discount>'.$mincontract.' and ';};
      if ($maxcontract>0) {$if=$if.' a.discount<'.$maxcontract.' and ';};
      if ($oktmo>0) 
	{
	 $if=$if. " a.coid in (select oid from orgs where orgs.oktmo like '".$oktmo."%') and ";
	};
	if ($cname>0) { 
		$if=$if. " a.coid in (select oid from orgs where orgs.name like '%".to1251($cname)."%') and ";
	  	};
	if ($pname>0) { 
		$if=$if. " a.oid in (select oid from orgs where orgs.name like '%".to1251($pname)."%') and ";
		};

      if ($purnumber!='') {$if=$if . " a.purchasenumber='".$purnumber."' and ";};

      if ($ptype!='') {
        if (strpos('!!!'.$ptype,'%')>0) 
		{ $if=$if. " a.type like("._sql_validate_value($ptype).") and ";
		}
        else {
		$if=$if. ' a.type='._sql_validate_value($ptype).' and ';
	}

}; 
if (strlen($metatag)>0) {
 $if = $if . "  a.purchasenumber in ( ".
"    select purchasenumber from
	meta_work.dbo.Datafiles where fileid in (select [fileId] FROM [meta_work].[dbo].[Metatags] where value like '".to1251($metatag)."')) and ";
};
//if (trim($if)=="") {$maxlist=1;};
//echo '>>'.$if.'<<';
      $sql="select top ".$maxlist. " * from (select
a.purchasenumber,a.coid,a.maxprice,a.date,a.status,a.cphone,a.cemail,a.lot,a.discount,a.offers,a.rejected,a.okpd,a.type,a.oid,a.name,a.percents,orgs.inn,orgs.name as oname,o2.inn as oinn,o2.name as o2name,p.sum,a.itemprice,

iif(a.itemprice>0 and 

 (((a.maxprice-a.discount)<a.itemprice)or(a.itemprice-p.sum>=0)) ,
(a.itemprice-iif(p.sum=0,iif(a.itemprice>0,a.itemprice,a.maxprice),p.sum ) )/a.itemprice*100,iif(a.maxprice=0, NULL ,(a.maxprice-iif(p.sum=0,maxprice,p.sum ))/a.maxprice*100)) as percentsex,
 orgs.epzorg,iif(a.offers>0,100.0*a.rejected/a.offers,NULL) as rj
 
 from (
 select *,(case when maxprice=0 then NULL else (maxprice-discount)/maxprice*100 end) as percents from ".$zakupkibase.".dbo.purchasesLT
 ) as a 
 inner join orgs on (a.coid=orgs.oid) left join orgs o2 on (a.oid=o2.oid) 
 left join zakupki.dbo.concurents as p on (p.purchasenumber=a.purchasenumber and p.place=1 and p.active=1 and a.lot=p.lot )
 ) as a where ".$if."  a.date > '2014-01-01' order by a.date";

//echo htmlentities($sql);
//echo toutf($sql);
//header	
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
//23 - c.epzorg
//24 - rj - reject/offers
$cnt=0;
if (strlen(trim($if))>0)
{
 $stmt = sqlsrv_query ($db, $sql,array(), array("Scrollable"=>"buffered","QueryTimeout" => 300));
  if( $stmt === false ) {
    echo ($sql.'<br>');
    die(toutf(sqlsrv_errors()[0][2]));
  }
$cnt=sql_getvalue($db,'rowcount_big()');
} else 
  $stmt = 0;
//$cnt=sqlsrv_num_rows($stmt);
$arr='';
if ($stmt!=0)
while($row = sqlsrv_fetch_array($stmt)) {

if($arr) $arr.=',';
	$d='""';
	if ($row[3]!=NULL) {$d='new Date('.safestr($row[3]->format('Y-m-d')).')';};
$percent='"-"';
if ($row[21]==0) {$row[21]='""';};

$cp='undefined';if (($row[20]=='')&&($row[8]>0))
	{
	$cp='"c_missed"';
	$row[20]=$row[2];
	};
if ($row[8]!='')
   { 
      if ($row[9]==0) {$row[9]=1;}; //для пустых выигранных заявок
$percent=$row[22];		
      $percent=round(($percent)*100)/100;

	};
$arr.='['.join(',',array('undefined',
$d, // date
safestr($row[12]), // type
safestr($row[0]), // num
safestr($row[7]), // lot
$row[9], // offers
$row[10], // rejected
safestr($row[11]), // okpd
$row[2],
$row[21],
$row[20],
$row[8],
$percent,
safestr($row[14]),
safestr($row[16]),
safestr($row[17]),
safestr($row[18]),
safestr($row[19]),
$row[0],
getmetatags($db,$row[0],$metatag),
$cp,
safestr($row[23]),
round($row[24]*100)/100
)).']';
}
//  [new Date('2019-02-15'),'EPP44','0377200003719000002','',1,
//   1,'35.14.10.000',200000,200000,
//   200000,200000,0,'Поставка электрической энергии (мощности)','0411099187',
//   'БЮДЖЕТНОЕ УЧРЕЖДЕНИЕ РЕСПУБЛИКИ АЛТАЙ "РЕСПУБЛИКАНСКАЯ ДЕТСКАЯ БИБЛИОТЕКА"','2224103849','" Алтайэнергосбыт "','0377200003719000002',
//   [
//    ['81D6D6CE43C6003EE0530A86121F10AF','gos_bud_/Приложение №02 Договорные величины_свыше_ЦК_5,6.xls','Company','Алтайэнерго'],
//    ['81D6D6CE43C6003EE0530A86121F10AF','gos_bud_/Приложение №07 Акт учета почасовок для 3-6 ЦК.xls','Company','Алтайэнерго']
//   ],
//   'c_missed'
//   ],

print "<script>var arr=[$arr];
var xlshdr='".str_replace("\n","\\n",str_replace("\r",'',$xlshdr))."';
</script>
";
//------------ Шапка сайта
echo '<div class="hdrgray">Поиск по метаданным и закупкам</div>'; 
echo '<form method="get" action="meta.php">
	Способ закупки:<input name="ptype" list="ptype" value="'.$ptype.'" '.
 inputstyle(200).
 $ptype_datalist.
	'Метаданные:<input name="metatag" value="'.htmlspecialchars($metatag).'" '.inputstyle(450).
	'<div>Обьект закупки:<input name="subject" value="'.htmlspecialchars($purname).'" '.inputstyle(300).
	'&nbsp;&nbsp;ОКПД закупки:<input name="okpd" value="'.htmlspecialchars($okpd).'" '.inputstyle(150).
	'&nbsp;&nbsp;Номер закупки:<input name="purnumber" value="'.$purnumber.'" '.inputstyle(200).
	'</div>'.
	'<p>Процент снижения НМЦК(Цены единицы) от:<input name="mindiscount" value="'.$mindiscount.'" '.$inputstyle.
	'&nbsp;&nbsp;До:<input name="maxdiscount" value="'.$maxdiscount.'" '.$inputstyle.
	

	'<div>'.
	'Дата закупки от:<input type="date" name="mindate" value="'.$mindate.'" '.$inputstyle.
	'До:<input name="maxdate" type="date" value="'.$maxdate.'" '.$inputstyle.
	' Cумма контракта(ов) от:<input name="mincp" value="'.$mincontract.'" '.$inputstyle.
	'&nbsp;&nbsp;До:<input name="maxcp" value="'.$maxcontract.'" '.$inputstyle.


'</div>
  <div> ИНН Заказчика/(организатора закупки):<input name="cinn" value="'.$cinn.'" '.$inputstyle.
	'&nbsp;Название <input name="cname" value="'.$cname.'" '.inputstyle(500).
'</div>
<div style="display:flex;align-items:center;">ОКТМО:<div><div class="oktmoContainer oktmoStyle" id="oktmo">
    <input type="text" id="oktmotext" placeholder=" " autocomplete="off" oninput="searchoktmo(this)">
    <button type="button" class="ddcleadButt" onclick="document.getElementById(\'oktmotext\').value=\'\';document.getElementById(\'oktmovalue\').value=\'\';">&times;</button>
    <button type="button" class="ddbutt oktmoStyle" onclick="toggleoktmo()"><div class="arrow-down"/></button>
    <input type="hidden" id="oktmovalue" name="oktmo" placeholder=" " value="'.$oktmo.'">
</div>
<div id="oktmosearch" class="oktmopanel"><ul id="ulOKTMO"></ul></div></div></div>

<div> ИНН Поставщика:          <input name="iinn" value="'.$iinn.'" '.$inputstyle.
	'&nbsp;Название <input name="pname" value="'.$pname.'" '.inputstyle(500).
'</div><br> Вывести список не более чем из <input name="maxlist" value="'.$maxlist.'"'.$inputstyle.' строк

        <input type=submit value="Найти" formaction="meta.php" '.$submitstyle."\n".
        '<input type=button value="Cкачать результат" OnClick="downloadxl();" '.$submitstyle.
'</form>найдено '.$cnt.' записей<br>';
 ob_end_flush();

?>
<table id="ress" border=1 cellspacing=0 cellpadding=0>
<thead>
<th class="sort sortable" fid=1 template="formatDate(v)">Дата&nbsp;&nbsp;</th>
<th class="sort sortable" fid=2>Способ закупки&nbsp;&nbsp;</th>
<th class="sort sortable" fid=3 template="`<a target='_blank' href='https://zakupki.gov.ru/epz/order/notice/ea44/view/common-info.html?regNumber=${v}' target='_blank'>&nbsp;${v}</a>`">№ закупки</th>
<th class="sort sortable" fid=4>№ лота&nbsp;&nbsp;</th>
<th class="sort num sortable" fid=5>Количество заявок&nbsp;&nbsp;</th>
<th class="sort num sortable" fid=6>Отклонено&nbsp;&nbsp;&nbsp;&nbsp;<span></th>
<th class="sort num sortable" fid=22 template="formatPercent(v)">% отклоненных&nbsp;&nbsp;&nbsp;&nbsp;<span></th>
<th class="sort sortable" fid=7>ОКПД(2)&nbsp;&nbsp;</th>
<th class="sort num sortable" fid=8>НМЦК&nbsp;&nbsp;<span></th>
<th class="sort num sortable" fid=9 template="`<span${e[20]?' class='+e[20]:''}>${v?v:'-'}</span>`">Цена единицы&nbsp;&nbsp;</th>
<th class="sort num sortable" fid=10>Цена победителя</th>
<th class="sort num sortable" fid=11 template="v>0?`<a href='${baseurl}contractsex.php?purnumber=${e[3]}'>${v}</a>`:'-'">Цена контракта</th>
<th class="sort num sortable" fid=12 template="formatPercent(v)">% сни-жения</th>
<th class="sort sortable" fid=13>Предмет закупки</th>
<th class="sort sortable" fid=14 template="orgsEx(v)">ИНН Заказчика<br>(Организатора закупки)</th>
<th class="sort sortable" fid=15 template="e[21]?`<a target='_blank' href='https://zakupki.gov.ru/epz/organization/view/info.html?organizationCode=${e[21]}'>${v}</a>`:v">Заказчик<br>(организатор закупки)</th>
<th class="sort sortable" fid=16 template="orgsEx(v)">ИНН поставщика</th>
<th class="sort sortable" fid=17>Поставщик</th>
<th template="metas(e[19],e[3])">Метаданные</th></thead>
<tbody id="bress"/>
</table>
<?php
include "footer.php";
 sql_close($db);
?>
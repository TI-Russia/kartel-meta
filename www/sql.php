
<?php
global $sql_id;
$zakupkibase='zakupki_work';
function getparm($d)
{
$req='='.$_SERVER['QUERY_STRING']; $p='';
if (isset($_POST[$d])) { $p=$_POST[$d]; } 
$pos=strpos($req,$d."=");
if ($pos>0) {
//	echo $d.' pos:'.$pos.'<br>';
	$p=substr($req,$pos+strlen($d)+1);
	$l=strpos('!'.$p,"&");
	if ($l>0) {
 		   $p=substr($p,0,$l-1);
		  }
//        echo rawurldecode($req).'<br>';
	};

// if (!isset($_GET[$d]))
//  { $p=''; } else
//  {  $p=$_GET[$d];};
//echo $p."<br>".rawurldecode($p)."<br>";
return rawurldecode($p);
};
function sql_connect() {
sqlsrv_configure( "WarningsReturnAsErrors", 0 );
$sql_id=sqlsrv_connect('localhost', array(
			'Database' => 'zakupki',
			'UID' => 'sa',
			'PWD' => 'sa11011'));
     return $sql_id;
    };

function sql_escape($msg)
{
		return str_replace(array("'", "\0","�","�"), array('""', '',"*","*"), $msg);
}

function sql_close($id) { return sqlsrv_close($id); };
function sql_getcount($db,$t )
 {   $sql='select count(*) from '.$t;
        $stmt=sqlsrv_query ($db, $sql);
//	echo toutf($sql);
	$row=sqlsrv_fetch_array($stmt);$total=$row[0];	
	sqlsrv_free_stmt($stmt);
	return $total;

 }
	function _sql_validate_value($var)
	{
		if (is_null($var))
		{ return 'NULL';}
		else if (is_string($var))
		{ return "'" . sql_escape($var) . "'";	}
		else
		{ return (is_bool($var)) ? intval($var) : $var; }
	}

function sql_get_Cid_by_Gid($db,$gid)
 {
     if ($gid=='') {$name=0;} else

    {
     $sql="select top 1 cid from orgs where gid=".$cid and cid>0;
     $name=0;
     $stmt = sqlsrv_query ($db, $sql);
      if( $stmt === false ) 
		{  echo ($sql.'<br>');
			    die(toutf(sqlsrv_errors()[0][2]));
                }
    while($row = sqlsrv_fetch_array($stmt)) {
    $name=toutf($row[0]);
   }
  }
 return $name;
}

function sql_get_Gid_by_Cid($db,$cid)
 {
     if ($cid=='') {$name=0;} else

    {
     $sql="select top 1 gid from orgs where cid=".$cid;
     $name=0;
     $stmt = sqlsrv_query ($db, $sql);
      if( $stmt === false ) 
		{  echo ($sql.'<br>');
			    die(toutf(sqlsrv_errors()[0][2]));
                }
    while($row = sqlsrv_fetch_array($stmt)) {
    $name=toutf($row[0]);
   }
  }
 return $name;
}
 
function sql_getorgname($db,$oid)	

   {     if ($oid=='') {$name="";} else

    {
     $sql="select name from orgs where oid=".$oid;
     $name='';
     $stmt = sqlsrv_query ($db, $sql);
      if( $stmt === false ) 
		{  echo ($sql.'<br>');
			    die(toutf(sqlsrv_errors()[0][2]));
                }
    while($row = sqlsrv_fetch_array($stmt)) {
    $name=toutf($row[0]);
   }
  }
 return $name;
}
function sql_getvalue($db,$oid)	

   { if ($oid=='') {$name="";} else
    {
     $sql="select ".$oid;
     $stmt = sqlsrv_query ($db, $sql);
      if( $stmt === false ) 
		{  echo ($sql.'<br>');
			    die(toutf(sqlsrv_errors()[0][2]));
                }
    while($row = sqlsrv_fetch_array($stmt)) {
    $name=$row[0];
	}

    }
 return $name;
}

function sql_getorgINN($db,$oid)	

   { if ($oid=='') {$name="";} else
    {
     $sql="select inn from orgs where oid=".$oid;
     $name='';
     $stmt = sqlsrv_query ($db, $sql);
      if( $stmt === false ) 
		{  echo ($sql.'<br>');
			    die(toutf(sqlsrv_errors()[0][2]));
                }
    while($row = sqlsrv_fetch_array($stmt)) {
    $name=toutf($row[0]);
	}
    }
 return $name;
}

function sql_getorgInfo($db,$oid)	

   { if ($oid=='') {$name="";} else
    {
     $sql="select inn,name,gid,cid from orgs where oid=".$oid;
     $name=array('','',0,0);
     $stmt = sqlsrv_query ($db, $sql);
      if( $stmt === false ) 
		{  echo ($sql.'<br>');
			    die(toutf(sqlsrv_errors()[0][2]));
                }
    while($row = sqlsrv_fetch_array($stmt)) {
    $name=array(toutf($row[0]),toutf($row[1]),toutf($row[2]),toutf($row[3]));
	}

    }
 return $name;
}

function exec_sql($id,$args)
 { 
 if ($args[2]=="") {$args[2]="null";};
 if ($args[3]=="") {$args[3]="null";};
//$args[3]='111';
//   $sql = to1251("begin declare @r int; execute addOrg('".$args[0]."','".$args[1]."','".$args[2]."','".$args[3]."');end");
 $sql = "execute addOrg @INN="._sql_validate_value($args[0]).",@name="._sql_validate_value($args[1]).",@email="._sql_validate_value($args[2]).",@phone="._sql_validate_value($args[3]);
   // $sql = to1251('execute addOrg @INN="'.$args[0].'",@name="'.$args[1].'",@email="'.$args[2].'",@phone="'.$args[3].'" ');
//   echo "\n sql string:".$sql."\n";
   $stmt = sqlsrv_query ($id, $sql,array(), array("Scrollable"=>"buffered"));
//   $stmt = sqlsrv_query ($id, $sql);
  if( $stmt === false ) {
     die( print_r( sqlsrv_errors(), true));
}
   	
 }

//--- common functions for data show
function findallp($db,$id,$cid)
{
 $r="<table border=1 cellspacing=0 cellpadding=0><tr><td>Номер</td><td>связи</td><td>вес</td></tr>";
 if ($cid!='') 
	{  $sql="select phone,usecnt,weight from orgphonesLtc where oid=".$id;
	} else
	{
	 $sql="select phone,usecnt,weight from orgphonesLt where oid=".$id;
	}
 $stmt = sqlsrv_query ($db, $sql);$n=0;
  if( $stmt === false ) {
     die( print_r( sqlsrv_errors(), true));};
  while($row = sqlsrv_fetch_array($stmt)) 
  {
 $r=$r."<tr><td width=160><a href=contractsex.php?phone=".rawurlencode(toutf($row[0])).">".toutf($row[0])."</a></td>
	<td width=20><a href=orgsex.php?phone=".$row[0].">".$row[1]."</a></td><td width=30>".$row[2]."</td></tr>";
 $n++;
 };

 $r=$r."</tr></table>";
 if ($n==0) {$r='--';};
 return $r;
}
function findalle($db,$id)
{
 $r="<table border=1 cellspacing=0 cellpadding=0><tr><td>Адрес</td><td>связи</td><td>вес</td></tr>";
// $sql="select phone from finddoublephonesbyorg(".$id.")";
 $sql="select email,usecnt,weight from orgEmailsLt where oid=".$id;
 $stmt = sqlsrv_query ($db, $sql);
 $n=0;
  if( $stmt === false ) {
     die( print_r( sqlsrv_errors(), true));};

  while($row = sqlsrv_fetch_array($stmt)) 
  {
 $r=$r."<tr><td width=160><a href=contractsex.php?email=".rawurlencode(toutf($row[0])).">".toutf($row[0])."</a></td>
	<td width=20>".$row[1]."</td><td width=30>".$row[2]."</td></tr>";
 $n++;
 };

 $r=$r."</tr></table>";
 if ($n==0) {$r='--';};
 return $r;
}
$ptype_datalist='
   <datalist id="ptype">
    <option> </option>
<option value="EPP44"> Закупка у единственного поставщика (подрядчика, исполнителя) </option>
<option value="EAP44"> Электронный аукцион	</option>
<option value="EAP20"> Электронный аукцион	</option>
<option	value="ZKP44"> Запрос котировок	</option>
<option	value="ZKP504"> Запрос котировок в электронной форме</option>
<option value="ZPP504"> Запрос предложений в электронной форме </option>
<option value="EA44" > Электронный аукцион	</option>
<option value="ZK44" > Запрос котировок	</option>
<option value="ZKP20"> Запрос котировок	в электронной форме</option>
<option value="EP44" > Закупка у единственного поставщика (подрядчика, исполнителя)</option>
<option value="OKP44"> Открытый конкурс</option>
<option value="ZA111"> Закрытый аукцион с учетом положений ст. 111 Закона № 44-ФЗ</option>
<option value="EAB44"> Электронный аукцион на проведение работ по строительству, реконструкции, кап. ремонту, сносу объекта кап. строительства, предусматривающих проектную документацию, утвержденную в порядке, установленном законодательством о градостроительной деятельности </option>
<option value="OKP20"> Открытый конкурс в электронной форме </option>
<option value="OKUP44"> Конкурс с ограниченным участием </option>
<option value="ZAP44"> Закрытый аукцион </option>
<option value="OK44"> Открытый конкурс </option>
<option value="ZPP44"> Запрос предложений </option>
<option value="POP44"> Предварительный отбор </option>
<option value="EAB20"> Электронный аукцион на проведение работ по строительству, реконструкции, кап. ремонту, сносу объекта кап. строительства в соответствии с п. 8 ч. 1 ст. 33 Закона № 44-ФЗ</option>
<option value="OKU44"> Конкурс с ограниченным участием </option>
<option value="OKP504"> Открытый конкурс в электронной форме </option>
<option value="ZP44"> Запрос предложений </option>
<option value="EP111"> Закупка у единственного поставщика (подрядчика, исполнителя) с учетом положений ст. 111 Закона № 44-ФЗ</option>
<option value="EA615"> Электронный аукцион по ПП №615 </option>
<option value="OKUP504">Конкурс с ограниченным участием в электронной форме </option>
<option value="OKB20">Открытый конкурс в электронной форме на проведение работ по строительству, реконструкции, кап. ремонту, сносу объекта кап. строительства в соответствии с ч. 1 п. 8 ст. 33 Закона № 44-ФЗ</option>
<option value="OKA44">Открытый конкурс для заключения договора на проведение аудита бухгалтерской (финансовой) отчетности (согласно ч. 4 ст. 5 Федерального закона от 30.12.2008г. № 307-ФЗ)</option>
<option value="OKB504">Открытый конкурс в электронной форме на проведение работ по строительству, реконструкции, кап. ремонту, сносу объекта кап. строительства, предусматривающих проектную документацию или экономически эффективную проектную документацию повторного использования, или смету на капитальный ремонт объекта капитального строительства, утвержденную в порядке, установленном законодательством о градостроительной деятельности</option>
<option value="EAO44">Электронный аукцион для торгов по обращению с твердыми коммунальными отходами</option>
<option value="EOK44">Конкурс для заключения энергосервисного контракта</option>
<option value="ZKKP44">Закрытый конкурс</option>
<option value="EA615">Электронный аукцион на оказание услуг или выполнение работ по капитальному ремонту общего имущества в многоквартирном доме</option>
<option value="EAP615">Электронный аукцион на оказание услуг или выполнение работ по капитальному ремонту общего имущества в многоквартирном доме</option>
<option value="OKA504">Открытый конкурс в электронной форме для заключения договора на проведение аудита бухгалтерской (финансовой) отчетности (согласно ч. 4 ст. 5 ФЗ от 30.12.2008г. № 307-ФЗ)</option>
<option value="PO44"> Предварительный отбор </option>
<option value="PK44">Повторный конкурс	</option>
<option value="EEA44">Электронный аукцион для заключения энергосервисного контракта</option>
<option value="EOK504">Открытый конкурс в электронной форме для заключения энергосервисного контракта	</option>
<option value="POKU44">Повторный конкурс с ограниченным участием</option>
<option value="OKA20">Открытый конкурс в электронной форме для заключения договора на проведение аудита бухгалтерской (финансовой) отчетности (согласно ч. 4 ст. 5 Федерального закона от 30.12.2008г. № 307-ФЗ)</option>
<option value="INM111">Способ определения поставщика (подрядчика, исполнителя), установленный Правительством Российской Федерации в соответствии со ст. 111 Федерального закона № 44-ФЗ</option>
<option value="INMP111">Способ определения поставщика (подрядчика, исполнителя), установленный Правительством Российской Федерации в соответствии со ст. 111 Федерального закона № 44-ФЗ</option>
<option value="OKK504">Открытый конкурс в электронной форме для заключения контракта в сфере науки, культуры или искусства</option>
<option value="ZKK111">Закрытый конкурс с учетом положений ст. 111 Закона № 44-ФЗ</option>
<option value="ZKOP44">Запрос котировок для оказания скорой, в том числе скорой специализированной, медицинской помощи в экстренной или неотложной форме и нормального жизнеобеспечения граждан (в соответствии со Статьей 76 Федерального закона № 44-ФЗ)</option>
<option value="EAO20">Электронный аукцион для торгов по обращению с твердыми коммунальными отходами</option>
<option value="ZKBGP44">Запрос котировок в целях оказания гуманитарной помощи либо ликвидации последствий чрезвычайной ситуации природного или техногенного характера (в соответствии со Статьей 82 Федерального закона № 44-ФЗ)</option>
<option value="OK111">Открытый конкурс с учетом положений ст. 111 Закона № 44-ФЗ</option>
<option value="ZKOO44">Запрос котировок на выполнение работ по строительству, реконструкции, капитальному ремонту, а также проектных, изыскательских работ в отношении олимпийских объектов и строительству домов взамен земельных участков и объектов недвижимого имущества, изымаемых в целях размещения олимпийских объектов (часть 15 статьи 112 Федерального закона №44-ФЗ)</option>
<option value="EOK20"></option>
<option value="ZKKUP44"></option>
<option value="ZKE44"></option>
<option value="OK"></option>
<option value="ZKKD111"></option>
<option value="ZKI44"></option>
<option value="ZP111"></option>
<option value="ZK111"></option>
<option value="OKUK504"></option>
<option value="EEA20"></option>
<option value="ZKBIG44"></option>
<option value="PO111"></option>
<option value="ZA44"></option>
<option value="ZKKU44"></option>
<option value="EA111"></option>
<option value="ZKKU111"></option>
<option value="ZKK44"></option>
<option value="EK44"></option>
<option value="OKU111"></option>
<option value="ZK504"></option>
<option value="ZKKD44"></option>
<option value="ZKB44"></option>
<option value="ZKKDP44"></option>
<option value="EZK20"></option>
<option value="ZKB111"></option>
<option value="EZK504"></option>
<option value="OKI20"></option>
<option value="EOKU44"></option>
<option value="ZAE44"></option>
<option value="OK504"></option>
<option value="OKD111"></option>
<option value="EOKU504"></option>
<option value="OKU504"></option>
<option value="ZP504"></option>
<option value="EZP504"></option>
<option value="OKI504"></option>
 </datalist>'

?>

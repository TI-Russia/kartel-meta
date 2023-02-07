
<?php
global $sql_id;
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
function sql_getcount($db,$t)
 {
        $stmt=sqlsrv_query ($db, 'select count(*) from '.$t);
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
   $stmt = sqlsrv_query ($id, $sql);
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
 $r=$r."<tr><td width=160><a href=contractsex.php?phone=".rawurlencode(toutf($row[0])).">".toutf($row[0])."</a></td><td width=20>".$row[1]."</td><td width=30>".$row[2]."</td></tr>";
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
 $r=$r."<tr><td width=160><a href=contractsex.php?email=".rawurlencode(toutf($row[0])).">".toutf($row[0])."</a></td><td width=20>".$row[1]."</td><td width=30>".$row[2]."</td></tr>";
 $n++;
 };

 $r=$r."</tr></table>";
 if ($n==0) {$r='--';};
 return $r;
}

?>

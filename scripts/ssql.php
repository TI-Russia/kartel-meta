
<?php
$sql_id=0;

function sql_connect() {
echo 'connecting:';
sqlsrv_configure( "WarningsReturnAsErrors", 0 );
$opt=array(		"Database" => "zakupki",
#                        "CharacterSet"=>"utf-8",

#			"Authentication"=>"SqlPassword",
			"UID" => "sa",          
			"PWD" => "sa11011")
;
$srv='tcp:localhost,1433';
$sql_id=sqlsrv_connect($srv, $opt);
if ($sql_id===false)
   {   echo 
	toutf(print_r(sqlsrv_errors()));
	toutf(sqlsrv_errors()[0][2])."\n";
	die();

};
 echo $sql_id;
 print_r($sql_id);
     return $sql_id;
    };
function sql_escape($msg)
{
		return str_replace(array("'", "\0","«","»"), array('""', '',"*","*"), $msg);
}

function sql_close($id) { return sqlsrv_close($id); };
	function _sql_validate_value($var)
	{
		if (is_null($var))
		{ return 'NULL';}
		else if (is_string($var))
		{ return "'" . sql_escape($var) . "'";	}
		else
		{ return (is_bool($var)) ? intval($var) : $var; }
	}

function exec_sql($id,$args)
 { 
 if ($args[2]=="") {$args[2]="null";};
 if ($args[3]=="") {$args[3]="null";};
 if ($args[7]=="") {$args[7]=0;};
 if ($args[8]=="") {$args[8]=0;};
//$args[3]='111';
// $args[1]="Открытое акционерное общество междугородной и международной электрической связи *Ростелеком* Филиал в Республике Марий Эл";
//   $sql = to1251("begin declare @r int; execute addOrg('".$args[0]."','".$args[1]."','".$args[2]."','".$args[3]."');end");
 $sql = "begin declare @r int;execute @r=addOrg @INN="._sql_validate_value($args[0]).",@name="._sql_validate_value($args[1]).",@email="._sql_validate_value($args[2]).",@phone="._sql_validate_value($args[3]).";".
         "      declare @cr int;execute @cr=addOrg @INN="._sql_validate_value($args[11]).",@name="._sql_validate_value($args[12]).",@phone='',@email='';"

       ."execute addContract @oid=@r, @num="._sql_validate_value($args[6]).
	 ",@pid="._sql_validate_value($args[4]).
	 ",@id="._sql_validate_value($args[5]).
         ",@email="._sql_validate_value($args[2]).",@phone="._sql_validate_value($args[3]).
         ",@price=".$args[7].
	 ",@date="._sql_validate_value($args[10]).
	 ",@coid= @cr".
	 ",@reg=".substr($args[11],0,2).
	 ",@ver=".$args[8] 
.";end;";
// return (0:$inn/1:$orgname/2:$email/3:$phone/4:$nno/5:$id/6:$rn/7:$price,
//         8:$ver/9:$flag/10:$date/11:$cinn,/12:$corgname,'ppz');
   // $sql = to1251('execute addOrg @INN="'.$args[0].'",@name="'.$args[1].'",@email="'.$args[2].'",@phone="'.$args[3].'" ');
//   echo "\n sql string:".$sql."\n";
if ($args[0]!="") 
{
   $stmt = sqlsrv_query ($id, $sql);
//else echo "skip\n";
  if( $stmt === false ) {
	$f=fopen('errors.log','a+');
         fwrite($f,toutf(sqlsrv_errors()[0][2])."\n");
	 fwrite($f,toutf($sql)."\n"); 
	fclose($f);
} else {
//   while($row = sqlsrv_fetch_array($stmt))  {  unset($row);  };
//    sqlsrv_free_stmt($stmt);  	
    unset($stmt);
}
  };
 unset($sql);
 };
 
?>

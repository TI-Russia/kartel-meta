
<?php
$sql_id=0;
include 'commonsql.php';

function exec_sql($id,$args,$fn)
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
	 ",@ver=".$args[8].
         ",@lot=".$args[13].";end;";
// return (0:$inn/1:$orgname/2:$email/3:$phone/4:$nno/5:$id/6:$rn/7:$price,
//         8:$ver/9:$flag/10:$date/11:$cinn,/12:$corgname,'ppz');
   // $sql = to1251('execute addOrg @INN="'.$args[0].'",@name="'.$args[1].'",@email="'.$args[2].'",@phone="'.$args[3].'" ');
//   echo "\n sql string:".$sql."\n";
if ($args[0]!="") 
{
   $stmt = sqlsrv_query ($id, $sql);
//else echo "skip\n";
  if( $stmt === false ) {
	$f=fopen('Contracts_errors.log','a+');
	 fwrite($f,$fn."\n");
         fwrite($f,toutf(sqlsrv_errors()[0][2])."\n");
	 fwrite($f,toutf($sql)."\n"); 
	fclose($f);
} else {
//   while($row = sqlsrv_fetch_array($stmt))  {  unset($row);  };
    sqlsrv_free_stmt($stmt);  	
    unset($stmt);
}
  };
 unset($sql);
 };
 
?>

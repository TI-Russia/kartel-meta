
<?php
global $sql_id;
include 'commonsql.php';

function execSQLP($db,$sql)
{
//  echo 'running:'.$sql."\n";
  $stmt = sqlsrv_query ($db, $sql);
  if ($stmt!=false) {sqlsrv_free_stmt($stmt);} 
    else { echo 'error';
			echo (toutf(sqlsrv_errors()[0][2])."\n");	
			echo toutf($sql);	
	};
  return true;
};

function getoid($db,$inn,$name)
 {
   $stmt = sqlsrv_query ($db, "select oid,name from orgs where inn='".$inn."';");
   $coid=0;$oname='';
   if (strlen($name)>190) {$name=substr($name,0,190);};
   $name=_sql_validate_value($name);
//   echo 'inn'.$inn."\n";
//   echo($coid.":".to1251($name)."\n");
   if( $stmt === false ) 
	    {
	     execSQLP($db,"insert into orgs (name,inn) values (".$name.",'".$inn."')");
	        $sql="select oid,name from orgs where inn='"."';";
	        $stmt = sqlsrv_query ($db, $sql);
		   if( $stmt === false ) 
	    		{ echo 'error';
			echo (sqlsrv_errors()[0][2]."\n");	
			echo $sql;
		        } else
				{
				while($row = sqlsrv_fetch_array($stmt)) 
		           	{  $coid=$row[0];$oname=$row[1]; }
				sqlsrv_free_stmt($stmt);  	
				}
		

            } else  
	{
	while($row = sqlsrv_fetch_array($stmt)) 
           	{  $coid=$row[0];
		   $oname=$row[1]; }
	sqlsrv_free_stmt($stmt);  	
//        echo($coid.":".$oname."\n");

     if (($coid>0)&&($oname!=$name))
	  {
	    execSQLP($db,"update orgs set name=".$name." where oid=".$coid );
	  }
     }
    return $coid;
  }

function exec_sql($id,$args,$fn)
 { 
   $inn=$args[6];
	if ($inn==123) {$oid=0;} else {
	$oid=getoid($id,$inn,$args[9]);};
$tel=to1251($args[7]);if (strlen($tel)>19) {$tel=substr($tel,0,19);};
$eml=_sql_validate_value(to1251($args[8]));
$dat=_sql_validate_value($args[10]);
$type=_sql_validate_value($args[11]);
$bl=$args[12];if ($bl=="") {$bl=0;};
$oktmo=$args[13];if ($oktmo=="") {$oktmo="";};
 $tel=_sql_validate_value($tel);
//   print_r($args);
$n=count($args[2]); //lots cnt.
if ($n>=1)
{
 for ($i=0; $i<$n;$i++) 
   { 
  $lot=$args[2][$i];
  $sum=$args[3][$i];
  $pname=$args[4][$i];	
  $ip=$args[14][$i];
  if (strlen($pname)>398) {$pname=substr($pname,0,398);};
  $pname=_sql_validate_value($pname);
  $okpd=_sql_validate_value(to1251($args[5][$i]));

  $sql="if not exists (select * from purchases where id=". $args[0] . " and lot=".$lot.")\n".
               "BEGIN insert into dbo.purchases (id,purchasenumber,coid,maxprice,cphone,cemail,date,lot,type,name,okpd,budgetlvl,oktmo,itemprice) 
                                      
               values (".$args[0].","._sql_validate_value($args[1]).",".$oid.",".$sum.
                ",".$tel.",".$eml.",".$dat.",".
		$lot.",".$type.",".$pname.",".$okpd.",".$bl.",'".$oktmo."',".$ip.
		");".
                "END;";
//   echo $sql;
   $stmt = sqlsrv_query ($id, $sql);
  if( $stmt === false ) {
	$f=fopen('not_errors.log','a+');
	fwrite($f,$fn."\n");
	fwrite($f,toutf(sqlsrv_errors()[0][2])."\n");
	 fwrite($f,toutf($sql)."\n"); fclose($f);
            } else  {sqlsrv_free_stmt($stmt);  	}

    };
   };
};  	
 
?>

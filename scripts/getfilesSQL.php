
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
/*  $id [0] => 3615
    $purchaseno [1] => 9911111111314000980
    $pd [2] => Array  (
	            [0] => =EFE4A6348D470052E043AC1107257BE2       )
    $fn [3] => Array  (
	            [0] => Test1.txt)

    $de [4] => Array  ([0] => Test1    )
    $date [5] => 2014-01-14T04:56:05.391+04:00
    [6] => EA44
*/
$dat=_sql_validate_value($args[5]);
//   print_r($args);
$n=count($args[2]); //lots cnt.
if ($n>=1)
{
 for ($i=0; $i<$n;$i++) 
   { 
  $pd=$args[2][$i];
  $fn=$args[3][$i];
  $pname=$args[4][$i];	
  if (strlen($pname)>398) {$pname=substr($pname,0,398);};
  $pname=_sql_validate_value($pname);
  $sql=" exec AddDataFile @pd='".$pd."',@pn='".$args[1]."', @fn="._sql_validate_value($fn).", @fd=".$pname.", @dat=".$dat.";";
/*  $sql="if not exists (select * from Datafiles where contentID='". $pd . "' )\n".
               "BEGIN insert into meta_zakupki.dbo.DataFiles (purchasenumber,contentID,filename,filedescription,filedate,clientid)  
                                      
               values ('".$args[1]."',"._sql_validate_value($pd).","._sql_validate_value($fn).",".$pname.
                ",".$dat.",0);".
                "END;";
*/
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

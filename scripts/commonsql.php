<?php
// -------- check for a already loaded file 
function sql_connect($dbname = "zakupki") {
echo "\nconnecting:";
sqlsrv_configure( "WarningsReturnAsErrors", 0 );
$opt=array(		"Database" => $dbname,
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
  echo "OK\n";
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

function checkAddFile($id,$fn,$mode,$datatype) 
	{
	$d=1;	
	 $sql="begin declare @r int;execute @r=addLoadedFile @fn='".$fn."',@mode=".$mode.",@datatype=".$datatype.";".
	       "select @r;end;";
         $stmt = sqlsrv_query ($id, $sql);
         if( $stmt === false ) {
	   $f=fopen('errors.log','a+');
               fwrite($f,toutf(sqlsrv_errors()[0][2])."\n");
	       fwrite($f,toutf($sql)."\n"); 
	      fclose($f);
	        } else {
	   while($row = sqlsrv_fetch_array($stmt))  
		{    $d=$row[0];
			;unset($row);  };
		    sqlsrv_free_stmt($stmt);  	
		}
	return $d;
  };

?>
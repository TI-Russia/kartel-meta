<?php
 include "commonsql.php";
 parse_str(implode('&', array_slice($argv, 1)), $_GET);
 $sql_id=sql_connect();
 $done=$_GET['done'];
 $n=1;if ($done==100) {$n=0;};
 $sql="execute logOperation ".$n.",'fileupdatetorgi',".$done;
 $stmt = sqlsrv_query ($sql_id, $sql);
 sql_close($sql_id);
?>
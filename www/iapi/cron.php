<?php declare(strict_types=0); 
if(!isset($argv)&&(empty($_GET["act"])||$_GET["act"]!='run')) { http_response_code(404); die; }
$servername = "localhost";

ini_set('max_execution_time', 600);
ini_set("default_socket_timeout", 60);
set_time_limit(600);

include_once 'func.php';

// Create connection
$conn = sql_connect();
if($conn)
{
  $start = microtime(true);
  $stmt=sqlsrv_query($conn, 'EXEC updatePriority', array(), array("QueryTimeout" => 600));
  $time_elapsed_secs = microtime(true) - $start;
  $s=date(DATE_ATOM);
  if($stmt===false)
  {
   $s.=" Error ({$time_elapsed_secs}s)";
  }else{
   sqlsrv_free_stmt($stmt);
   $s.=" OK ({$time_elapsed_secs}s)";
  }
  file_put_contents("E:\\mkc\\sites\\gl.mkc.ru\\iapi\\cron.log",$s);
  print $s;
  sql_close($conn);
}
?>
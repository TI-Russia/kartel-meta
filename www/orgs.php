<?php
 
 include "filer.php";
 include "sql.php";

if(count($_GET) == 0) 
  {$go='form';$params='';$inn='';$oid='';$cid=''; } 
else
{ 
   $oid=getparm('oid');
   $cid=getparm('cid');
   $inn=getparm('inn');
}

 include "header.php";
 $db=sql_connect();
 $p = '';
if ($oid!='') {$sql="select * from orgs where oid='".$oid."'";};
if ($cid!='') {$sql="select * from orgs where cid='".$cid."'";};
if ($inn!='') {$sql="select * from orgs where inn='".$inn."'";};

 $stmt = sqlsrv_query ($db, $sql);
  if( $stmt === false ) {
    echo ($sql.'<br>');
    die(toutf(sqlsrv_errors()[0][2]));
     }
echo "<table border=1>";
echo "<tr><td> id </td><td>ИД картеля</td><td>ИНН</td><td>Название<td>Телефон</td><td>Телефоны, по которым есть связи</td></tr>";    
while($row = sqlsrv_fetch_array($stmt)) {

echo "<tr><td><a href=orgs.php?oid=".$row[0].">".$row[0]."</a></td><td><a href=cartels.php?cid=".$row[7].">".$row[7]."</td><td>".toutf($row[2])."</td><td>".toutf($row[1])."</td><td>".toutf($row[4])."</td><td>".toutf($row[6]).findallp($db,$row[0],'')."</td></tr>";

//    print_r($row);

};
echo "</table>";
// print_r($row);
 sql_close($db);
?>
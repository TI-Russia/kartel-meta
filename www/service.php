
<?php
global $go;
function response($d)
{ 
 global $go;
 if (isset($_POST[$d])) { $go=$d; } 
}

if((count($_POST) == 0)&&(count($_GET)==0))
  {$go='form'; } 
else
{ 
// print_r($_POST);
  $go='form';
   if (isset($_POST['fn'])) { $go=$_POST['fn']; }
   response('fileupdatecontracts');   response('fileupdatetorgi');
   response('PurchasesLTupdate');   response('ConcurentsLTupdate');
   response('fileupdatenotice');
   response('loadcontracts');         response('loadtorgi');
   response('contractsLTupdate');     response('contractsupdate');
   response('findgroups');	      response('findcartels');
   response('reload');
//
//   if (isset($_POST['fileupdatecontracts'])) { $go='fileupdatecontracts';};
//   if (isset($_GET['go'])) { $go=$_GET['go']; }
}


 
 include "filer.php";
 include "sql.php";
 include "header.php";
function logOpen($db,$id,$func,$done)
 {
  $r="";
  $sql='declare @re int; 
	execute @re=logOperation '.$id.','.$func.','.$done.';select @re;';
  $stmt = sqlsrv_query ($db, $sql);
  if( $stmt === false ) { echo ($sql.'<br>'); die(toutf(sqlsrv_errors()[0][2])); }
   while($row = sqlsrv_fetch_array($stmt)) 
  {
   $r=$row[0];
  }
//   echo '<br>logopen '.$func.':'.$r.'<br>';
  return $r;
 };
function getactualData($db,$func)
 {
  $r='&nbsp';
  $sql="select top (1) laststatus,starttime,endtime,done from workflow where pendingprocedure='".$func."' order by starttime desc";
  $stmt = sqlsrv_query ($db, $sql);
  if( $stmt === false ) { echo ($sql.'<br>'); die(toutf(sqlsrv_errors()[0][2])); }
   while($row = sqlsrv_fetch_array($stmt)) 
  {
   $d1='';if ($row[1]!=NULL) {$d1=$row[1]->format( 'd-m-Y H:i:s' );};
   $d2='';if ($row[2]!=NULL) {$d2=$row[2]->format( 'd-m-Y H:i:s' );};
   if ($row[0]=='0')
    {   $r="операция завершена. последнее выполнение: начало:".$d1." конец:".$d2; }
   else
   if ($row[0]=='1') 
    {
       $r='запущено: '.$d1.' выполнено: '.$row[3].'%';
    }
  }               
 return $r;
 };
$db=sql_connect();
//-----------------------------------------------------
echo('

<form method="post" action=""> 
<table border=1>
   <tr><td><input type=submit value="обновить данные по конкурсам c госзакупок" name="fileupdatenotice">
         </td><td>'.getactualData($db,'fileupdatenotice').'</td></tr>
   <tr><td><input type=submit value="обновить данные по контрактам с госзакупок" name="fileupdatecontracts">
         </td><td>'.getactualData($db,'fileupdatecontracts').'</td></tr>
   <tr><td><input type=submit value="обновить данные по торгам с госзакупок" name="fileupdatetorgi">
         </td><td>'.getactualData($db,'fileupdatetorgi').'</td></tr>
   <tr><td><input type=submit value="Загрузить данные по конкурсам в базу" name="loadnotice">
         </td><td>'.getactualData($db,'loadnotice').'</td></tr>
   <tr><td><input type=submit value="Загрузить данные по контрактам в базу" name="loadcontracts">
         </td><td>'.getactualData($db,'loadcontracts').'</td></tr>
   <tr><td><input type=submit value="Загрузить данные по торгам в базу" name="loadtorgi">
         </td><td>'.getactualData($db,'loadtorgi').'</td></tr>
   <tr><td><input type=submit value="Обработать закупки" name="PurchasesLTupdate">
         </td><td>'.getactualData($db,'PurchasesLTupdate').'</td></tr>
  <tr><td><input type=submit value="Обработать торги" name="ConcurentsLTupdate">
         </td><td>'.getactualData($db,'ConcurentsLTupdate').'</td></tr>

  <tr><td><input type=submit value="Обработать контракты" name="contractsLTupdate">
         </td><td>'.getactualData($db,'contractsLTupdate').'</td></tr>

   <tr><td><input type=submit value="Обработать аналитику по контрактам" name="contractsupdate">
         </td><td>'.getactualData($db,'contractsupdate').'</td></tr>
   <tr><td><input type=submit value="Найти и сформировать список групп" name="findgroups">
         </td><td>'.getactualData($db,'findgroups').'</td></tr>
   <tr><td><input type=submit value="найти и сформировать список картелей" name="findcartels">
         </td><td>'.getactualData($db,'findcartels').'</td></tr>
   <tr><td colspan=2><input type=submit value="Обновить данные на странице" name="reload">
         </td></tr>

');

//-----------------+------------------------------------
//echo $go;
switch($go){ 
case "reload":  break;
case "ConcurentsLTupdate":
case "fileupdatenotice":
case "fileupdatetorgi":
case "fileupdatecontracts":
case "loadcontracts":
case "loadtorgi":
case "loadnotice":
case "PurchasesLTupdate":
case "contractsLTupdate":
case "contractsupdate":
case "findgroups":
case "findcartels":
     {
	if (logOpen($db,2,$go,0)==0) 
//          if (true)
             {
	       echo '<br>запуск процедуры '.$go;
             } else 
	     { echo '<br>процедура '.$go.' уже запущена, дождитесь окончания';
	     }
//	break;
}; 

case "form":
default:

/*$sql='select * from workflow where laststatus>0' ;

 $stmt = sqlsrv_query ($db, $sql);
  if( $stmt === false ) { echo ($sql.'<br>'); die(toutf(sqlsrv_errors()[0][2])); }
 echo "<table border=1 cellspacing=0 cellpadding=0>";
while($row = sqlsrv_fetch_array($stmt)) {
	$d='';
	if ($row[3]!=NULL) {$d=$row[3]->format( 'd-m-Y' );};
        echo "<tr><td>".toutf($row[0])."</td><td>".toutf($row[1])."</a></td><td>".
         $d."</td><td>".$row[5]."</td></tr>";
	} ;

echo "</table>";break;


*/
}
// print_r($row);
 sql_close($db);

?>


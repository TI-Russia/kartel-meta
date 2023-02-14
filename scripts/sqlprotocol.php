
<?php
global $sql_id;
include "commonsql.php";

function getoid($id,$inn)
 {
   $stmt = sqlsrv_query ($id, "select oid from orgs where inn='".$inn."';");
   $coid=0;
   if( $stmt === false ) {
	$f=fopen('prot_errors.log','a+');
         fwrite($f,toutf(sqlsrv_errors()[0][2])."\n");
	 fwrite($f,'error finding inn:'.toutf($inn)."\n"); 
	fclose($f);
            } else  
	{
	while($row = sqlsrv_fetch_array($stmt)) 
           	{  $coid=$row[0];  }
	sqlsrv_free_stmt($stmt);  	
	}
    return $coid;
  }
function addorg($id,$inn,$name,$mail='',$phone='')
{
$sql="begin declare @r int;execute @r=addOrg @INN="._sql_validate_value($inn).",@name="._sql_validate_value($name).",@email="._sql_validate_value($mail).",@phone="._sql_validate_value($phone).";".
"select @r;end;";
  $stmt = sqlsrv_query ($id,$sql);
  $coid=0;
   if( $stmt === false ) {
	$f=fopen('prot_errors.log','a+');
         fwrite($f,toutf(sqlsrv_errors()[0][2])."\n");
	 fwrite($f,'error finding inn:'.toutf($inn)."\n"); 
	fclose($f);
            } else  
	{
	while($row = sqlsrv_fetch_array($stmt)) 
           	{  $coid=$row[0];  }
	sqlsrv_free_stmt($stmt);  	
	}
    return $coid;

}
function execSQLP($db,$sql)
{
//  echo 'running:'.$sql."\n";
  $stmt = sqlsrv_query ($db, $sql);
  sqlsrv_free_stmt($stmt);
  return true;
};

function logOpen($db,$id,$func,$done)
 {
$r='';  
$sql='declare @re int; 
	execute @re=logOperation '.$id.','.$func.','.$done.';select @re;';
  $stmt = sqlsrv_query ($db, $sql);
  if( $stmt === false ) { echo ($sql.'<br>'); die(toutf(sqlsrv_errors()[0][2])); }
   while($row = sqlsrv_fetch_array($stmt)) 
  {  $r=$row[0]; }
   sqlsrv_free_stmt($stmt);
   return $r;
 };

function exec_sql($id,$args)
 { 
//print_r($args);
 $rs=$args[1];$n=count($args[0]);
 if ((strlen($rs)>0)&&($n>=1))
{
 for ($i=0; $i<$n;$i++) 
   { $inn=$args[0][$i];
     if ($inn!="")  //нахер пустые данные
    {	
     $sum=$args[2][$i];$oid=getoid($id,$inn);
     //$phone=$args[7][$i];
     //if (strlen($phone)>19) {$phone=substr($phone,0,19);};
//     $email=$args[8][$i];
     $appid=filter_var(str_replace("-","_",$args[9][$i]), FILTER_SANITIZE_NUMBER_INT);
     $pl=$args[10][$i];
     if (strlen($appid)==0) $appid=1;
     if (strlen($appid)>17) { $appid=str_replace($rs,'',$appid);
     if (strlen($appid)==0) $appid=1;
		};  		
//	echo '>>>>>'.$i.':'.$appid.'<<<<<'."\n";
     if ($oid==0) 
	{  //add org here
	 $oid=addorg($id,$inn,$args[6][$i]);
	};
     if ($sum=="") {$sum=0;}; 	
     $tm=_sql_validate_value($args[3][$i]);
     $lot=$args[4][$i];
     $allowed=0;
     if (($args[5][$i]=='true')||($args[5][$i]=='')) {$allowed=1;};
$pla='';$oids='';$sums='';
 if ($pl>0) {$pla=', place='.$pl.' ';};
 if ($oid>0) {$oids=', oid='.$oid.' ';};
 if ($sum>0) {$sums=', sum='.$sum.' ';};
  $sql="if not exists (select * from concurents where appid=".$appid." and purchasenumber='".$rs."' and lot=".$lot. " )\n
               BEGIN insert into dbo.concurents (purchasenumber,oid,sum,appdate,lot,allowed,appid,place,protocolnumber,ef2,active) 
                                       values ('".$rs."',".$oid.",".$sum.",".$tm.",".$lot.",".$allowed.","._sql_validate_value($appid).",".$pl.","._sql_validate_value($args[11]).","._sql_validate_value($args[12]).",1) 
 end else begin 
	       update dbo.concurents set allowed=".$allowed.$pla.$oids.$sums." where appid=".$appid." and purchasenumber='".$rs."' and lot=".$lot. ";

					end;";
   $stmt = sqlsrv_query ($id, $sql);
  if( $stmt === false ) {
	$f=fopen('prot_errors.log','a+');fwrite($f,toutf(sqlsrv_errors()[0][2])."\n");
	 fwrite($f,toutf($sql)."\n"); fclose($f);
            } else  {sqlsrv_free_stmt($stmt);  	}
   };
   };
  }  	
 };
function exec_sql2020($id,$args)
 { 
 $rs=$args[0];$n=count($args[1]);
//print_r($args);
if (($n>=1)&&(strlen($rs)>0))
{
 for ($i=0; $i<$n;$i++) 
   { $inn=filter_var(str_replace("-","_",$args[2][$i]), FILTER_SANITIZE_NUMBER_INT);
//     echo $inn;
     if ($inn!="")  //нахер пустые данные
    {	
     $sum=$args[5][$i];if ($sum=="") {$sum=0;}; 	
     $tm=_sql_validate_value($args[3][$i]);
     $lot=$args[1][$i];
     $allowed=0;
     if ((strlen($args[6][$i])==0)||($args[6][$i]=='true')) {$allowed=1;};
     $pos=$args[4][$i];	

$ssu='';
$ssa='';
//echo 'allowed:'.$allowed.', pos:'.$pos;
if ($sum>0) 
    {$ssu="\n update dbo.concurents set sum=".$sum."  where sum=0 and appid=".$inn." and purchasenumber='".$rs."' and lot=".$lot. ";";};
if (($pos>0)&&($allowed==1)) 
    {$ssa="\n update dbo.concurents set allowed=".$allowed.", place=".$pos.", protocolnumber='".$args[7]."' where appid=".$inn." and purchasenumber='".$rs."' and lot=".$lot. ";";}
    else
if (($allowed==0)&&($pos<=0))
    {$ssa="\n update dbo.concurents set allowed=".$allowed.", place=0, protocolnumber='".$args[7]."'  where appid=".$inn." and purchasenumber='".$rs."' and lot=".$lot. ";";}

//echo $ssa;
if (strlen($ssa.$ssu)==0) { $ssa=" select 1;";};
  $sql="if not exists (select * from concurents where appid=".$inn." and purchasenumber='".$rs."' and lot=".$lot. " )\n
               BEGIN insert into dbo.concurents (purchasenumber,appid,sum,appdate,lot,allowed,place,protocolnumber,ef2,active) 
                                       values ('".$rs."',".$inn.",".$sum.",".$tm.",".$lot.",".$allowed.",".$pos.","._sql_validate_value($args[7]).","._sql_validate_value($args[8]).",1) 
					end else
		begin	".
	      $ssa.	
	       
	      $ssu. " end";

// echo $sql."\n";
   $stmt = sqlsrv_query ($id, $sql);
  if( $stmt === false ) {
	$f=fopen('prot_errors.log','a+');fwrite($f,toutf(sqlsrv_errors()[0][2])."\n");
	 fwrite($f,toutf($sql)."\n"); fclose($f);
            } else  {sqlsrv_free_stmt($stmt);  	}
   };
   };
  }  	
 }

function exec_sql_cancel($id,$args)
 { 
 $rs=$args[0];
 $pn=$args[1];
 $pn1=$args[2];
 $sql=" update concurents set active=0 where purchasenumber='".$rs."' and
 (protocolnumber='".$pn ."'";
if (strlen($pn1)>2) {$sql=$sql. " or protocolnumber='".$pn1."');";} else
                    {$sql=$sql . ")";};
   $stmt = sqlsrv_query ($id, $sql);
  if( $stmt === false ) {
	$f=fopen('prot_errors.log','a+');fwrite($f,toutf(sqlsrv_errors()[0][2])."\n");
	 fwrite($f,toutf($sql)."\n"); fclose($f);
            } else  {sqlsrv_free_stmt($stmt);  	}
   };

?>

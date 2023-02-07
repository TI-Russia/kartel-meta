<?php declare(strict_types=0); 
if(empty($_GET["act"])) { http_response_code(404); die; }
$servername = "localhost";

include_once 'func.php';

define( "ERROR", "error");
define( "OK", "ok" );

// Create connection
$conn = sql_connect();

class Result
{
  public $result;
}

$result = new Result();

// Check connection
if ($conn) 
{
  switch($_GET["act"])
  {
     case "new":
        $id=sql_exec_new($conn);
        if($id!=null)
        {
         $iid=sql_select_uid($conn, $id);
         if($iid!=null)
         {
          // В лог создание пользователя
          sql_exec_log($conn,$iid,0);
         }
        }
        $result->result = array("id" => $id);
        break;
     case "get":
         $id=$_GET["id"];
         if(isset($id)&&strlen($id)<42)
         {
           $iid=sql_select_uid($conn, $id);
           if($iid!=null)
           {
             // Пишем в лог обращение
//             sql_exec_log($conn,$iid,1);
             $res=sql_select_list($conn,$iid);
             $exts=sql_select_exts($conn);
             $ver=get_ver();
             sql_exec_log($conn,$iid,isset($res)?1000+count($res):999);
             if(isset($res)){
              $result->result = array("data" => array("ver" => $ver, "list" => $res, "exts" => $exts));
             }
           }
         }
        break;
     case "gex":
         $id=$_GET["id"];
         if(isset($id)&&strlen($id)<42)
         {
           $iid=sql_select_uid($conn, $id);
           if($iid!=null)
           {
             // Пишем в лог обращение
//             sql_exec_log($conn,$iid,1);
             $res=sql_select_xlist($conn,$iid);
             $exts=sql_select_exts($conn);
             $ver=getFileVersionInfo('client\\cc\\client.exe')['FileVersion'].'+'.
             getFileVersionInfo('client\\tc\\7z.dll')['FileVersion'].'+'.
             getFileVersionInfo('client\\tc\\exiftool.exe')['FileVersion'];
             sql_exec_log($conn,$iid,isset($res)?1000+count($res):999);
             if(isset($res)){
              $result->result = array("data" => array("ver" => $ver, "list" => $res, "exts" => $exts));
             }
           }
         }
        break;
     case "put":
         $id=$_GET["id"];
         if(isset($id)&&strlen($id)<42)
         {
           $xiid=sql_select_uidx($conn, $id);
           if($xiid!=null)
           {
            if($xiid[1]<3)
            {
             $iid=$xiid[0];
             // Пишем в лог обращение
             sql_exec_log($conn,$iid,2);
             $data=jGet($iid);
             if($data&&isset($data->id)&&$data->id==$id)
             {
               $res=save_data($conn,$iid,$data->list);
// временно, просто чтобы не считало ошибкой
               $result->result = array("list" => "Got it", "X" => "#$iid", "R" => $res);
             }
            // затычка для древних возвращенцев с неактуальными данными
            }else{$result->result = array("list" => "Got it", "X" => '#'.$xiid[0], "R" => 1, "P" => $xiid[1]);}
           }
         }
        break;
     case "stt":
        $res=get_stat($conn);
        if($res!=null)
        {
echo '<head>
<link rel="stylesheet" href="/css/styles.css"> 
</head>';
          $u=null;
          echo date(DATE_ATOM).'<br>';
          echo 'Last cron process: '.file_get_contents('cron.log');
          echo '<table border=1 cellpadding=9 style="text-align:center"><tr><td>User</td><td>Last active</td><td>Status</td><td>Count</td></tr>';
          $o=0;
          foreach($res as $r)
          { $tim=1;
            if($r[0]!=$u)
            {
             $tim=new DateTimeImmutable($r[1]);
             $target = new DateTimeImmutable();
             $interval = $tim->diff($target);
             $tim=24*(31*($interval->m+$interval->y)+$interval->d)+$interval->h;
             $mins=60*$tim+$interval->i;
             if($tim<99)
             {
              echo '<tr>';
              $diff=$tim>1?"$tim hours":"$mins minutes";
              $mark=$tim<16&&$mins>22?' bgcolor=red':($tim>=16?' bgcolor=gray':'');
              echo "<td>$r[0]</td><td $mark>$r[1] ($diff)</td>";
              $u=$r[0];
             }else{
              $o+=$r[3];
             }
            }else{echo '<tr><td colspan=2/>';}
            if($tim<99)
            {
             switch($r[2])
             {case -3:case -4:case -5: case -6: $s='REWORK'; break; case -2: $s='WORK';break;case -1: $s='PREP';case 0: $s='OK'; break; default: $s=$r[2];}
             $fnum=number_format($r[3],0,'',' ');
             echo "<td>$s</td><td>$fnum</td></tr>";
            }
          }
          if($o>0) echo '<tr><td colspan=3>OTHERS</td><td>'.number_format($o,0,'',' ').'</td></tr>';
          echo '</table><br><br>';
          $res=get_zstat($conn);
          if($res!=null)
          {
            $c=0;
            echo '<table border=1 cellpadding=9 style="text-align:center"><tr><td>Priority</td><td>Count</td></tr>';
            foreach($res as $r)
            {
              if($r[0]===null){$r[0]='IGNORED';}else{$c+=$r[1];}
              echo "<tr><td>$r[0]</td><td>".number_format($r[1],0,'',' ').'</td></tr>';
            }
            echo "<tr><td>TOTAL</td><td>".number_format($c,0,'',' ').'</td></tr></table>';
          }
        }
        break;
  }
}

sql_close($conn);
if(isset($result->result))
{
  echo json_encode($result);
}else{
  http_response_code(501); die;
}

<?php

// Коннект
function sql_connect() {
 sqlsrv_configure( "WarningsReturnAsErrors", 0 );
 ini_set('mssql.charset', 'UTF-8');
 $sql_id=sqlsrv_connect('localhost', array(
//			'Database' => 'meta_junior',
			'Database' => 'meta_zakupki',
                        'CharacterSet' => 'UTF-8',
                        'ReturnDatesAsStrings'=>true,
			'UID' => 'sa',
			'PWD' => 'sa11011'));
 return $sql_id;
};

// Закрытие коннекта
function sql_close($conn) { return sqlsrv_close($conn); };

// Создать пользователя
function sql_exec_new($db)
{
  $id=guidv4();
  $stmt=sqlsrv_query($db, 'INSERT INTO Users(UID)VALUES(?)',array($id));
  if($stmt===false)
  {
   return null;
  }else{
   sqlsrv_free_stmt($stmt);
   return $id;
  }	
}

// Получить числовой ID пользователя по UID
function sql_select_uid($db,$uid)
{
  $stmt=sqlsrv_query($db, 'SELECT ID FROM Users WHERE UID=?',array($uid));
  if($stmt===false)
  {
   return null;
  }else{
   $res=sqlsrv_fetch_array($stmt) ?? false;
   sqlsrv_free_stmt($stmt);
   return $res===false||count($res)==0?null:$res[0];
  }	
}

// Получить числовой ID пользователя по UID
function sql_select_uidx($db,$uid)
{
  $stmt=sqlsrv_query($db, 'SELECT ID,DATEDIFF(DAY,LASTactive,GETDATE()) FROM Users WHERE UID=?',array($uid));
  if($stmt===false)
  {
   return null;
  }else{
   $res=sqlsrv_fetch_array($stmt) ?? false;
   sqlsrv_free_stmt($stmt);
   return $res===false||count($res)==0?null:$res;
  }	
}

// Получить список загружаемых данных
function sql_select_list($db,$id)
{
  $stmt=sqlsrv_query($db, 'EXEC getFiles @id=?', [$id]);
  if($stmt===false)
  {
   return null;
  }else{
   $result=array();
   while($res=sqlsrv_fetch_array($stmt))
   {
    array_push($result,$res[0]);
   }
   sqlsrv_free_stmt($stmt);
   return $result;
  }	
}

// Получить список загружаемых данных
function sql_select_xlist($db,$id)
{
  $stmt=sqlsrv_query($db, 'EXEC getXFiles @id=?', [$id]);
  if($stmt===false)
  {
   return null;
  }else{
   $result=array();
   while($res=sqlsrv_fetch_array($stmt))
   {
    array_push($result,$res[0]);
   }
   sqlsrv_free_stmt($stmt);
   return $result;
  }	
}

// Получить список загружаемых данных
function sql_select_exts($db)
{
  $stmt=sqlsrv_query($db, 'SELECT ext FROM Exts');
  if($stmt===false)
  {
   return null;
  }else{
   $result=array();
   while($res=sqlsrv_fetch_array($stmt))
   {
    array_push($result,$res[0]);
   }
   sqlsrv_free_stmt($stmt);
   return $result;
  }	
}

// Запись действия в лог
function sql_exec_log($db,$uid,$act)
{
/*  $ip=$_SERVER['REMOTE_ADDR'];
  $stmt=sqlsrv_query($db, 'INSERT INTO UserLogs(UserID,Action,IP)VALUES(?,?,?)',array($uid,$act,$ip));
  if($stmt===false)
  {
   echo print_r(sqlsrv_errors(), true);   return null;
  }else{
   sqlsrv_free_stmt($stmt);
   return 1;
  }	*/
  $stmt=sqlsrv_query($db, 'UPDATE Users SET LastActive=GETDATE() WHERE ID=?',array($uid));
  if($stmt===false)
  {
   echo print_r(sqlsrv_errors(), true);   return null;
  }else{
   sqlsrv_free_stmt($stmt);
  }
 return 1;
}

// Прячемся от обращений
if(!isset($servername))
{
http_response_code(404); die;
}

// Получить GUID без внешних функций (которых нет)
function guidv4($data = null) {
    // Generate 16 bytes (128 bits) of random data or use the data passed into the function.
    $data = $data ?? random_bytes(16);
    assert(strlen($data) == 16);

    // Set version to 0100
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    // Set bits 6-7 to 10
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

    // Output the 36 character UUID.
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

// Получить и откорректировать непечатные символы в JSON
function jGet($iid)
{
 $json_params=mb_convert_encoding(file_get_contents("php://input"), 'UTF-8', 'UTF-8');
 file_put_contents("E:\\mkc\\sites\\gl.mkc.ru\\iapi\\json".$iid,$json_params);
 if (strlen($json_params)>0 && ($dec=json_decode($json_params)) && json_last_error()==JSON_ERROR_NONE)
 {return $dec;}else{return null;}
}

// Сохранить данные по пришедшим файлам
function save_data($db,$id,$files)
{
 // Структура
 // { userid, list: [ { contentid, status, optional.list: [ { filename, tags(XML) } ] } ] }
 if(isset($files)&&count($files))
 {
  $allok=1;
  // Блоки с данными
  foreach($files as $file)
  {
   if(isset($file)&&isset($file->id)&&isset($file->status))
   {
     $ok=1;
     if($file->status==0&&isset($file->list))
     {
      // Запись тегов (для каждого файла, которых в случае архива может быть много)
      foreach($file->list as $subfile)
      {
        $xml='<root>'.$subfile->tags.'</root>';
        $stmt=sqlsrv_query($db, 'EXEC addMetaFile @fileid=?,@filename=?,@XML=?', [$file->id, $subfile->filename, $xml]);
        if($stmt===false)
        {
         $ok=0;
         $allok=0;
        }
        sqlsrv_free_stmt($stmt);
      }
     }
     // Запись состояния
     if($ok)
     {
      $stmt=sqlsrv_query($db, 'EXEC updateFileInfo @fileid=?,@status=?,@user=?', [$file->id, $file->status, $id]);
      if($stmt===false)
      {
       $allok=0;
      }
     }
     sqlsrv_free_stmt($stmt);
   }
  }
  return $allok;
 }
 return 0;
}

// Получаем статистику по пользователям
function get_stat($db)
{
  $stmt=sqlsrv_query($db, 'SELECT Users.ID,LastActive,A.* FROM Users CROSS APPLY(SELECT [status],COUNT(*) FROM Datafiles where clientid=Users.ID GROUP BY [status]) AS A(status,cnt)ORDER BY Users.ID,A.[status]');
  if($stmt===false)
  {
   return null;
  }else{
   $result=array();
   while($res=sqlsrv_fetch_array($stmt))
   {
    array_push($result,$res);
   }
   sqlsrv_free_stmt($stmt);
   return $result;
  }	
}

// Статистика по файлам
function get_zstat($db)
{
  $stmt=sqlsrv_query($db, 'SELECT [priority],COUNT(*) FROM Datafiles WHERE [status] IS NULL GROUP BY [priority] ORDER BY [priority] DESC');
  if($stmt===false)
  {
   return null;
  }else{
   $result=array();
   while($res=sqlsrv_fetch_array($stmt))
   {
    array_push($result,$res);
   }
   sqlsrv_free_stmt($stmt);
   return $result;
  }	
}

// Читаем версию клиента
function get_ver()
{
 $ver=0;
 $handle=fopen("client\\client.pl", "r");
 if ($handle)
 {
  while (($line = fgets($handle)) !== false)
  {
   if(preg_match('/\s*\$version\s*=\s*(\d+);/',$line,$matches))
   {
     $ver=$matches[1];
     break;
   }
  }
 fclose($handle);
 } 
 return $ver;
}

function getFileVersionInfo($filename,$encoding='UTF-8'){
    $dat = file_get_contents($filename);
    if($pos=strrpos($dat,mb_convert_encoding('VS_VERSION_INFO','UTF-16LE'))){
        $pos-= 6;
        $six = unpack('v*',substr($dat,$pos,6));
        $dat = substr($dat,$pos,$six[1]);
        if($pos=strpos($dat,mb_convert_encoding('StringFileInfo','UTF-16LE'))){
            $pos+= 54;
            $res = [];
            $six = unpack('v*',substr($dat,$pos,6));
            while(count($six)>2 && ($six[2]||$six[3])){
                $nul = strpos($dat,"\0\0\0",$pos+6)+1;
                $key = mb_convert_encoding(substr($dat,$pos+6,$nul-$pos-6),$encoding,'UTF-16LE');
                $val = mb_convert_encoding(substr($dat,ceil(($nul+2)/4)*4,$six[2]*2-2),$encoding,'UTF-16LE');
                $res[$key] = $val;
                $pos+= ceil($six[1]/4)*4;
                $six = unpack('v*',substr($dat,$pos,6));
            }
            return $res;
        }
    }
}
function sql_info($db,$id)
{
 if(strlen($id)==32)
 {
  $stmt=sqlsrv_query($db, 'SELECT clientID,d.contentID,[priority],[status],d.filename,f.filename,t.tag,t.value FROM Datafiles d left join metafiles f on f.contentid=d.contentid left join metatags t on t.[file]=f.id WHERE d.contentid=?', [$id]);
  if($stmt===false)
  {
   return null;
  }else{
   $result=array();
   while($res=sqlsrv_fetch_array($stmt))
   {
    array_push($result,$res);
   }
   sqlsrv_free_stmt($stmt);
   return $result;
  }	
 }elseif(strlen($id)<=22)
 {
  $stmt=sqlsrv_query($db, 'SELECT clientID,contentID,[priority],[status] FROM Datafiles WHERE purchasenumber=?', [$id]);
  if($stmt===false)
  {
   return null;
  }else{
   $result=array();
   while($res=sqlsrv_fetch_array($stmt))
   {
    array_push($result,$res);
   }
   sqlsrv_free_stmt($stmt);
   return $result;
  }	
 }
 return null;
}
?>
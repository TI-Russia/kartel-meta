<?php
1
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
            while(count($six)>2 && ($six[2] || $six[3])){
                $nul = strpos($dat,"\0\0\0",$pos+6)+1;
                $key = mb_convert_encoding(substr($dat,$pos+6,$nul-$pos-6),$encoding,'UTF-16LE');
                $val = mb_convert_encoding(substr($dat,ceil(($nul+2)/4)*4,$six[2]*2-2),$encoding,'UTF-16LE');
                $res[$key] = $val;
                $pos+= ceil($six[1]/4)*4;
                $six = unpack('v*',substr($dat,$pos,6));
            }
echo print_r($six).'<br>';
            return $res;
        }
    }
}
echo print_r(getFileVersionInfo("cc/exiftool.exe"));
echo '<br>';
echo print_r(getFileVersionInfo("cc/7z.dll"));
echo '<br>';
echo print_r(getFileVersionInfo("cc/client.exe"));
?>
<!DOCTYPE html>
<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="icon" href="favicon.ico" type="image/x-icon">
<?php
$myurl=$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];
if (strpos($myurl,'index')>0) {
    echo '
        <link rel="stylesheet" href="css/mg_style.css"> 
        <link rel="stylesheet" href="css/styles_new.css"> 
    ';
} else {
    echo '
        <link rel="stylesheet" href="/css/styles.css"> 
        <link rel="stylesheet" href="/css/mg_style.css"> 
    ';
}

$style='
<style type="text/css">
tr
.c_red   { background-color: #FF3030;}
.c_green { background-color: #10EF10;}
.c_missed { background-color: #f3D8D8;}'./*#fa6600;}*/
'.c_blue  { background-color: #00aeef; color:#ffffff;}
.c_blue a { color:#ffffff;}
.c_blue a:hover { color:#000000;}
}
.c_red   { background-color: #FF3030;}
.c_green { background-color: #10EF10;}
span
.c_missed { background-color: #F3D8D8;}'. /*#fa6600 */
'</style>';
 echo $style;
if(isset($script))echo $script;
if(!isset($onload))$onload='';
echo "<body$onload>";
?>
</head>
<body style="margin: 0px; background: rgb(255, 255, 255); " >
<div class="kartelescop screen">
<div class="header">
<div class="ebalarect"> 
<h1 class="ebalatext"></h1>
</div>
 <div class="rowBoxEx">
<?php
if (strpos($myurl,'index')==0) {
if ((strpos($myurl,'scope.php')!=0)||(strpos($myurl,'about.php')!=0))
{
 echo '<div class="back-button">
	<div class="text_back_label roboto-menu--small-button"><a href="#" onclick="document.location.href=\'index.php\'";return false;"><span class="menu1">← На главную</span></a></div>
 </div>';}
 else
{
$urlpage='cartelescope.php';
if (strpos($myurl,'meta')>0) $urlpage='metascope.php';

echo '
 <div class="back-button">'.
	'<div class="text_back_label roboto-menu--small-button"><a href="#" onclick="document.location.href=\''.$urlpage.'\';return false;"><span class="menu1">← Вернуться</span></a></div>'.
	//'<div class="text_back_label roboto-menu--small-button"><a href="#" onclick="history.back();return false;"><span class="menu1">← Вернуться</span></a></div>'.
 '</div>';};
};
echo' <div class="frame-7">';
 	if (strpos($myurl,'meta')>0) 
	{echo '<img class="ts_logo" src="./images/metascop.svg"  alt="metascop-logo">
		<div class="ts_header_deviz helios-small-description">Для поиска вертикальных сговоров</div>';
	} else  if ((strpos($myurl,'index.php')>0)||(strpos($myurl,'about.php')>0))
         	{echo '<img class="ts_logo" src="./images/tenderscop.svg"  alt="tenderscop-logo">
		<div class="ts_header_deviz helios-small-description">Цифровые инструменты общественного контроля публичных закупок.</div>';
	}
	else echo 	'<img class="ts_logo" src="/images/kartelescop-logo.svg"  alt="kartelescop-logo">
			<div class="ts_header_deviz helios-small-description">Для поиска горизонтальных сговоров</div>';
?>
</div>
 <div class="menu">
   <div class="rowBox">

     <div class="hdrLabel roboto-menu--small-button"><a href="about.php"><span class="menu1">О проекте</span></a></div>
     <div class="hdrLabel roboto-menu--small-button"><a href='pdf/about_metadata.pdf'><span class="menu1">О метаданных</span></a></div>
     <div class="hdrLabel roboto-menu--small-button"><a href='https://forms.gle/Gj8m7ARqmp7p1t5G9'><span class="menu1">Обратная связь</span></a></div>
     <div class="hdrLabel roboto-menu--small-button"><a href="https://drive.google.com/file/d/1IhdEhwnl0bDsvCXwKRcGWfWTlrieK9Va/view?usp=share_link"><span class="menu-highlighted">Металаборатория</span></a></div>
      
   </div> 
 </div>
 </div>
</div>
<div class="tscontent">

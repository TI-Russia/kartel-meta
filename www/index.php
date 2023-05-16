
<?php
 include "header.php"; 
 include "filer.php";
 include "sql.php";
?>

<table width=100% border=0 align=left style="margin-bottom: 48px">
<tr><td colspan=3><div class="about_text">Команда независимых экспертов по госзакупкам создала базу данных и документации из Единой информационной системы в сфере закупок. <strong>Эта информация позволяет обнаруживать признаки картелей и сговоров между заказчиками и участниками закупочных процедур.</strong></div>
<div class="spacer" style="height:20px"></div>
</td></tr>
<tr>
<td>
 <table width="100%" border=1> 
<tr>                        
<td class="product-name"><div class="spacer" style="height:30px"></div>
      <img src="/images/kartelescop.svg" alt="kartelescope"><br>для поиска горизонтальных сговоров
	<div class="spacer" style="height:20px"></div>
</td>
  <td class="icon-outer"><img src="/images/kartel-icon.svg" alt="kartelescope"></td>
</tr>
<tr>
<td colspan=2 class="product-text" style="height:160px" >
<div class="spacer" style="height:30px"></div>
Данные о закупках, включая группы компаний — участников процедур с совпадающими контактными данными (признак горизонтального сговора).
<div class="spacer" style="height:30px"></div>
</td></tr>
<tr><td colspan=2 class="boxed-outer">
 <div class="boxed" OnClick="document.location.href='cartelescope.php'">
	<span>Изучить данные</span>
	 <img align=right class="arrow-black" src="./images/arrow-white.svg" alt="arrow-black">	
</div>
</td></tr></table>
</td>
<td width=5%>&nbsp</td>

<td>
 <table width="100%" border=1> 
<tr>
  <td class="product-name"><div class="spacer" style="height:30px"></div>
      <img src="/images/metascop.svg" alt="kartelescope"><br>для поиска вертикальных сговоров
	<div class="spacer" style="height:20px"></div>
</td>
  <td class="icon-outer"><img src="/images/meta-icon.svg" alt="kartelescope"></td>
</tr>
<tr><td colspan=2 class="product-text" style="height:160px"  >
<div class="spacer" style="height:30px"></div>
Метаданные документов, размещенных заказчиками при проведении закупок, включая файлы с цифровыми следами компаний ー победителей торгов (признак вертикального сговора).
<div class="spacer" style="height:30px"></div>
</td></tr>
<tr><td colspan=2 class="boxed-outer">

<div class="boxed" OnClick="document.location.href='metascope.php'">

	<span>Изучить данные</span>
	 <img align=right class="arrow-black" src="./images/arrow-white.svg" alt="arrow-black">	
</div>
</td></tr></table>
</td>

</td></tr></table>

<div class="how">
    <div class="how-main">Как пользоваться ТЕНДЕРСКОПОМ?</div>
    <div class="how-icon"><a href="https://t.me/"><img src="images/11.png" /></a></div>
    <div class="how-text"><a href="https://t.me/">Смотрите наше видео</a></div>
    <div class="-how-icon"><a href="https://t.me/"><img src="images/12.png" /></a></div>
    <div class="how-text"><a href="https://t.me/">Читайте инструкцию в PDF</a></div>
</div>

<?php
include "footer.php";
?>


<?php
 include "header.php"; 
 include "filer.php";
 include "sql.php";
?>

<span class="relative"><center>

<table width=95% border=0 align=left>
<tr><td colspan=3><span class="about_text">Команда независимых экспертов по госзакупкам создала базу данных и документации из Единой информационной системы в сфере закупок. Эта информация позволяет обнаруживать признаки картелей и сговоров между заказчиками и участниками закупочных процедур.</span>
<div class="spacer" style="height:20px"></div>
</td></tr>
<tr>
<td>
 <table width="100%" border=1> 
<tr>                        
<td><div class="spacer" style="height:30px"></div>
      <img src="/images/kartelescop.svg" alt="kartelescope"><br>для поиска горизонтальных сговоров
	<div class="spacer" style="height:20px"></div>
</td>
  <td><img src="/images/kartel-icon.svg" alt="kartelescope"></td>
</tr>
<tr>
<td colspan=2 class="footer_text" style="height:160px" >
<div class="spacer" style="height:30px"></div>
&nbsp;&nbsp;Данные о конкурентных закупках с 2014 года, включая 26 900 групп компаний — участников конкурсов с совпадающими контактными данными (признак горизонтального сговора).
<div class="spacer" style="height:30px"></div>
</td></tr>
<tr><td colspan=2>
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
  <td><div class="spacer" style="height:30px"></div>
      <img src="/images/metascop.svg" alt="kartelescope"><br>для поиска вертикальных сговоров
	<div class="spacer" style="height:20px"></div>
</td>
  <td><img src="/images/meta-icon.svg" alt="kartelescope"></td>
</tr>
<tr><td colspan=2 class="footer_text" style="height:160px"  >
<div class="spacer" style="height:30px"></div>
&nbsp;&nbsp;Метаданные, извлеченные из 44,8 млн файлов, размещенных заказчиками при проведении закупок, включая файлы с цифровыми следами компаний ー победителей торгов (признак вертикального сговора).
<div class="spacer" style="height:30px"></div>
</td></tr>
<tr><td colspan=2>

<div class="boxed" OnClick="document.location.href='metascope.php'">

	<span>Изучить данные</span>
	 <img align=right class="arrow-black" src="./images/arrow-white.svg" alt="arrow-black">	
</div>
</td></tr></table>
</td>

</span></td></tr></table>
';
<?php
include "footer.php";
?>

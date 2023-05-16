<?php
 include "header.php"; 
 include "filer.php";
 include "sql.php";
 echo '
<table border=0 width=100% >
<tr><td width="162px">	<div class="spacer" style="height:20px"></div>
			<div class="x-xM67Cj">ИСКАТЬ:</div> </td>
<td><span class="relative">
 <div class="line-menu" OnClick="document.location.href=\'meta.php\'"><div class="text_label helioscond-bold-white-22px">
	<span class="helioscond-bold-log-cabin-22px">ПО МЕТАДАННЫМ</span>
	<span class="helioscond-bold-cerulean-22px"> И ЗАКУПКАМ</span></div>
	<img class="arrow-black" src="./images/arrow-black.svg"  alt="arrow-black" OnClick="document.location.href=\'meta.php\'">

</div>
<div class="line-menu" OnClick="document.location.href=\'metapairs.php\'"><div class="text_label helioscond-bold-white-22px">
	<span class="helioscond-bold-log-cabin-22px">ПО ПАРАМ </span>
	<span class="helioscond-bold-cerulean-22px"> ПОСТАВЩИК-ЗАКАЗЧИК</span>
  </div>
	 <img class="arrow-black" src="./images/arrow-black.svg" alt="arrow-black">	
</div>
</span>
</td></tr>
<tr><td><div class="spacer" style="height:20px"></div>	<div class="x-xM67Cj">СМОТРЕТЬ:</div> </td>
<td>
<span class="relative">
 <div class="line-menu" OnClick="document.location.href=\'metahits.php\'"><div class="text_label helioscond-bold-white-22px">
      <span class="helioscond-bold-log-cabin-22px">ПОБЕДИТЕЛИ </span>
      <span class="helioscond-bold-cerulean-22px">С СОВПАДАЮЩИМИ МЕТАДАННЫМИ</span></div>
        <img class="arrow-black" src="images/arrow-black.svg" alt="arrow-black"></div>
  <div class="line-menu" OnClick="document.location.href=\'metahitsxx.php\'"><div class="text_label helioscond-bold-white-22px">
      <span class="helioscond-bold-log-cabin-22px">СОТРУДНИКИ ПОСТАВЩИКОВ </span>
      <span class="helioscond-bold-cerulean-22px">В МЕТАДАННЫХ КОНКУРСОВ</span></div>
	<img class="arrow-black" src="./images/arrow-black.svg" alt="arrow-black"></div>
  <div class="line-menu" OnClick="document.location.href=\'metafun.php\'"><div class="text_label helioscond-bold-white-22px">
      <span class="helioscond-bold-log-cabin-22px"> РЕЙТИНГИ </span>
      <span class="helioscond-bold-cerulean-22px"> МЕТАДАННЫХ </span></div>
	<img class="arrow-black" src="./images/arrow-black.svg" alt="arrow-black"></div>

</span>

</td></tr>
</table>
';
/* echo "
<div class='navbar'>      
<span class='boxed'><a href=meta.php>Поиск и просмотр данных по закупкам с метаданными</a><br></span>
        <br>
       <span class='boxed' style='top:80px;'><a href=metahits.php>Поставщики с совпадающими метаданными </a><br></span>
       <span class='boxed' style='top:160px;'><a href=metahitsEx.php>Поставщики с признаками сговора с заказчиком</a><br></span>
       <span class='boxed' style='top:240px;'><a href=metahitsxx.php>Сотрудники поставщиков в метаданных закупок</a><br></span>
       <span class='boxed' style='top:320px;'><a href=groups.php>'Рейтинги' метаданных</a><br></span>
</div>";

 echo "<div class='agent'>";*/
include "footer.php";
?>

<div style="height= 100vh; background: #ffffff ; 
background-repeat: no-repeat;
position: relative;
background-size: 100% auto;
background-attachment: scroll;">
<br>


<?php
 include "header.php"; 
 include "filer.php";
 include "sql.php";
 echo '
<table border=0 width=100% >
<tr valign="center"><td width="162px" >
<div class="spacer" style="height:20px"></div>
<div class="x-xM67Cj" >ИСКАТЬ:</div></td>
<td><span class="relative">
 <div class="line-menu" OnClick="document.location.href=\'orgsex.php\'"><div class="text_label helioscond-bold-white-22px">
	<span class="helioscond-bold-log-cabin-22px">ПО РЕКВИЗИТАМ</span>
	<span class="helioscond-bold-cerulean-22px"> ОРГАНИЗАЦИИ</span></div>
	<img class="arrow-black" src="./images/arrow-black.svg"  alt="arrow-black">
</div>
<div class="line-menu" OnClick="document.location.href=\'purchases.php\'"><div class="text_label helioscond-bold-white-22px">
	<span class="helioscond-bold-log-cabin-22px">ПО ПАРАМЕТРАМ</span>
	<span class="helioscond-bold-cerulean-22px"> ЗАКУПКИ</span>
  </div>
	 <img class="arrow-black" src="./images/arrow-black.svg" alt="arrow-black">	
</div>
 <div class="line-menu" OnClick="document.location.href=\'contractsex.php\'">
         <div class="text_label helioscond-bold-white-22px">
		<span class="helioscond-bold-log-cabin-22px">ПО ДАННЫМ</span>
		<span class="helioscond-bold-cerulean-22px"> КОНТРАКТА</span>
	</div>
	<img class="arrow-black" src="./images/arrow-black.svg" alt="arrow-black">
 </div>
</span>
</td></tr>
<tr><td>
<div class="spacer" style="height:20px"></div>
<div class="x-xM67Cj">СМОТРЕТЬ:</div> </td>
<td>
<span class="relative">
 <div class="line-menu" OnClick="document.location.href=\'groups.php\'"><div class="text_label helioscond-bold-white-22px">
      <span class="helioscond-bold-log-cabin-22px">ГРУППЫ КОМПАНИЙ </span>
      <span class="helioscond-bold-cerulean-22px">С СОВПАДАЮЩИМИ КОНТАКТАМИ</span></div>
 <img class="arrow-black" src="images/arrow-black.svg" alt="arrow-black"></div>
  <div class="line-menu" OnClick="document.location.href=\'cartels.php\'"><div class="text_label helioscond-bold-white-22px">
      <span class="helioscond-bold-log-cabin-22px">ГРУППЫ КОМПАНИЙ </span>
      <span class="helioscond-bold-cerulean-22px">С ПРИЗНАКАМИ КАРТЕЛЕЙ</span></div>
	<img class="arrow-black" src="./images/arrow-black.svg" alt="arrow-black"></div>

</span>

</td></tr>
</table>
';
/*<div class='navbar'>      
<span class='boxed'><a href=orgsex.php>Поиск и просмотр данных по организациям</a><br></span>
        <br>
       <span class='boxed' style='top:80px;'><a href=purchases.php>Поиск и просмотр сведений о закупках </a><br></span>
       <span class='boxed' style='top:160px;'><a href=contractsex.php>Поиск и просмотр сведений по контрактам</a><br></span>
       <span class='boxed' style='top:240px;'><a href=groups.php>Просмотр групп компаний</a><br></span>
       <span class='boxed' style='top:320px;'><a href=cartels.php>Просмотр сведений о картелях</a></span><br>
</div>";
*/
include "footer.php";
?>
</div>
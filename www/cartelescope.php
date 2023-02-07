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
 echo "
<div class='navbar'>      
<span class='boxed'><a href=orgsex.php>Поиск и просмотр данных по организациям</a><br></span>
        <br>
       <span class='boxed' style='top:80px;'><a href=purchases.php>Поиск и просмотр сведений о закупках </a><br></span>
       <span class='boxed' style='top:160px;'><a href=contractsex.php>Поиск и просмотр сведений по контрактам</a><br></span>
       <span class='boxed' style='top:240px;'><a href=groups.php>Просмотр групп компаний</a><br></span>
       <span class='boxed' style='top:320px;'><a href=cartels.php>Просмотр сведений о картелях</a></span><br>
</div>";

 echo "<div class='agent'>";
include "footer.php";
?>
</div>
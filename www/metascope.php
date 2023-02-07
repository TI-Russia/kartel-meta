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
<span class='boxed'><a href=meta.php>Поиск и просмотр данных по закупкам с метаданными</a><br></span>
        <br>
       <span class='boxed' style='top:80px;'><a href=metahits.php>Поставщики с совпадающими метаданными </a><br></span>
       <span class='boxed' style='top:160px;'><a href=metahitsEx.php>Поставщики с признаками сговора с конкретным заказчиком</a><br></span>
       <span class='boxed' style='top:240px;'><a href=metahitsxx.php>Сотрудники поставщиков в метаданных </a><br></span>
       <span class='boxed' style='top:320px;'><a href=groups.php>'Рейтинги' метаданных</a><br></span>
</div>";

 echo "<div class='agent'>";
include "footer.php";
?>
</div>
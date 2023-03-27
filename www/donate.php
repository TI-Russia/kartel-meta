
<?php
 include "header.php"; 
 include "filer.php";
 include "sql.php";

 echo '
<span class="relative"><center>
<table width=70% border=0><tr><td>
 <div class="line-menu" OnClick="document.location.href=\'cartelescope.php\'"><div class="text_label helioscond-bold-white-22px">
	<span class="helioscond-bold-log-cabin-22px">КАРТЕЛЕ</span>
	<span class="helioscond-bold-cerulean-22px">СКОП</span></div>
	<img class="arrow-black" src="./images/arrow-black.svg"  alt="arrow-black">
</div>
<div class="line-menu" OnClick="document.location.href=\'metascope.php\'"><div class="text_label helioscond-bold-white-22px">
	<span class="helioscond-bold-log-cabin-22px">МЕТА</span>
	<span class="helioscond-bold-cerulean-22px">СКОП</span>
  </div>
	 <img class="arrow-black" src="./images/arrow-black.svg" alt="arrow-black">	
</div>
</span></td></tr></table>
';

include "footer.php";
?>
</div>
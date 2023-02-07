<?php

function printpages($pages,$page,$order,$reg)
{
if ($pages>0) 

{ //draw navbar
if ($order!='') { $order='order='.$order.'&';};
if (($reg!=0)&&($reg!='0')) { $reg='region='.$reg.'&';} else 
 {$reg='';};

$href='<a href="' . $_SERVER['SCRIPT_NAME'] . '?'.$reg.$order.'page=';
echo "\n<br>Страницы: ";


// _________________ начало блока 1 _________________
echo '<table border=0 ><tr><td>';
// Выводим ссылки "назад" и "на первую страницу"
if ($page>=1) {
    echo $href. '1"><<</a> &nbsp; ';
    echo $href. max($page-6,1). '">< </a> &nbsp; ';
}
// __________________ конец блока 1 __________________
$limit=5;$start = max(1,$page-$limit);$end=$page+$limit;
for ($j = $start; $j<$pages; $j++) {
    // Выводим ссылки только в том случае, если их номер больше или равен
    // начальному значению, и меньше или равен конечному значению
    if ($j>=$start && $j<=$end) {
        // Ссылка на текущую страницу выделяется жирным
        if ($j==($page)) echo $href.$j . '"><strong style="color: #ff0036">' . $j . 
        '</strong></a> &nbsp; ';
        // Ссылки на остальные страницы
        else echo $href . $j . '">' . $j . '</a> &nbsp; ';
    }
} // _________________ начало блока 2 _________________
if ($page<$pages) {
    // Чтобы попасть на следующую страницу нужно увеличить $pages на 2
    echo $href . min($page+6,$pages) . '"> ></a> &nbsp; ';
    echo $href . ($pages) . '">>></a> &nbsp; ';
}
}
};
echo '</td></tr></table>';
?>
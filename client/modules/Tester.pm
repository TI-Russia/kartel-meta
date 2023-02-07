package Tester;

use strict;
use warnings;
use 5.012;
use Exporter 'import';
use UTF8;
#no UTF8;

our @EXPORT = qw(test);

sub test($)
{ 
 return (index($_[0],Encode::decode("cp1251","Ошибка доступа. Файл с uri PRIZ"))!=-1||index($_[0],Encode::decode("cp1251","Загрузка файла PRIZ"))!=-1)&&(index($_[0],Encode::decode("cp1251","не опубликован"))!=-1||index($_[0],Encode::decode("cp1251","не завершена")))!=-1;
}

1;
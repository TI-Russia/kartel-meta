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
 return (index($_[0],Encode::decode("cp1251","������ �������. ���� � uri PRIZ"))!=-1||index($_[0],Encode::decode("cp1251","�������� ����� PRIZ"))!=-1)&&(index($_[0],Encode::decode("cp1251","�� �����������"))!=-1||index($_[0],Encode::decode("cp1251","�� ���������")))!=-1;
}

1;
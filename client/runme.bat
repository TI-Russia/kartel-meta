@echo off
if not -%1==- goto second
if not -%~n0==-update goto start
copy %0 runme.bat
runme.bat
:second
if not exist perl goto begin
set second=%1
if -%2==--f goto seccopy
if exist client%second% goto secrun
:seccopy
if not exist client%second% mkdir client%second%
copy -y client\*.pl client%second%\
if not exist client%second%\modules mkdir client%second%\modules
copy -y client\*.pl client%second%\
copy -y client\modules\* client%second%\modules\
:secrun
cd client%second%
goto begin
:start
if exist update.bat del update.bat
set perl=perl
if not exist perl goto begin
set perl="%~dp0perl\bin\perl.exe"
setlocal
set PATH=%~dp0perl\site\bin;%~dp0perl\bin;%~dp0c\bin;%PATH%
set TERM=
set PERL_JSON_BACKEND=
set PERL_YAML_BACKEND=
set PERL5LIB=
set PERL5OPT=
set PERL_MM_OPT=
set PERL_MB_OPT=
cd client
:begin
%perl% client.pl
if ERRORLEVEL 7 goto :update
goto :end
:update
if not -%second%==- goto begin
cd ..
if exist update.bat update.bat
cd client
goto begin
:end

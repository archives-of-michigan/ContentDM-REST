@echo off
set php4="C:\Program Files\PHP4\php.exe"
set php5="C:\Program Files\PHP5\php.exe"
cd ..
%php4% -v
%php4% tests/test.php
%php5% -v
%php5% tests/test.php
cd tests
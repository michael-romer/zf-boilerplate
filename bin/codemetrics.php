<?php
echo PHP_EOL . "#####################" . PHP_EOL;
echo "#  PHP LOC Results #" . PHP_EOL;
echo "#####################" . PHP_EOL;
echo shell_exec("phploc ../application ../library/App") . PHP_EOL;

echo PHP_EOL . "####################" . PHP_EOL;
echo "#  PHP MD Results  #" . PHP_EOL;
echo "####################" . PHP_EOL;
echo shell_exec("phpmd ../application,../library/App text design,codesize,unusedcode,naming");
<?php
echo PHP_EOL . "#####################" . PHP_EOL;
echo "#  PHP LOC Results  #" . PHP_EOL;
echo "#####################" . PHP_EOL;
echo shell_exec("phploc ../application ../library/App") . PHP_EOL;

echo PHP_EOL . "####################" . PHP_EOL;
echo "#  PHP MD Results  #" . PHP_EOL;
echo "####################" . PHP_EOL;
echo shell_exec("phpmd ../application,../library/App text design,codesize,unusedcode,naming");

echo PHP_EOL . "#####################" . PHP_EOL;
echo "#  PHP CPD Results  #" . PHP_EOL;
echo "#####################" . PHP_EOL;
echo shell_exec("phpcpd ../application ../library/App");

echo PHP_EOL . "########################" . PHP_EOL;
echo "#  PHP Depend Results  #" . PHP_EOL;
echo "########################" . PHP_EOL;
echo shell_exec("pdepend --summary-xml=codemetrics/summary.xml --jdepend-chart=codemetrics/jdepend.svg --overview-pyramid=codemetrics/pyramid.svg ../application,../library/App");



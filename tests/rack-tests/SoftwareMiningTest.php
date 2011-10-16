<?php
class SoftwareMiningTest extends phpRack_Test
{
    public function testToolsExist()
    {
        $this->assert->php->pear
            ->package('phpmd/PHP_PMD')
            ->package('pdepend/PHP_Depend')
            ->package('PHP_CodeSniffer-1.3.0')
            ->package('phpunit/phploc')
            ->package('phpunit/phpcpd')
            ->package('docblox/DocBlox')
            ->package('doc.php.net/phd');
    }
}
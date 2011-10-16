<?php
class ElasticSearchTest extends phpRack_Test
{
    public function testPhpExtensionsExist()
    {
        $this->assert->php->extensions
            ->isLoaded('curl');
    }
}
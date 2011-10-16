<?php
class CachingTest extends phpRack_Test
{
    public function testPhpExtensionsExist()
    {
        $this->assert->php->extensions
            ->isLoaded('memcache');
    }
}
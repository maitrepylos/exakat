<?php

namespace Test;

include_once(dirname(dirname(dirname(dirname(__DIR__)))).'/library/Autoload.php');
spl_autoload_register('Autoload::autoload_test');
spl_autoload_register('Autoload::autoload_phpunit');
spl_autoload_register('Autoload::autoload_library');

class Php_PearUsage extends Analyzer {
    /* 2 methods */

    public function testPhp_PearUsage01()  { $this->generic_test('Php/PearUsage.01'); }
    public function testPhp_PearUsage02()  { $this->generic_test('Php/PearUsage.02'); }
}
?>
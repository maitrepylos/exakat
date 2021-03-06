<?php

namespace Test;

include_once(dirname(dirname(dirname(dirname(__DIR__)))).'/library/Autoload.php');
spl_autoload_register('Autoload::autoload_test');
spl_autoload_register('Autoload::autoload_phpunit');
spl_autoload_register('Autoload::autoload_library');

class Interfaces_InterfaceUsage extends Analyzer {
    /* 2 methods */

    public function testInterfaces_InterfaceUsage01()  { $this->generic_test('Interfaces_InterfaceUsage.01'); }
    public function testInterfaces_InterfaceUsage02()  { $this->generic_test('Interfaces_InterfaceUsage.02'); }
}
?>
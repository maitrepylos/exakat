<?php

namespace Test;

include_once(dirname(dirname(dirname(dirname(__DIR__)))).'/library/Autoload.php');
spl_autoload_register('Autoload::autoload_test');
spl_autoload_register('Autoload::autoload_phpunit');
spl_autoload_register('Autoload::autoload_library');

class Project_IsLibrary extends Analyzer {
    /* 5 methods */

    public function testProject_IsLibrary01()  { $this->generic_test('Project/IsLibrary.01'); }
    public function testProject_IsLibrary02()  { $this->generic_test('Project/IsLibrary.02'); }
    public function testProject_IsLibrary03()  { $this->generic_test('Project/IsLibrary.03'); }
    public function testProject_IsLibrary04()  { $this->generic_test('Project/IsLibrary.04'); }
    public function testProject_IsLibrary05()  { $this->generic_test('Project/IsLibrary.05'); }
}
?>
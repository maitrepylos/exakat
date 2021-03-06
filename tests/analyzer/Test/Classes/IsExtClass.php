<?php

namespace Test;

include_once(dirname(dirname(dirname(dirname(__DIR__)))).'/library/Autoload.php');
spl_autoload_register('Autoload::autoload_test');
spl_autoload_register('Autoload::autoload_phpunit');
spl_autoload_register('Autoload::autoload_library');

class Classes_IsExtClass extends Analyzer {
    /* 8 methods */

    public function testClasses_IsExtClass01()  { $this->generic_test('Classes_IsExtClass.01'); }
    public function testClasses_IsExtClass02()  { $this->generic_test('Classes_IsExtClass.02'); }
    public function testClasses_IsExtClass03()  { $this->generic_test('Classes_IsExtClass.03'); }
    public function testClasses_IsExtClass04()  { $this->generic_test('Classes_IsExtClass.04'); }
    public function testClasses_IsExtClass05()  { $this->generic_test('Classes_IsExtClass.05'); }
    public function testClasses_IsExtClass06()  { $this->generic_test('Classes/IsExtClass.06'); }
    public function testClasses_IsExtClass07()  { $this->generic_test('Classes/IsExtClass.07'); }
    public function testClasses_IsExtClass08()  { $this->generic_test('Classes/IsExtClass.08'); }
}
?>
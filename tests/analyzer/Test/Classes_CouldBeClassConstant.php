<?php

namespace Test;

include_once(dirname(dirname(dirname(__DIR__))).'/library/Autoload.php');
spl_autoload_register('Autoload::autoload_test');
spl_autoload_register('Autoload::autoload_phpunit');
spl_autoload_register('Autoload::autoload_library');

class Classes_CouldBeClassConstant extends Analyzer {
    /* 3 methods */

    public function testClasses_CouldBeClassConstant01()  { $this->generic_test('Classes_CouldBeClassConstant.01'); }
    public function testClasses_CouldBeClassConstant02()  { $this->generic_test('Classes_CouldBeClassConstant.02'); }
    public function testClasses_CouldBeClassConstant03()  { $this->generic_test('Classes_CouldBeClassConstant.03'); }
}
?>
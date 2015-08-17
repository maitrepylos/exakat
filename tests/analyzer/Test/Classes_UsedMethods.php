<?php

namespace Test;

include_once(dirname(dirname(dirname(__DIR__))).'/library/Autoload.php');
spl_autoload_register('Autoload::autoload_test');
spl_autoload_register('Autoload::autoload_phpunit');
spl_autoload_register('Autoload::autoload_library');

class Classes_UsedMethods extends Analyzer {
    /* 4 methods */

    public function testClasses_UsedMethods01()  { $this->generic_test('Classes_UsedMethods.01'); }
    public function testClasses_UsedMethods02()  { $this->generic_test('Classes_UsedMethods.02'); }
    public function testClasses_UsedMethods03()  { $this->generic_test('Classes_UsedMethods.03'); }
    public function testClasses_UsedMethods04()  { $this->generic_test('Classes_UsedMethods.04'); }
}
?>
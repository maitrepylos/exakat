<?php

namespace Test;

include_once(dirname(dirname(dirname(dirname(__DIR__)))).'/library/Autoload.php');
spl_autoload_register('Autoload::autoload_test');
spl_autoload_register('Autoload::autoload_phpunit');
spl_autoload_register('Autoload::autoload_library');

class Classes_ThisIsForClasses extends Analyzer {
    /* 11 methods */

    public function testClasses_ThisIsForClasses01()  { $this->generic_test('Classes_ThisIsForClasses.01'); }
    public function testClasses_ThisIsForClasses02()  { $this->generic_test('Classes_ThisIsForClasses.02'); }
    public function testClasses_ThisIsForClasses03()  { $this->generic_test('Classes_ThisIsForClasses.03'); }
    public function testClasses_ThisIsForClasses04()  { $this->generic_test('Classes_ThisIsForClasses.04'); }
    public function testClasses_ThisIsForClasses05()  { $this->generic_test('Classes_ThisIsForClasses.05'); }
    public function testClasses_ThisIsForClasses06()  { $this->generic_test('Classes/ThisIsForClasses.06'); }
    public function testClasses_ThisIsForClasses07()  { $this->generic_test('Classes/ThisIsForClasses.07'); }
    public function testClasses_ThisIsForClasses08()  { $this->generic_test('Classes/ThisIsForClasses.08'); }
    public function testClasses_ThisIsForClasses09()  { $this->generic_test('Classes/ThisIsForClasses.09'); }
    public function testClasses_ThisIsForClasses10()  { $this->generic_test('Classes/ThisIsForClasses.10'); }
    public function testClasses_ThisIsForClasses11()  { $this->generic_test('Classes/ThisIsForClasses.11'); }
}
?>
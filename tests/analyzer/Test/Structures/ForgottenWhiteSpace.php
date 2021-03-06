<?php

namespace Test;

include_once(dirname(dirname(dirname(dirname(__DIR__)))).'/library/Autoload.php');
spl_autoload_register('Autoload::autoload_test');
spl_autoload_register('Autoload::autoload_phpunit');
spl_autoload_register('Autoload::autoload_library');

class Structures_ForgottenWhiteSpace extends Analyzer {
    /* 11 methods */

    public function testStructures_ForgottenWhiteSpace01()  { $this->generic_test('Structures_ForgottenWhiteSpace.01'); }
    public function testStructures_ForgottenWhiteSpace02()  { $this->generic_test('Structures_ForgottenWhiteSpace.02'); }
    public function testStructures_ForgottenWhiteSpace03()  { $this->generic_test('Structures_ForgottenWhiteSpace.03'); }
    public function testStructures_ForgottenWhiteSpace04()  { $this->generic_test('Structures_ForgottenWhiteSpace.04'); }
    public function testStructures_ForgottenWhiteSpace05()  { $this->generic_test('Structures_ForgottenWhiteSpace.05'); }
    public function testStructures_ForgottenWhiteSpace06()  { $this->generic_test('Structures/ForgottenWhiteSpace.06'); }
    public function testStructures_ForgottenWhiteSpace07()  { $this->generic_test('Structures/ForgottenWhiteSpace.07'); }
    public function testStructures_ForgottenWhiteSpace08()  { $this->generic_test('Structures/ForgottenWhiteSpace.08'); }
    public function testStructures_ForgottenWhiteSpace09()  { $this->generic_test('Structures/ForgottenWhiteSpace.09'); }
    public function testStructures_ForgottenWhiteSpace10()  { $this->generic_test('Structures/ForgottenWhiteSpace.10'); }
    public function testStructures_ForgottenWhiteSpace11()  { $this->generic_test('Structures/ForgottenWhiteSpace.11'); }
}
?>
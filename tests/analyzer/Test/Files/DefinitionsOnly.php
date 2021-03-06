<?php

namespace Test;

include_once(dirname(dirname(dirname(dirname(__DIR__)))).'/library/Autoload.php');
spl_autoload_register('Autoload::autoload_test');
spl_autoload_register('Autoload::autoload_phpunit');
spl_autoload_register('Autoload::autoload_library');

class Files_DefinitionsOnly extends Analyzer {
    /* 9 methods */

    public function testFiles_DefinitionsOnly01()  { $this->generic_test('Files_DefinitionsOnly.01'); }
    public function testFiles_DefinitionsOnly02()  { $this->generic_test('Files_DefinitionsOnly.02'); }
    public function testFiles_DefinitionsOnly03()  { $this->generic_test('Files_DefinitionsOnly.03'); }
    public function testFiles_DefinitionsOnly04()  { $this->generic_test('Files_DefinitionsOnly.04'); }
    public function testFiles_DefinitionsOnly05()  { $this->generic_test('Files_DefinitionsOnly.05'); }
    public function testFiles_DefinitionsOnly06()  { $this->generic_test('Files_DefinitionsOnly.06'); }
    public function testFiles_DefinitionsOnly07()  { $this->generic_test('Files_DefinitionsOnly.07'); }
    public function testFiles_DefinitionsOnly08()  { $this->generic_test('Files_DefinitionsOnly.08'); }
    public function testFiles_DefinitionsOnly09()  { $this->generic_test('Files/DefinitionsOnly.09'); }
}
?>
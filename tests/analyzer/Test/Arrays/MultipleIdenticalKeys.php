<?php

namespace Test;

include_once(dirname(dirname(dirname(dirname(__DIR__)))).'/library/Autoload.php');
spl_autoload_register('Autoload::autoload_test');
spl_autoload_register('Autoload::autoload_phpunit');
spl_autoload_register('Autoload::autoload_library');

class Arrays_MultipleIdenticalKeys extends Analyzer {
    /* 4 methods */

    public function testArrays_MultipleIdenticalKeys01()  { $this->generic_test('Arrays_MultipleIdenticalKeys.01'); }
    public function testArrays_MultipleIdenticalKeys02()  { $this->generic_test('Arrays_MultipleIdenticalKeys.02'); }
    public function testArrays_MultipleIdenticalKeys03()  { $this->generic_test('Arrays/MultipleIdenticalKeys.03'); }
    public function testArrays_MultipleIdenticalKeys04()  { $this->generic_test('Arrays/MultipleIdenticalKeys.04'); }
}
?>
<?php

namespace Test;

include_once(dirname(dirname(dirname(dirname(__DIR__)))).'/library/Autoload.php');
spl_autoload_register('Autoload::autoload_test');
spl_autoload_register('Autoload::autoload_phpunit');
spl_autoload_register('Autoload::autoload_library');

class Arrays_AmbiguousKeys extends Analyzer {
    /* 4 methods */

    public function testArrays_AmbiguousKeys01()  { $this->generic_test('Arrays_AmbiguousKeys.01'); }
    public function testArrays_AmbiguousKeys02()  { $this->generic_test('Arrays/AmbiguousKeys.02'); }
    public function testArrays_AmbiguousKeys03()  { $this->generic_test('Arrays/AmbiguousKeys.03'); }
    public function testArrays_AmbiguousKeys04()  { $this->generic_test('Arrays/AmbiguousKeys.04'); }
}
?>
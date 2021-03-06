<?php

namespace Test;

include_once(dirname(dirname(dirname(dirname(__DIR__)))).'/library/Autoload.php');
spl_autoload_register('Autoload::autoload_test');
spl_autoload_register('Autoload::autoload_phpunit');
spl_autoload_register('Autoload::autoload_library');

class Exceptions_AlreadyCaught extends Analyzer {
    /* 4 methods */

    public function testExceptions_AlreadyCaught01()  { $this->generic_test('Exceptions/AlreadyCaught.01'); }
    public function testExceptions_AlreadyCaught02()  { $this->generic_test('Exceptions/AlreadyCaught.02'); }
    public function testExceptions_AlreadyCaught03()  { $this->generic_test('Exceptions/AlreadyCaught.03'); }
    public function testExceptions_AlreadyCaught04()  { $this->generic_test('Exceptions/AlreadyCaught.04'); }
}
?>
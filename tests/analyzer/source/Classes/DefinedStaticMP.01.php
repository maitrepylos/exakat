<?php

class c {
     private $x = 1;

     function definedInParentParent() { print __METHOD__."\n";}
     private $pdefinedInParentParent;
}

class a extends c {
     private $x = 1;
     
     function definedInParent() { print __METHOD__."\n";}
     private $pdefinedInParent;
}

class b extends a {
     public $x = 2;
     
     function y() {
        static::definedInParent();
        static::definedInParentParent();
        static::undefined();
        static::definedinStatic();

        static::$pdefinedInParent;
        static::$pdefinedInParentParent;
        static::$pundefined;
        static::$pdefinedinStatic;
     }
     
     function definedinStatic() { print __METHOD__."\n";}
     private $pdefinedinStatic;
}


$b = new b();
//print a::$x
print $b->y();
?>
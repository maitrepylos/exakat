<?php

class A implements \arrayaccess {
    function x () {
        $this[] = $this[1];
    }
}

class B extends A {
    function x () {
        $this[] = $this[2];
    }
}

class C extends B {
    function x () {
        $this[] = $this[3];
    }
}

class D extends C {
    function x () {
        $this[] = $this[4];
    }
}

?>
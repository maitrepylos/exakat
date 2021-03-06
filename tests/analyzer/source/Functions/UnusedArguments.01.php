<?php 

function a1($readOnlyA1, $writenOnlyA1, $readAndWrittenA1, $unusedA1) { 
    $writenOnlyA1 = $readOnlyA1 + 1;
    $readAndWrittenA1 = $readAndWrittenA1 * $readOnlyA1;
    $localA1 = 1;
};

function a2(&$readOnlyA2, &$writenOnlyA2, &$readAndWrittenA2, &$unusedA2) { 
    $writenOnlyA2 = $readOnlyA2 + 1;
    $readAndWrittenA2 = $readAndWrittenA2 * $readOnlyA2;
    $localA2 = 1;
};

$a = function () use (&$readOnlyClosureR, &$writenOnlyClosureR, &$readAndWrittenClosureR, &$unusedClosureR) { 
    $writenOnlyClosureR = $readOnlyClosureR + 1;
    $readAndWrittenClosureR = $readAndWrittenClosureR * $readOnlyClosureR;
    $localClosureR = 1;
};

$a = function () use ($readOnlyClosure, $writenOnlyClosure, $readAndWrittenClosure, $unusedUseClosure) { 
    $writenOnlyClosure = $readOnlyClosure + 1;
    $readAndWrittenClosure = $readAndWrittenClosure * $readOnlyClosure;
    $localClosure = 1;
};

interface i {
    function interfaceMethod($interfaceArgument) ;
}

abstract class c {
    abstract function abstractClassMethod($abstractClassArgument) ;
             function         ClassMethod($ClassArgument) {}
}

trait t {
    abstract function abstractClassMethod($abstractTraitArgument) ;
             function         ClassMethod($traitArgument) {}
}

?>

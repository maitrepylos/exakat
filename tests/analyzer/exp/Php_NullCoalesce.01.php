<?php

$expected     = array('$a2 ?? $a22 ?? $a222 ?? \'2\'',
                      '0 || 2 ?? 3',
                      '$a ?? \'2\'',
                      '$a22 ?? $a222 ?? \'2\'',
                      '$a222 ?? \'2\'',
);

$expected_not = array();

?>
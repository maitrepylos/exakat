<?php

$expected     = array('function unusedCMethod( ) { /**/ } ');

$expected_not = array('function usedCMethod( ) { /**/ } ',
                      'function usedTMethod( ) { /**/ } ',
                      'function usedIMethod( ) { /**/ } ',
                      'function unusedIMethod( ) { /**/ } ',
                      );

?>
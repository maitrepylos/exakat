<?php
/*
 * Copyright 2012-2016 Damien Seguy – Exakat Ltd <contact(at)exakat.io>
 * This file is part of Exakat.
 *
 * Exakat is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Exakat is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with Exakat.  If not, see <http://www.gnu.org/licenses/>.
 *
 * The latest code can be found at <http://exakat.io/>.
 *
*/


namespace Exakat\Analyzer\Php;

use Exakat\Analyzer\Analyzer;

class Php7RelaxedKeyword extends Analyzer {
    protected $phpVersion = '7.0+';
    
    public function analyze() {
        $keywords = $this->loadIni('php7_relaxed_keyword.ini', 'keywords');
        
        //////////////////////////////////////////////////////////////////////
        // Definitions in a class                                           //
        //////////////////////////////////////////////////////////////////////
        // Method names
        $this->atomIs('Class')
             ->outIs('BLOCK')
             ->outIs('ELEMENT')
             ->atomIs('Function')
             ->outIs('NAME')
             ->codeIs($keywords)
             ->inIs('NAME');
        $this->prepareQuery();

        // Constant names
        $this->atomIs('Class')
             ->outIs('BLOCK')
             ->outIs('ELEMENT')
             ->atomIs('Const')
             ->outIs('CONST')
             ->outIs('NAME')
             ->codeIs($keywords)
             ->inIs('NAME');
        $this->prepareQuery();

        // Constant names
        $this->atomIs('Class')
             ->outIs('BLOCK')
             ->outIs('ELEMENT')
             ->atomIs('Const')
             ->outIs('CONST')
             ->outIs('NAME')
             ->codeIs($keywords)
             ->inIs('NAME');
        $this->prepareQuery();

        //////////////////////////////////////////////////////////////////////
        // Static usage                                                     //
        //////////////////////////////////////////////////////////////////////
        // Static Constant
        $this->atomIs('Staticconstant')
             ->outIs('CONSTANT')
             ->codeIs($keywords)
             ->back('first');
        $this->prepareQuery();

        // Static Methodcall
        $this->atomIs('Staticmethodcall')
             ->outIs('METHOD')
             ->codeIs($keywords)
             ->back('first');
        $this->prepareQuery();

        // Static Property
        $keywordsVariables = array_map(function ($x) { return '$'.$x; }, $keywords);
        $this->atomIs('Staticproperty')
             ->outIs('PROPERTY')
             ->codeIs($keywordsVariables)
             ->back('first');
        $this->prepareQuery();

        //////////////////////////////////////////////////////////////////////
        // Static usage                                                     //
        //////////////////////////////////////////////////////////////////////
        // Methodcall 
        $this->atomIs('Methodcall')
             ->outIs('METHOD')
             ->codeIs($keywords)
             ->back('first');
        $this->prepareQuery();

        // Property
        $this->atomIs('Property')
             ->outIs('PROPERTY')
             ->codeIs($keywords)
             ->back('first');
        $this->prepareQuery();
    }
}

?>

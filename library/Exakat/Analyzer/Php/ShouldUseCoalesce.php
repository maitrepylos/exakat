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

class ShouldUseCoalesce extends Analyzer {
    protected $phpVersion = '7.0+';
    
    public function analyze() {

        //isset($a) ? $a : 'b';
        $this->atomIs('Ternary')
             ->outIs('CONDITION')
             ->functioncallIs('\\isset')
             ->outIs('ARGUMENTS')
             ->outWithRank('ARGUMENT', 0)
             ->savePropertyAs('fullcode', 'variable')
             ->inIs('ARGUMENT')
             ->inIs('ARGUMENTS')
             ->inIs('CONDITION')
             ->outIs('THEN')
             ->samePropertyAs('fullcode', 'variable')
             ->back('first');
        $this->prepareQuery();

        //!isset($a) ? 'b' : $a;
        $this->atomIs('Ternary')
             ->outIs('CONDITION')
             ->atomIs('Not')
             ->outIs('NOT')
             ->functioncallIs('\\isset')
             ->outIs('ARGUMENTS')
             ->outWithRank('ARGUMENT', 0)
             ->savePropertyAs('fullcode', 'variable')
             ->inIs('ARGUMENT')
             ->inIs('ARGUMENTS')
             ->inIs('NOT')
             ->inIs('CONDITION')
             ->outIs('ELSE')
             ->samePropertyAs('fullcode', 'variable')
             ->back('first');
        $this->prepareQuery();

        //$a === null ? $a : 'b';
        $this->atomIs('Ternary')
             ->outIs('CONDITION')
             ->atomIs('Comparison')
             ->codeIs('===')
             ->outIs(array('LEFT', 'RIGHT'))
             ->atomIs('Null')
             ->inIs(array('LEFT', 'RIGHT'))
             ->outIs(array('LEFT', 'RIGHT')) // Out to the other one, in fact
             ->savePropertyAs('fullcode', 'variable')
             ->inIs(array('LEFT', 'RIGHT'))
             ->inIs('CONDITION')
             ->outIs('THEN')
             ->samePropertyAs('fullcode', 'variable')
             ->back('first');
        $this->prepareQuery();

        //$a !== null ?  'b' : $a;
        $this->atomIs('Ternary')
             ->outIs('CONDITION')
             ->atomIs('Comparison')
             ->codeIs('!==')
             ->outIs(array('LEFT', 'RIGHT'))
             ->atomIs('Null')
             ->inIs(array('LEFT', 'RIGHT'))
             ->outIs(array('LEFT', 'RIGHT'))
             ->tokenIsNot('T_STRING')
             ->savePropertyAs('fullcode', 'variable')
             ->inIs(array('LEFT', 'RIGHT'))
             ->inIs('CONDITION')
             ->outIs('ELSE')
             ->samePropertyAs('fullcode', 'variable')
             ->back('first');
        $this->prepareQuery();

        //if (($model = Model::get($id)) === NULL) { $model = $default_model; }
        $this->atomIs('Ifthen')
             ->outIs('CONDITION')
             ->atomIs('Comparison')
             ->codeIs('===')
             ->outIs(array('LEFT', 'RIGHT'))
             ->atomIs('Null')
             ->inIs(array('LEFT', 'RIGHT'))
             ->outIs(array('LEFT', 'RIGHT'))
             ->outIsIE('CODE')
             ->atomIs('Assignation')
             ->codeIs('=')
             ->outIs('LEFT')
             ->savePropertyAs('fullcode', 'variable')
             ->inIs('LEFT')
             ->inIsIE('CODE')
             ->inIs(array('LEFT', 'RIGHT'))
             ->inIs('CONDITION')
             ->outIs('THEN')
             ->outWithRank('ELEMENT', 0)
             ->outIs('LEFT')
             ->samePropertyAs('fullcode', 'variable')
             ->back('first');
        $this->prepareQuery();
    }
}

?>

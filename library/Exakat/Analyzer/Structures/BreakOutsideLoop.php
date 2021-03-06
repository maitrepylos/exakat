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


namespace Exakat\Analyzer\Structures;

use Exakat\Analyzer\Analyzer;

class BreakOutsideLoop extends Analyzer {
    protected $phpVersion = '7.0-';
    
    public function analyze() {
        $loops = '"Dowhile", "For", "Foreach", "While", "Switch"';
        // break (null)
        $this->atomIs('Break')
             ->outIs('LEVEL')
             ->atomIs('Void')
             ->filter('it.in.loop(1){true}{it.object.atom in ['.$loops.']}.any() == false')
             ->back('first');
        $this->prepareQuery();

        // break 1
        $this->atomIs('Break')
             ->outIs('LEVEL')
             ->atomIs('Integer')
             ->savePropertyAs('code', 'counter')
             ->filter('it.in.loop(1){true}{ it.object.atom in ['.$loops.'] }.count() < counter.toInteger()') // really count temps
             ->back('first');
        $this->prepareQuery();

        // continue (null)
        $this->atomIs('Continue')
             ->outIs('LEVEL')
             ->atomIs('Void')
             ->filter('it.in.loop(1){true}{it.object.atom in ['.$loops.']}.any() == false')
             ->back('first');
        $this->prepareQuery();

        // continue 1
        $this->atomIs('Continue')
             ->outIs('LEVEL')
             ->atomIs('Integer')
             ->savePropertyAs('code', 'counter')
             ->filter('it.in.loop(1){true}{ it.object.atom in ['.$loops.'] }.count() < counter.toInteger()') // really count temps
             ->back('first');
        $this->prepareQuery();
    }
}

?>

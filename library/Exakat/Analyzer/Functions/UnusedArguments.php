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


namespace Exakat\Analyzer\Functions;

use Exakat\Analyzer\Analyzer;

class UnusedArguments extends Analyzer {
    public function dependsOn() {
        return array('Variables/Arguments',
                     'Variables/IsRead',
                     'Variables/IsModified',
                     );
    }
    
    public function analyze() {
        $isNotRead = 'where( repeat( out() ).emit( hasLabel("Variable").filter{ it.get().value("code") == varname; }).times('.self::MAX_LOOPING.')
                                          .where( __.in("ANALYZED").has("analyzer", "Variables/IsRead").count().is(eq(1)) )
                                          .count().is(eq(0)) )';
    
        $isNotUsed = 'where( repeat( out() ).emit( hasLabel("Variable").filter{ it.get().value("code") == varname; } ).times('.self::MAX_LOOPING.').count().is(eq(0)) )';
        //                                          .where( __.in("ANALYZED").has("analyzer", within("Analyzer\\\\Variables\\\\IsRead", "Analyzer\\\\Variables\\\\IsModified")).count().is(eq(1)) )

        // Arguments, not reference
        $this->analyzerIs('Variables/Arguments')
             ->savePropertyAs('code', 'varname')
             ->isNot('reference', true)
             ->inIs('ARGUMENT')
             ->inIs('ARGUMENTS')
             ->atomIs('Function')

             ->hasNoOut('ABSTRACT')
             ->hasNoInterface()

             ->outIs('BLOCK')
             // this argument must be read at least once
             ->raw($isNotRead)
             ->back('first');
        $this->prepareQuery();

        // Arguments, reference
        $this->analyzerIs('Variables/Arguments')
             ->savePropertyAs('code', 'varname')
             ->is('reference', true)
             ->inIs('ARGUMENT')
             ->inIs('ARGUMENTS')
             ->atomIs('Function')
             ->hasNoInterface()
             ->hasNoOut('ABSTRACT')

             ->outIs('BLOCK')
             // this argument must be read or written at least once (in fact, used)
             ->raw($isNotUsed)
             ->back('first');
        $this->prepareQuery();

        // Arguments in a USE, not a reference
        $this->atomIs('Function')
             ->hasChildren('Void', 'NAME')
             ->outIs('USE')
             ->outIs('ARGUMENT')
             ->is('reference', false)
             ->savePropertyAs('code', 'varname')
             ->_as('results')
             ->back('first')

             ->outIs('BLOCK')
             // this argument must be read or written at least once
             ->raw($isNotRead)

             ->back('results');
        $this->prepareQuery();

        // Arguments in a USE, reference
        // Arguments in a USE, not a reference
        $this->atomIs('Function')
             ->hasChildren('Void', 'NAME')
             ->outIs('USE')
             ->outIs('ARGUMENT')
             ->is('reference', true)
             ->savePropertyAs('code', 'varname')
             ->_as('results')
             ->back('first')

             ->outIs('BLOCK')
             // this argument must be read or written at least once
             ->raw($isNotUsed)

             ->back('results');
        $this->prepareQuery();
    }
}

?>

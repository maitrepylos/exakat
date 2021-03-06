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
namespace Exakat\Analyzer\Security;

use Exakat\Analyzer\Analyzer;

class CurlOptions extends Analyzer {
    public function dependsOn() {
        return array('Structures/Falsy',
                     'Constants/ConstantUsage');
    }
    public function analyze() {
        $options = array('\\curlopt_ssl_verifypeer', '\\curlopt_ssl_verifyhost');
        
        // Via curl_setopt
        $this->atomFunctionIs('\curl_setopt')
             ->outIs('ARGUMENTS')
             ->outWithRank('ARGUMENT', 1)
             ->atomIs(array('Identifier', 'Nsname'))
             ->fullnspathIs($options)
             ->inIs('ARGUMENT')
             ->outWithRank('ARGUMENT', 2)
             ->analyzerIs('Structures/Falsy')
             ->back('first');
        $this->prepareQuery();

        // Via curl_setopt_array (actually, any array with key => value that fit the options
        $this->atomFunctionIs('\array')
             ->outIs('ARGUMENTS')
             ->outIs('ARGUMENT')
             ->atomIs('Keyvalue')
             ->outIs('KEY')
             ->atomIs(array('Identifier', 'Nsname'))
             ->fullnspathIs($options)
             ->inIs('KEY')
             ->outIs('VALUE')
             ->analyzerIs('Structures/Falsy')
             ->back('first');
        $this->prepareQuery();
    }
}

?>

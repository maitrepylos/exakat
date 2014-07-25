<?php

namespace Tokenizer;

class Methodcall extends TokenAuto {
    static public $operators = array('T_OBJECT_OPERATOR');
    static public $atom = 'Methodcall';

    public function _check() {
        $operands = array('Variable', 'Property', 'Array', 'Functioncall', 'Methodcall', 
                          'Staticmethodcall', 'Staticproperty', 'Parenthesis' );

        // $this->x($args);
        $this->conditions = array( -2 => array('filterOut' => array('T_DOUBLE_COLON', 'T_OBJECT_OPERATOR')),
                                   -1 => array('atom'      => $operands), 
                                    0 => array('token'     => Methodcall::$operators,
                                               'atom'      => 'none'),
                                    1 => array('atom'      => array('Functioncall', 'Methodcall'))
                                 );
        
        $this->actions = array('makeMethodCall' => true,
                               'atom'           => 'Methodcall',
                               'cleanIndex'     => true,
                               'makeSequence'   => 'p');
        $this->checkAuto(); 

        // $this->{$x}($args);
        $this->conditions = array( -2 => array('filterOut' => array('T_DOUBLE_COLON')),
                                   -1 => array('atom'      => $operands), 
                                    0 => array('token'     => Methodcall::$operators),
                                    1 => array('token'     => 'T_OPEN_CURLY'),
                                    2 => array('atom'      => 'yes'),
                                    3 => array('token'     => 'T_CLOSE_CURLY'),
                                    4 => array('token'     => 'T_OPEN_PARENTHESIS'),
                                    5 => array('atom'      => array('Arguments', 'Void')),
                                    6 => array('token'     => 'T_CLOSE_PARENTHESIS')
                                 );
        
        $this->actions = array('transform'    => array( -1 => 'OBJECT',
                                                         1 => 'DROP',
                                                         2 => 'METHOD',
                                                         3 => 'DROP',
                                                         4 => 'DROP',
                                                         5 => 'ARGUMENTS',
                                                         6 => 'DROP'),
                               'atom'         => 'Methodcall',
                               'cleanIndex'   => true,
                               'makeSequence' => 'it');
        $this->checkAuto(); 
        
        return $this->checkRemaining();
    }

    public function fullcode() {
        return <<<GREMLIN

fullcode.out("NAME").each{ it.setProperty('fullcode', it.getProperty('code')); };

fullcode.filter{ it.out("METHOD").count() == 1}.each{ fullcode.fullcode = fullcode.out("OBJECT").next().getProperty('fullcode') + "->" + fullcode.out("METHOD").next().getProperty('fullcode');  }

GREMLIN;
    }
}

?>
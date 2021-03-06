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


namespace Exakat\Loader;

use Exakat\Config;
use Exakat\Datastore;
use Exakat\Graph\Cypher;
use Exakat\Tasks\Load;
use Exception;

class CypherG3 {
    const CSV_SEPARATOR = ',';
    
    private $node = null;
    private static $nodes = array();
    private static $file_saved = 0;
    private $unlink = array();

    private static $links = array();
    private static $lastLink = array();

    private static $cols = array();
    private static $count = -1; // id must start at 0 in batch-import
    private $id = 0;
    
    private static $fp_rels       = null;
    private static $fp_nodes      = null;
    private static $fp_nodes_attr = array();
    private static $indexedId     = array();
    private static $tokenCounts   = array();

    private $config = null;
    
    private $isLink = false;
    
    private $cypher = null;
    
    public function __construct() {
        $this->config = Config::factory();
        
        // Force autoload
        $this->cypher = new Cypher($this->config );

        if (file_exists($this->config->projects_root.'/projects/.exakat/nodes.cypher.csv') && static::$file_saved == 0) {
            $this->cleanCsv();
        }
        
        $node = array('inited' => true);
        $this->node = &$node;
    }

    public function finalize() {
        self::saveTokenCounts();
        
        // Load Nodes
        $files = glob($this->config->projects_root.'/projects/.exakat/nodes.g3.*.csv');
        foreach($files as $file) {
            preg_match('/nodes\.g3\.(.*)\.csv$/', $file, $r);
            $atom = $r[1];

            $queryTemplate = 'CREATE INDEX ON :'.$atom.'(eid)';
            $this->cypher->query($queryTemplate);

            $b = microtime(true);
            
            $extra = array();
            foreach(Load::$PROP_OPTIONS as $title => $atoms) {
                if (in_array($atom, $atoms)) {
                    if (in_array($title, array('delimiter', 'noDelimiter', 'fullnspath', 'alias', 'origin', 'encoding', 'strval'))) {
                    // Raw string
                        $extra[] = "$title: csvLine.$title";
                    } elseif (in_array($title, array('alternative', 'heredoc', 'reference', 'variadic', 'absolute', 'enclosing', 'bracket', 'close_tag'))) {
                    // Boolean
                        $extra[] = "$title: (csvLine.$title <> \"\")";
                    } elseif (in_array($title, array('count', 'intval', 'args_max', 'args_min'))) {
                    // Integer
                        $extra[] = "$title: toInt(csvLine.$title)";
                    } else {
                        die('Unexpected option in '.__CLASS__.' : "'.$title.'"');
                    }
                }
            }
            $extra = implode(', ', $extra);
            if(!empty($extra)) {
                $extra = ','. $extra;
            }
            
            $queryTemplate = <<<CYPHER
USING PERIODIC COMMIT 1000
LOAD CSV WITH HEADERS FROM "file:{$this->config->projects_root}/projects/.exakat/nodes.g3.$atom.csv" AS csvLine
CREATE (token:$atom { 
eid: toInt(csvLine.id),
code: csvLine.code,
fullcode: csvLine.fullcode,
line: toInt(csvLine.line),
token: csvLine.token,
rank: toInt(csvLine.rank)
$extra})

CYPHER;
            try {
                $res = $this->cypher->query($queryTemplate);
                if (isset($res->message)) {
                    print $queryTemplate."\n";
                    print_r($res);
                    die();
                }

                $this->unlink[] = $file;
                $e = microtime(true);
            } catch (Exception $e) {
                $this->cleanCsv(); 
                die("Couldn't load nodes in the database\n".$e->getMessage());
            }
        }
        display('Loaded nodes');
        
        // Load relations
        $files = glob($this->config->projects_root.'/projects/.exakat/rels.g3.*.csv');
        foreach($files as $file) {
            preg_match('/rels\.g3\.(.*)\.(.*)\.(.*)\.csv$/', $file, $r);
            $edge = $r[1];
            $origin = $r[2];
            $destination = $r[3];
            
            $b = microtime(true);
            $queryTemplate = <<<CYPHER
USING PERIODIC COMMIT 1000
LOAD CSV WITH HEADERS FROM "file:{$this->config->projects_root}/projects/.exakat/rels.g3.$edge.$origin.$destination.csv" AS csvLine
MATCH (token:$origin { eid: toInt(csvLine.start)}),(token2:$destination { eid: toInt(csvLine.end)})
CREATE (token)-[:$edge]->(token2)

CYPHER;
            try {
                $res = $this->cypher->query($queryTemplate);
                if (isset($res->message)) {
                    print $queryTemplate."\n";
                    print_r($res);
                    die();
                }
                $this->unlink[] = $file;
                $e = microtime(true);
            } catch (Exception $e) {
                $this->cleanCsv(); 
                die("Couldn't load relations for ".$edge." in the database\n".$e->getMessage());
            }

        }
        display('Loaded links');

        $this->cleanCsv();
        display('Cleaning CSV');

        return true;
    }

    private function cleanCsv() {
        if (empty($this->unlink)) {
            return ;
        }
        foreach($this->unlink as $file) {
            unlink($file);
        }
    }

    public function saveTokenCounts() {
        $config = Config::factory();
        $datastore = new Datastore($config);
        
        $datastore->addRow('tokenCounts', static::$tokenCounts);
    }
    
    public function makeNode() {
        return new static();
    }
    
    public function setProperty($name, $value) {
        if ($this->isLink) {
            static::$lastLink[$name] = $value;
        } else {
            if (!isset(static::$cols[$name])) { 
                static::$cols[$name] = true; 
            }

            $this->node[$name] = $value;
        }
        
        return $this;
    }

    public function hasProperty($name) {
        if ($this->isLink) {
            return isset(static::$lastLink[$name]);
        } else {
            return isset($this->node[$name]);
        }
    }

    public function getProperty($name) {
        if ($this->isLink) {
            return static::$lastLink[$name];
        } else {
            return $this->node[$name];
        }
    }
    
    public function save() {
        if (empty($this->id)) {
            ++static::$count;
            $this->id = static::$count;
            static::$nodes[$this->id] = &$this->node;
        } else {
            static::$nodes[$this->id] = &$this->node;
        }
        
        $this->isLink = false;
        
        return $this;
    }

    public function relateTo($destination, $label) {
        static::$links[$label][] = array('origin' => $this->id, 
                                         'destination' => $destination->id, 
                                         'label' => $label
                                 );
        
        if (isset($this->node['index'])) { 
            static::$indexedId[$this->id] = 1;
        }

        static::$lastLink = &static::$links[$label][count(static::$links[$label]) - 1];
        $this->isLink = true;

        return $this;
    }

    public function escapeString($string) {
        $x = str_replace("\\", "\\\\", $string);
        return str_replace("\"", "\\\"", $x);
    }
}

?>

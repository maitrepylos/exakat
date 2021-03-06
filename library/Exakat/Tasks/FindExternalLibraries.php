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


namespace Exakat\Tasks;

use Exakat\Config;
use Exakat\Phpexec;
use Exakat\Tasks\Precedence;
use Exakat\Exceptions\NoSuchProject;
use Exakat\Exceptions\ProjectNeeded;

class FindExternalLibraries extends Tasks {
    const CONCURENCE = self::ANYTIME;

    const WHOLE_DIR   = 1;
    const FILE_ONLY   = 2;
    const PARENT_DIR  = 3; // Whole_dir and parent.
    
    private $php = null;

    // classic must be in lower case form.
    private $classic = array('adoconnection'    => self::WHOLE_DIR,
                             'bbq'              => self::WHOLE_DIR,
                             'cpdf'             => self::WHOLE_DIR, // ezpdf
                             'cakeplugin'       => self::PARENT_DIR, // cakephp
                             'dompdf'           => self::PARENT_DIR,
                             'fpdf'             => self::FILE_ONLY,
                             'graph'            => self::PARENT_DIR, // Jpgraph
                             'html2pdf'         => self::WHOLE_DIR, // contains tcpdf
                             'htmlpurifier'     => self::FILE_ONLY,
                             'http_class'       => self::WHOLE_DIR,
                             'idna_convert'     => self::WHOLE_DIR,
                             'lessc'            => self::FILE_ONLY,
                             'magpierss'        => self::WHOLE_DIR,
                             'markdown_parser'  => self::FILE_ONLY,
                             'markdown'         => self::WHOLE_DIR,
                             'mpdf'             => self::WHOLE_DIR,
                             'oauthtoken'       => self::WHOLE_DIR,
                             'passwordhash'     => self::FILE_ONLY,
                             'pchart'           => self::WHOLE_DIR,
                             'pclzip'           => self::FILE_ONLY,
                             'gacl'             => self::WHOLE_DIR,
                             'propel'           => self::PARENT_DIR,
                             'gettext_reader'   => self::WHOLE_DIR,
                             'phpexcel'         => self::WHOLE_DIR,
                             'phpmailer'        => self::WHOLE_DIR,
                             'qrcode'           => self::FILE_ONLY,
                             'services_json'    => self::FILE_ONLY,
                             'sfyaml'           => self::WHOLE_DIR,
                             'swift'            => self::PARENT_DIR,
                             'smarty'           => self::WHOLE_DIR,
                             'tcpdf'            => self::WHOLE_DIR,
                             'text_diff'        => self::WHOLE_DIR,
                             'text_highlighter' => self::WHOLE_DIR,
                             'tfpdf'            => self::WHOLE_DIR,
                             'utf8'             => self::WHOLE_DIR,
                             'ci_xmlrpc'        => self::FILE_ONLY,
                             'xajax'            => self::PARENT_DIR,
                             'yii'              => self::WHOLE_DIR,
                             'zend_view'        => self::WHOLE_DIR,
                             );

    // classic must be in lower case form.
    private $classicTests = array('phpunit_framework_testcase'    => self::WHOLE_DIR, // PHPunit
                                  'codeception\test\unit'         => self::WHOLE_DIR, // Codeception
                                  'objectbehavior'                => self::WHOLE_DIR, // PHP spec
                                  'unittestcase'                  => self::WHOLE_DIR, // Simpletest
                                  'atoum'                         => self::WHOLE_DIR, // Atoum
                                  // behat, peridot, kahlan, phpt?
                                   );

    public function run(Config $config) {
        $project = $config->project;
        if ($project == 'default') {
            throw new ProjectNeeded();
        }

        if (!file_exists($config->projects_root.'/projects/'.$project.'/')) {
            throw new NoSuchProject();
        }

        $dir = $config->projects_root.'/projects/'.$project.'/code';
        $configFile = $config->projects_root.'/projects/'.$project.'/config.ini';
        $ini = parse_ini_file($configFile);
        
        if ($config->update && isset($ini['FindExternalLibraries'])) {
            display('Not updating '.$project.'/config.ini. This tool was already run. Please, clean the config.ini file in the project directory, before running it again.');
            return; //Cancel task
        }
    
        display('Processing files');
        $files = $this->datastore->getCol('files', 'file');
        if (empty($files)) {
            display('No files to process. Aborting');
            return;
        }

        $this->php = new Phpexec();

        $this->php->getTokens();
        Precedence::preloadConstants($this->php->getActualVersion());
        
        $r = array();
        $path = $config->projects_root.'/projects/'.$project.'/code';
        rsort($files);
        $ignore = 'None';
        $ignoreLength = 0;
        foreach($files as $id => $file) {
            if (substr($file, 0, $ignoreLength) == $ignore) { print "Ignore $file ($ignore)\n"; continue; }
            $s = $this->process($path.$file);
            
            if (!empty($s)) {
               $r[] = $s;
               $ignore = array_pop($s);
               $ignoreLength = strlen($ignore);
            }
       }

       if (!empty($r)) {
           $newConfigs = call_user_func_array('array_merge', $r);
        } else {
            $newConfigs = array();
        }
//        $newConfigs = array_keys(array_count_values(array_values($newConfigs)));

        if (count(array_keys($newConfigs)) == 1) {
            display('One external library is going to be omitted : '.implode(', ', array_keys($newConfigs)));
        } elseif (!empty($newConfigs)) {
            display(count(array_keys($newConfigs)).' external libraries are going to be omitted : '.implode(', ', array_keys($newConfigs)));
        }

        $store = array();
        foreach($newConfigs as $library => $file) {
            $store[] = array('library' => $library,
                             'file'    => $file);
        }

        $this->datastore->cleanTable('externallibraries');
        $this->datastore->addRow('externallibraries', $store);

        if ($config->update === true && !empty($newConfigs)) {
             display('Updating '.$project.'/config.ini');
             $ini = file_get_contents($configFile);
             $ini = preg_replace("#(ignore_dirs\[\] = \/.*?\n)\n#is", '$1'."\n".';Ignoring external libraries'."\n".'ignore_dirs[] = '.implode("\n".'ignore_dirs[] = ', $newConfigs)."\n;Ignoring external libraries\n\n", $ini);

             $ini .= "\nFindExternalLibraries = 1\n";

             file_put_contents($configFile, $ini);
        } else {
            display('Not updating '.$project.'/config.ini. '.count($newConfigs).' external libraries found');
        }
    }
    
    private function process($filename) {
        $return = array();

        $tokens = $this->php->getTokenFromFile($filename);
        if (count($tokens) == 1) {
            return $return;
        }
        $this->log->log("$filename : ".count($tokens));
        
        foreach($tokens as $id => $token) {
            if (is_string($token)) { continue; }

            if ($token[0] == T_WHITESPACE)  { continue; }
            if ($token[0] == T_DOC_COMMENT) { continue; }
            if ($token[0] == T_COMMENT)     { continue; }
        
            if ($token[0] == T_CLASS) {
                if (!is_array($tokens[$id + 2])) { continue; }
                $class = $tokens[$id + 2][1];
                if (!is_string($class)) {
                    // ignoring errors in the parsed code. Should go to log.
                    continue;
                }

                $lclass = strtolower($class);
                if (isset($this->classic[$lclass])) {
                    if ($this->classic[$lclass] == static::WHOLE_DIR) {
                        $returnPath = dirname(preg_replace('#.*projects/.*?/code/#', '/', $filename));
                    } elseif ($this->classic[$lclass] == static::PARENT_DIR) {
                        $returnPath = dirname(dirname(preg_replace('#.*projects/.*?/code/#', '/', $filename)));
                    } elseif ($this->classic[$lclass] == static::FILE_ONLY) {
                        $returnPath = preg_replace('#.*projects/.*?/code/#', '/', $filename);
                    }
                    if ($returnPath != '/') {
                        $return[$class] = $returnPath;
                    }
                }
                
                if (is_array($tokens[$id + 4]) && $tokens[$id + 4][0] == T_EXTENDS) {
                    $ix = $id + 6;
                    $extends = '';
                    
                    while($tokens[$ix][0] == T_NS_SEPARATOR || $tokens[$ix][0] == T_STRING ) {
                        $extends .= $tokens[$ix][1];
                        ++$ix;
                    }
                    
                    $extends = trim(strtolower($extends), '\\');
                    if (isset($this->classicTests[$extends])) {
                        if ($this->classicTests[$extends] == static::WHOLE_DIR) {
                            $returnPath = dirname(preg_replace('#.*projects/.*?/code/#', '/', $filename));
                        } elseif ($this->classicTests[$extends] == static::PARENT_DIR) {
                            $returnPath = dirname(dirname(preg_replace('#.*projects/.*?/code/#', '/', $filename)));
                        } elseif ($this->classicTests[$extends] == static::FILE_ONLY) {
                            $returnPath = preg_replace('#.*projects/.*?/code/#', '/', $filename);
                        }
                        if ($returnPath != '/') {
                            $return[$class] = $returnPath;
                        }
                    } 
                }
            }
        }
    
        return $return;
    }
}

?>

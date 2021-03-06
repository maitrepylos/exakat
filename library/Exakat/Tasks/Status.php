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
use Exakat\Exceptions\NoSuchProject;
use Exakat\Exceptions\ProjectNeeded;
use Exakat\Reports\Reports;

class Status extends Tasks {
    const CONCURENCE = self::ANYTIME;
    
    public function run(Config $config) {
        $project = $config->project;

        if ($project === 'default') {
            $status = array();

            if (file_exists($config->projects_root.'/projects/.exakat/Project.json')) {
                if (file_exists($config->projects_root.'/projects/.exakat/Project.json')) {
                    $json = json_decode(file_get_contents($config->projects_root.'/projects/.exakat/Project.json'));
                } else {
                    $json = new \Stdclass();
                    $json->project = '';
                    $json->step = '';
                
                }
                $res = $this->gremlin->query('g.V().hasLabel("Project").values("fullcode")');
                $inGraph = isset($res->results[0]) ? $res->results[0] : '<None>';
                $status = array('Running'  => 'Project',
                                'project'  => $json->project,
                                'in graph' => isset($res->results[0]) ? $res->results[0] : '<N/A>',
                                'step'     => $json->step,);
            } else {
                $status['Running'] = 'idle';
            }
            //$status = array('project' => $project);
            
            $this->display($status, $config->json);
            return;
        }

        $path = $config->projects_root.'/projects/'.$project;
        
        if (!file_exists($path.'/')) {
            throw new NoSuchProject($project);
        }

        $status = array('project' => $project);
        $status['loc'] = $this->datastore->getHash('loc');
        $status['tokens'] = $this->datastore->getHash('tokens');
        if (file_exists($config->projects_root.'/projects/.exakat/Project.json')) {
            $json = json_decode(file_get_contents($config->projects_root.'/projects/.exakat/Project.json'));
            if ($json->project === $project) {
                $status['status'] = $json->step;
            } else {
                $status['status'] = 'Not running';
            }
        } else {
            $status['status'] = 'Not running';
        }

        switch($config->project_vcs) {
            case 'git' :
                if (file_exists($config->projects_root.'/projects/'.$config->project.'/code/')) {
                    $status['git status'] = trim(shell_exec('cd '.$config->projects_root.'/projects/'.$config->project.'/code/; git rev-parse HEAD'));
                }
                
                if (file_exists($config->projects_root.'/projects/'.$config->project.'/code/')) {
                    $res = shell_exec('cd '.$config->projects_root.'/projects/'.$config->project.'/code/; git remote update; git status -uno | grep \'up-to-date\'');
                    $status['updatable'] = empty($res);
                } else {
                    $status['updatable'] = false;
                }
                break 1;

            case 'composer' :
                $json = @json_decode(@file_get_contents($config->projects_root.'/projects/'.$config->project.'/code/composer.lock'));
                if (isset($json->hash)) {
                    $status['hash'] = $json->hash;
                } else {
                    $status['hash'] = 'Can\'t read hash';
                }
                
                $res = shell_exec('cd '.$config->projects_root.'/projects/'.$config->project.'; git remote update; git status -uno | grep \'Nothing to install or update\'');
                $status['updatable'] = empty($res);
                break 1;

            case 'copy' :
            case 'symlink' :
                $status['hash'] = 'None';
                $status['updatable'] = false;
                break 1;

            default:
                $status['hash'] = 'None';
                $status['updatable'] = 'N/A';
                break 1;
        }
        
        

        $configuration = array();
        // 

        // Check the logs
        $errors = $this->getErrors($path);
        if (!empty($errors)) {
            $status['errors'] = $errors;
        }
        

        // Status of progress
        // errors? 

        $formats = array();
        foreach(Reports::$FORMATS as $format) {
            $a = $this->datastore->getHash($format);
            if (!empty($a)) {
                $formats[$format] = $a;
            }
        }
        // Always have formats, even if empty
        $status['formats'] = $formats;
        
        $this->display($status, $config->json);
    }
    
    private function display($status, $json = false) {
        // Json publication
        if ($json === true) {
            print json_encode($status);
            return;
        } 
        
        // commandline publication
        $text = '';
        $size = 0;
        foreach($status as $k => $v) {
            $size = max($size, strlen($k));
        }

        foreach($status as $field => $value) {
            if (is_array($value)) {
                $sub = substr($field.str_repeat(' ', $size), 0, $size)." : \n";

                $sizea = 0;
                foreach($value as $k => $v) {
                    $sizea = max($sizea, strlen($k));
                }
                foreach($value as $k => $v) {
                    $sub .= "    ".substr($k.str_repeat(' ', $sizea), 0, $sizea)." : $v\n";
                }
                $text .= "\n".$sub."\n";
            } else {
                $text .= substr($field.str_repeat(' ', $size), 0, $size) . ' : '.$value."\n";
            }
        }
        
        print $text;
    }
    
    private function getErrors($path) {
        $errors = array();

        // Init error
        $e = $this->datastore->getHash('init error');
        if (!empty($e)) {
            $errors['init error'] = $e;
            return $errors;
        }
        
        // Size error
        $e = $this->datastore->getHash('token error');
        if (!empty($e)) {
            $errors['init error'] = $e;
            return $errors;
        }
        
        return $errors;
    }
}

?>

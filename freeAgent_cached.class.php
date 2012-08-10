<?php

require 'freeAgent.class.php';

class freeAgent_cached extends freeAgent
{
    protected $cache = array('project' => array(), 'timeslips' => array());

    protected $filename = '.freeAgent_cache';

    public function __construct($token)
    {
        parent::__construct($token);
        if(file_exists($this->filename)) {
            $this->cache = unserialize(file_get_contents($this->filename));
        }
    }

    public function __destruct()
    {
        file_put_contents($this->filename, serialize($this->cache));
    }

    public function getProject($project_id)
    {
        if(!isset($this->cache['project'][ $project_id ])) {
            $this->cache['project'][ $project_id ] = parent::getProject($project_id);
        }

        return $this->cache['project'][ $project_id ];
    }

}

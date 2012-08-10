<?php

class freeAgent_timeslips
{
    protected $timeslips;

    public function __construct($timeslips)
    {
        $this->timeslips = $timeslips;
    }

    public function report()
    {
        $data = '';
        foreach($this->timeslips as $entry)
        {
            $data .= sprintf("%d\t%d\t%s %.2f %s\n", basename($entry->project), basename($entry->task), $entry->dated_on, $entry->hours, $entry->comment);
        }

        return $data;
    }

    public function reportByProject()
    {
        $projects = array();
        $sort     = array();
        $data     = '';
        foreach($this->timeslips as $entry)
        {
            $project_id = basename($entry->project);
            if(!isset($projects[ $project_id ])) {
                $projects[ $project_id ] = array('hours' => 0, 'tasks' => array());
                $sort[ $project_id ] = 0;
            }
            $sort[ $project_id ] += $entry->hours;
            $projects[ $project_id ]['id']      = $project_id;
            $projects[ $project_id ]['hours']  += $entry->hours;
            $projects[ $project_id ]['tasks'][] = $entry;
        }
        array_multisort($sort, SORT_DESC, $projects);

        return $projects;
    }
}


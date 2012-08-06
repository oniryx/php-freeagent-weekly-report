<?php

define(TOKEN, file_get_contents('TOKEN'));

/*
curl
https://api.freeagent.com/v2/timeslips 
-H "Authorization: Bearer 1B9BavQXH1qAyC9_7VPZTIDro0-VNCYSIOXTPbWcb"
-H "Accept: application/json"
-H "Content-Type: application/json"
-X GET
 */



class freeAgent
{
    protected $token;

    protected $ch;

    public function __construct($token)
    {
        $this->token = $token;
        $this->ch    = curl_init();
    }

    function request($url, $method='GET', $type='application/json')
    {
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->ch, CURLOPT_URL, $url);
        curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($this->ch, CURLOPT_USERAGENT, 'PHP Script');
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, array(
                "Authorization: Bearer $this->token",
                "Accept: $type",
                "Content-type: $type",
            ));

        return curl_exec($this->ch);
    }

    function timeslips()
    {
        return $this->request('https://api.freeagent.com/v2/timeslips');
    }
}

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
        $data     = '';
        foreach($this->timeslips as $entry)
        {
            $projects[ basename($entry->project) ][ basename($entry->task) ][ basename($entry->url) ] = $entry;
        }

        foreach($projects as $project_id=>$tasks)
        {
            $total_project = 0;
            $project_task = '';
            foreach($tasks as $task_id=>$entries) {
                $total_task = 0;
                foreach($entries as $entry_id=>$entry) {
                    $total_task += $entry->hours;
                }
                $project_task .= sprintf("\t%d\t%.2f\n", $task_id, $total_task);
                $total_project += $total_task;
            }
            $data .= sprintf("%d\t%.2f\n%s", $project_id, $total_project, $project_task);
        }

        return $data;
    }
}

$fa = new freeAgent(TOKEN);
$timeslips = json_decode($fa->timeslips());
$t  = new freeAgent_timeslips($timeslips->timeslips);

echo $t->report();
echo $t->reportByProject();


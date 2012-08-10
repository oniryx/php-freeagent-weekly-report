<?php

class freeAgent
{
    const API_URL = 'https://api.freeagent.com/v2/';

    protected $token;

    protected $ch;

    public function __construct($token)
    {
        $this->token = $token;
        $this->ch    = curl_init();
        $this->cache = $cache;
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

    function getTimeslips($from_date=null, $to_date=null)
    {
        $response = json_decode($this->request(self::API_URL.'timeslips'.(is_null($from_date)?'':"?from_date=$from_date&to_date=$to_date")));

        return $response->timeslips;
    }

    function getProject($project_id)
    {
        $response = json_decode($this->request(self::API_URL.'/projects/'.$project_id));

        return $response->project;
    }
}


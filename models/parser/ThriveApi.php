<?php

namespace app\models\parser;

class ThriveApi
{
    private $url;
    private $key;
    private $secret;
    private $hash;
    private $offset;
    private $callCount;
    public $result;

    public function __construct($config)
    {
        $this->url = $config['url'];
        $this->key = $config['key'];
        $this->secret = $config['secret'];
        if (array_key_exists('offset', $config)) {
            $this->offset = $config['offset'];
        } else {
            $this->offset = 0;
        }
        $this->callCount = 0;
    }

    private function makeApiHash($key, $secret, $offset=0)
    {
        if(!is_string($key) || strlen($key) != 32)
        {
            return false;
        }
        if(!is_string($secret) || strlen($secret) != 16)
        {
            return false;
        }
        $now = time() + ($offset * 60);
        // hash changes every 60 seconds
        $date = (int)round(ceil(($now / 60)) * 60);
        return hash('sha256', md5($key . $secret . $date));
    }

    public function hasError()
    {
        if (isset($this->result['error']) && $this->result['error'] == true)
        {
            $has = true;
        }
        else
        {
            $has = false;
        }
        return $has;
    }

    public function generateException()
    {
        if (isset($this->result['debug']) && isset($this->result['debug']['time']))
        {
            $now = time();
            $error = ($now - $this->result['debug']['time']) . ' seconds difference between server --> set offset to ' . round(($now - $this->result['debug']['time']) / 60);
        }
        elseif (isset($this->result['message']))
        {
            $error = $this->result['message'];
        }
        else
        {
            $error = 'Unknown error';
        }
        throw new \Exception($error);
    }

    public function getCallCount()
    {
        return $this->callCount;
    }

    public function call($method, $postData = array(), $json_result = true)
    {
        $url = $this->url.$method;
        $postData['_8663a5a'] = $this->makeApiHash($this->key, $this->secret, $this->offset);
        $result =  ParserUtils::postCURL($url, $postData);
        $this->result = json_decode($result, true);
        $this->callCount++;
        if ($json_result)
        {
            return $this->result;
        }
        else
        {
            return $result;
        }
    }

    public function getMetricsTable($searchDate){
        $thriveMethod = "campaigns/getMetricsTable";
        $params = array(
            'customFrom' => $searchDate->format('m/d/Y'),//$startDate,
            'customTo' => $searchDate->format('m/d/Y'), //$endDate,
        );
        try {
            $results = $this->call($thriveMethod, $params);
        } catch (Exception $e) {
            echo $e->getMessage();
            die();
        };
        return $results;
    }

    public function getWithMetricsNetwork($searchDate, $timeZone, $networkName='E'){
        $thriveMethod = "networks/getWithMetrics";
        $nextDate = clone $searchDate;
        date_add($nextDate, date_interval_create_from_date_string('+1 days'));
        $params = array(
            'search' => 'Network ' . $networkName,
            'range' =>[
                'from' => $searchDate->format('m/d/Y'),
                'to' => $nextDate->format('m/d/Y'),
                'timezone' => $timeZone
            ],
        );
        try {
            $results = $this->call($thriveMethod, $params);
        } catch (Exception $e) {
            echo $e->getMessage();
            die();
        }
        return $results;
    }

}
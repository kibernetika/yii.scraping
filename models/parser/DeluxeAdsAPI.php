<?php

namespace app\models\parser;


class DeluxeAdsAPI
{
    private $postData;
    private $url;

    public function __construct($config, $urlSource)
    {
        $this->url = $urlSource;
        $this->postData['start_at_row'] = 1;
        $this->postData['row_limit'] = 300;
        $this->postData['api_key'] = $config['api_key'];
        $this->postData['affiliate_id'] = $config['affiliate_id'];
        $this->postData['site_offer_id'] = 0;
    }

    public function getRevenu($startDate, $endDate, $method = 'affiliates/api/3/reports.asmx/DailySummary'){
        $url = $this->url.$method;
        $start = $startDate->format('m/d/Y H:i:s');
        $this->postData['start_date'] = $start;
        $this->postData['end_date'] = date_add($endDate, date_interval_create_from_date_string('+1 days'))->format('m/d/Y H:i:s');
        $result = ParserUtils::postCURL($url, $this->postData);
        return $result;
    }

}
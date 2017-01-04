<?php
/**
 * Created by PhpStorm.
 * User: cybernetics
 * Date: 19.09.2016
 * Time: 17:34
 */

namespace app\models\parser;


use yii\base\Exception;

class ClicksureAPI
{
    private $apiKey;
    private $url;

    public function __construct($config)
    {
        $this->url = 'https://www.clicksure.com/';
        $this->apiKey = $config['api_key'];
    }

    public function parseClicksureApi($date, $method)
    {
        $url = $this->url.$method;
        $headers = array(
            'Content-Type:text/xml',
            'Authorisation:' . $this->apiKey,
        );
        // Create new CURL Instance
        $session = curl_init($url);
        $root = '<?xml version="1.0" encoding="UTF-8"?><xml/>';
        $xml_object = new \simpleXMLElement($root);
        $child = $xml_object->addChild('data');
        // Set the start date and end date to today
        $child->addChild( 'start', date($date->format('Y-m-d')) );
        $child->addChild( 'end', date($date->format('Y-m-d')) );
        // Convert object to string
        $xml_data = $xml_object->asXML();
        curl_setopt_array($session, array(
            CURLOPT_TIMEOUT        => 90,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_FAILONERROR    => true,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_CUSTOMREQUEST  => 'POST',
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_POSTFIELDS     => $xml_data
        ));
        // Post data and get xml response
        $response = curl_exec($session);
        // Convert xml string to object
        $result = simplexml_load_string($response);
        if($response === false)
        {
            $error = curl_error($session);
            throw new Exception('CURL ERROR' . $error);
        }
        // output all of the data
        $totalSales = 0;
        $totalActions = 0;
        try {
            foreach ($result->data->metric as $row) {
                // echo '<br> Campaign Name - '.$row->campaignName;
                // echo '<br> Campaign Type - '.$row->type;
                // echo '<br> Clicks - '.$row->clicks;
                $totalActions = $totalActions + $row->actions;
                // echo '<br> Conversion Percentage - '.$row->conversionPercentage;
                // echo '<br> EPC - '.$row->epc;
                $totalSales = $totalSales + str_replace(",", "", substr($row->sales, 1));
            }
        } catch (Exception $ex) {}
        return $totalSales;
    }
}
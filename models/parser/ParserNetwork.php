<?php
/**
 * Created by PhpStorm.
 * User: cybernetics
 * Date: 04.10.2016
 * Time: 12:49
 */

namespace app\models\parser;

use Yii;
use app\models\table;
use app\models\table\Source;
use app\models\parser\ParserUtils;
use yii\base\Exception;

class ParserNetwork
{
    public static function loadNewData($user = null){
        $user = is_null($user) ? Yii::$app->user->id : $user;
        try{
            self::callParserAPI('Deluxe Ads','Deluxe Ads', $user);
            self::callParserAPI('Clicksure','Clicksure', $user);
            self::callParserAPI('Mobooka','Mobooka', $user);
            self::callParserAPI('Convert2Media','Convert2Media', $user);
            self::callParserAPI('Kainero','Kainero', $user);
        }catch( Exception $ex){}
        $source = Source::find()
            ->andFilterWhere(['and', ['need_manual'=>1, 'type'=>2]] )
            ->all();
        foreach ($source as $item){
            if ( ! ParserUtils::existAccount($user, $item->attributes['id']) ) continue;
            self::callParserAPI('Manual', $item['name'], $user, '+1');
        }
        $id_source = ParserUtils::getIdSource('source', 'Casino Rewards');
        if ( ! ParserUtils::existAccount($user, $id_source) ) return null;
        $startDate = ParserUtils::getStartDate('source_return', $id_source, $user)->modify('+1 days');
        $id_sourceParse = ParserUtils::getIdSource('source', 'Thrive');
        $endDate = new \DateTime();
        $endDate = $endDate->modify('-1 days');
        if ( $startDate <= $endDate ){
            $parseData = self::parseCasinoRewards($startDate, $endDate, $id_source, $id_sourceParse, $user);
            if( ! is_null($parseData) )
                try{
                Yii::$app->db->createCommand()
                    ->batchInsert('source_return', ['id_source', 'date', 'date_create', 'return', 'conversion', 'id_user', 'update_manual'], $parseData)
                    ->execute();
                }catch( \mysqli_sql_exception $error){ return; }
        }
    }

    public static function callParserAPI($parseMethod, $sourceName, $user, $day = '+1'){
        $endDate = new \DateTime();
        date_add($endDate, date_interval_create_from_date_string('-1 days'));
        $id_source = ParserUtils::getIdSource('source', $sourceName);
        if ( ! ParserUtils::existAccount($user, $id_source) ) return null;
        $startDate = ParserUtils::getStartDate('source_return', $id_source, $user)->modify($day . ' days');
        $parseMethod = 'app\models\parser\ParserNetwork::parse' . str_replace(' ','',$parseMethod);
        if ( $startDate <= $endDate ){
            $parseData = call_user_func($parseMethod, $startDate, $endDate, $id_source, $user);
            $fields = ['id_source', 'date', 'date_create', 'return', 'conversion', 'id_user','update_manual'];
            if ( $parseMethod == 'app\models\parser\Parser::parseManual' )
                $fields[] = 'update_manual';
            try{
                Yii::$app->db->createCommand()
                    ->batchInsert('source_return', $fields, $parseData)
                    ->execute();
            }catch( \mysqli_sql_exception $error){ return; }
        }
    }

    public static function parseThrive($startDate, $endDate, $id_source, $user)
    {
        $account = Yii::$app->db->createCommand
        ("SELECT * FROM account WHERE id_source = ".$id_source."  AND id_user = '".$user."' ")
            ->queryOne();
        $config = [
            'thrive' => [
                'url' => 'http://clicksthrough.com/ajax/',
                'key' => $account['api_first_key'],
                'secret' => $account['api_second_key'],
                'offset' => 0
            ]
        ];
        $searchDate = $startDate;
        $result = array();
        while( $searchDate < $endDate ){
            $thriveAPI = new ThriveApi($config['thrive']);
            $results = $thriveAPI->getMetricsTable($searchDate);
            $thriveReturn = $results['data'][0]['rev'];
            if (is_null($thriveReturn)) {
                echo '<script>alert("Clicksthrough.com not login. Try again later!")</script>';
                return $result;
            }
            $thriveSpend = $results['data'][0]['cost'];
            $thriveConv = $results['data'][0]['conv'];
            $resultsICanada = $thriveAPI->getWithMetricsNetwork($searchDate, 'US/Eastern', 'I');
            if ( ! isset($resultsICanada['metrics']) ) continue;
            $netvI_Return_Canada = $resultsICanada['metrics'][11]['rev'];
            $resultsIAmsterdam = $thriveAPI->getWithMetricsNetwork($searchDate, 'Europe/Amsterdam', 'I');
            $netvI_Return_Amsterdam = $resultsIAmsterdam['metrics'][11]['rev'];
            $result[] = [
                'id_source' => $account['id_source'],
                'date' => date_format($searchDate, 'Y/m/d'),
                'date_create' => date_format(new \DateTime, 'Y/m/d H:m:s'),
                'spend' => $thriveSpend,
                'return' => $thriveReturn,
                'return_amsterdam' => $thriveReturn - $netvI_Return_Canada + $netvI_Return_Amsterdam,
                'conversion' => $thriveConv,
                'id_user' => $user,
            ];
            date_add($searchDate, date_interval_create_from_date_string('+1 days'));
        }
        return $result;
    }

    public static function parseDeluxeAds($startDate, $endDate, $id_source, $user){
        
        $searchDate = $startDate;
        $account = Yii::$app->db->createCommand
        ("SELECT * FROM account WHERE id_source = ".$id_source."  AND id_user = ".$user)
            ->queryOne();
        $config = [
            'api_key' => $account['api_first_key'],
            'affiliate_id' => $account['api_second_key'],
        ];
        $results = array();
        while( $searchDate < $endDate ){
            $deluxeAdsAPI = new DeluxeAdsAPI($config, 'http://deluxeadstrack.com/');
            $date = $searchDate->format('Y/m/d');
            $result = ParserUtils::convertXMLtoArr($deluxeAdsAPI->getRevenu($searchDate, $searchDate));
            if ( count($result) < 11) continue;
            $return = is_null($result[10]['value']) ? 0 : $result[10]['value'];
            $results[] = [
                'id_source' => $account['id_source'],
                'date' => $date,
                'date_create' => date_format(new \DateTime, 'Y/m/d H:m:s'),
                'return' =>   $return,
                'conversion' => 0,
                'id_user' => $user,
                'update_manual' => 0,
            ];
        }
        return $results;
    }

    public static function parseClicksure($startDate, $endDate, $id_source, $user){
        $searchDate = $startDate;
        $account = Yii::$app->db->createCommand
        ("SELECT * FROM account WHERE id_source = ".$id_source."  AND id_user = ".$user)
            ->queryOne();
        $config = [
            'api_key' => $account['api_first_key'],
            'affiliate_id' => $account['api_second_key'],
        ];
        $results = array();
        while( $searchDate < $endDate ){
            $clicksureAPI = new ClicksureAPI($config);
            $date = $searchDate->format('Y/m/d');
            $result = $clicksureAPI->parseClicksureApi($searchDate, 'affiliate/api/v1/analytics/cpa?cd%5B%5D=S1');
            if ( is_null($result) ) continue;
            $return = is_null($result) ? 0 : $result;
            $results[] = [
                'id_source' => $account['id_source'],
                'date' => $date,
                'date_create' => date_format(new \DateTime, 'Y/m/d H:m:s'),
                'return' =>  $return,
                'conversion' => 0,
                'id_user' => $user,
                'update_manual' => 0,
            ];
            date_add($searchDate, date_interval_create_from_date_string('+1 days'));
        }
        return $results;
    }

    public static function parseMobooka($startDate, $endDate, $id_source, $user){
        $searchDate = $startDate;
        $account = Yii::$app->db->createCommand
        ("SELECT * FROM account WHERE id_source = ".$id_source."  AND id_user = ".$user)
            ->queryOne();
        $config = [
            'api_key' => $account['api_first_key'],
            'affiliate_id' => $account['api_second_key'],
        ];
        $results = array();
        while( $searchDate <= $endDate ){
            $mobookaAPI = new DeluxeAdsAPI($config, 'http://mobooka.us/');
            $date = $searchDate->format('Y/m/d');
            $result = ParserUtils::convertXMLtoArr($mobookaAPI->getRevenu($searchDate, $searchDate));
            if ( count($result) < 11) continue;
            $return = is_null($result[10]['value']) ? 0 : $result[10]['value'];
            $results[] = [
                'id_source' => $account['id_source'],
                'date' => $date,
                'date_create' => date_format(new \DateTime, 'Y/m/d H:m:s'),
                'return' =>  $return,
                'conversion' => 0,
                'id_user' => $user,
                'update_manual' => 0,
            ];
        }
        return $results;
    }

    public static function parseConvert2Media($startDate, $endDate, $id_source, $user){
        $searchDate = $startDate;
        $account = Yii::$app->db->createCommand
        ("SELECT * FROM account WHERE id_source = ".$id_source."  AND id_user = ".$user)
            ->queryOne();
        $config = [
            'api_key' => $account['api_first_key'],
            'affiliate_id' => $account['api_second_key'],
        ];
        $results = array();
        while( $searchDate <= $endDate ){
            $convert2MediaAPI = new DeluxeAdsAPI($config, 'https://www.c2mtrax.com/');
            $date = $searchDate->format('Y/m/d');
            $result = ParserUtils::convertXMLtoArr($convert2MediaAPI->getRevenu($searchDate, $searchDate));
            if ( count($result) < 11) continue;
            if ( isset($result['message']) ){
                return $results;
            }
            $return = is_null($result[10]['value']) ? 0 : $result[10]['value'];
            $results[] = [
                'id_source' => $account['id_source'],
                'date' => $date,
                'date_create' => date_format(new \DateTime, 'Y/m/d H:m:s'),
                'return' =>  $return,
                'conversion' => 0,
                'id_user' => $user,
                'update_manual' => 0,
            ];
        }
        return $results;
    }

    public static function parseKainero($startDate, $endDate, $id_source, $user){
        $searchDate = $startDate;
        $account = Yii::$app->db->createCommand
        ("SELECT * FROM account WHERE id_source = ".$id_source."  AND id_user = ".$user)
            ->queryOne();
        $config = [
            'api_key' => $account['api_first_key'],
            'affiliate_id' => $account['api_second_key'],
        ];
        $results = array();
        while( $searchDate <= $endDate ){
            $kainero = new DeluxeAdsAPI($config, 'https://kainero.com/');
            $date = $searchDate->format('Y/m/d');
            $result = ParserUtils::convertXMLtoArr($kainero->getRevenu($searchDate, $searchDate));
            if ( count($result) < 11) continue;
            $return = is_null($result[10]['value']) ? 0 : $result[10]['value'];
            $results[] = [
                'id_source' => $account['id_source'],
                'date' => $date,
                'date_create' => date_format(new \DateTime, 'Y/m/d H:m:s'),
                'return' =>  $return,
                'conversion' => 0,
                'id_user' => $user,
                'update_manual' => 0,
            ];
        }
        return $results;
    }

    public static function parseManual($startDate, $endDate, $id_source, $user){
        try{
            $searchDate = $startDate;
            $results = array( );
            while( (date_format($searchDate, 'y') <= date_format($endDate, 'y')) && (date_format($searchDate, 'm') <= date_format($endDate, 'm')) ){
                $date = $searchDate->format('Y/m/t');
                $results[] = [
                    'id_source' => $id_source,
                    'date' => $date,
                    'date_create' => date_format(new \DateTime, 'Y/m/d H:m:s'),
                    'return' =>  0,
                    'conversion' => 0,
                    'id_user' => $user,
                    'update_manual' => 1,
                ];
                date_add($searchDate, date_interval_create_from_date_string('+1 months'));
            }
            return $results;
        }catch( Exception $ex){}
    }

    public static function parseCasinoRewards($startDate, $endDate, $id_source, $id_sourceParse, $user){
        $account = Yii::$app->db->createCommand
        ("SELECT * FROM account WHERE id_source = ".$id_sourceParse."  AND id_user = '".$user."' ")
            ->queryOne();
        $config = [
            'thrive' => [
                'url' => 'http://clicksthrough.com/ajax/',
                'key' => $account['api_first_key'],
                'secret' => $account['api_second_key'],
                'offset' => 0
            ]
        ];
        $searchDate = $startDate;
        $results = array();
        while( $searchDate <= $endDate ){
            $thriveAPI = new ThriveApi($config['thrive']);
            $result = $thriveAPI->getWithMetricsNetwork($searchDate, 'US/Eastern', 'E');
            if ( isset($result['message']) ){
                return $results;
            }
            $return = isset($result['metrics'][7]['rev']) ? $result['metrics'][7]['rev'] : 0;
            $results[] = [
                'id_source' => $id_source,
                'date' => $searchDate->format('Y/m/d'),
                'date_create' => date_format(new \DateTime, 'Y/m/d H:m:s'),
                'return' =>  $return,
                'conversion' => 0,
                'id_user' => $user,
                'update_manual' => 0,
            ];
            date_add($searchDate, date_interval_create_from_date_string('+1 days'));
        }
        return $results;
    }

}
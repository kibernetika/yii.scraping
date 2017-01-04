<?php
/**
 * Created by PhpStorm.
 * User: cybernetics
 * Date: 12.10.2016
 * Time: 13:34
 */

namespace app\models\parser;

use Yii;
use app\models\table\SourceAgregate;
use yii\base\Exception;
use app\models\table\Source;
use app\models\table;

class ParserAgregate
{
    public static function loadNewDataAgregate($user = null){
        $id_source = ParserUtils::getIdSource('source', 'Thrive');
        $user = is_null($user) ? Yii::$app->user->id : $user;
        if ( ! ParserUtils::existAccount($user, $id_source) ) return null;
        self::parseThrive($user);
        $source = Source::find()
            ->andFilterWhere(['and', ['need_manual'=>1, 'type'=>3]] )
            ->all();
        $fields = ['id_source', 'date', 'date_create', 'spend', 'return', 'return_amsterdam', 'conversion', 'id_user','update_manual'];
        foreach ($source as $item){
            if ( ! ParserUtils::existAccount($user, $item->attributes['id']) ) return null;
            $result = self::parseManual( $item['id'], $user );
            try{
                Yii::$app->db->createCommand()
                    ->batchInsert('source_agregate', $fields, $result)
                    ->execute();
            }catch( \mysqli_sql_exception $error){ return; }
        }
    }

    private static function parseThrive($user)
    {
        $id_source = ParserUtils::getIdSource('source', 'Thrive');
        if ( ! ParserUtils::existAccount($user, $id_source) ) return null;
        $startDate = ParserUtils::getStartDate('source_agregate', $id_source, $user)->modify('+1 days');
        $endDate = new \DateTime();
        $endDate = $endDate->modify('-1 days');
        if ( $startDate > $endDate ) return;
        try {
            $parseData = ParserNetwork::parseThrive($startDate, $endDate, $id_source, $user);
        }catch( Exception $ex){}
        if( ! is_null($parseData) ){
            foreach ($parseData as $item){
                try{
                    $sourceAgregate = new SourceAgregate($item);
                    $sourceAgregate->save();
                }catch( Exception $ex){}
            }
        }
    }

    public static function parseManual($id_source, $user){
        $endDate = new \DateTime();
        date_add($endDate, date_interval_create_from_date_string('-1 days'));
        $startDate = ParserUtils::getStartDate('source_agregate', $id_source, $user)->modify('+1 days');
        $searchDate = $startDate;
        $results = array( );
        while( (date_format($searchDate, 'y') <= date_format($endDate, 'y')) && (date_format($searchDate, 'm') <= date_format($endDate, 'm')) ){
            $date = $searchDate->format('Y/m/t');
            $results[] = [
                'id_source' => $id_source,
                'date' => $date,
                'date_create' => date_format(new \DateTime, 'Y/m/d H:m:s'),
                'spend' =>  0,
                'return' =>  0,
                'return_amsterdam' =>  0,
                'conversion' => 0,
                'id_user' => $user,
                'update_manual' => 1,
            ];
            date_add($searchDate, date_interval_create_from_date_string('+1 months'));
        }
        return $results;
    }
}
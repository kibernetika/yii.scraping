<?php

namespace app\models\view;

use Yii;
use DateTime;
/**
 * This is the ActiveQuery class for [[SourceAgregate]].
 *
 * @see SourceAgregate
 */
class MainAgregateQuery extends \yii\db\ActiveQuery
{
    public static function data($start = null, $end = null, $user = null)
    {
        if( is_null($start) ){
            $start = mktime(0, 0, 0, date('m'), 1, date("Y"));
            $start = gmdate("Y-m-d", $start);
            $end = new DateTime('now');
            $end = $end->format('Y-m-d');
        }
        $resultQuery = Yii::$app->db->createCommand
        ('SELECT source_agregate.`date`, source_agregate.spend, f_spend, source_agregate.`return`, source_agregate.return_amsterdam, f_return, source_agregate.conversion, '.
        '    DAYNAME(source_agregate.`date`) as `day` '.
        ' FROM source_agregate '.
        '    LEFT JOIN '.
            '   (SELECT source_return.`date`, SUM(source_return.`return`) as f_return
                FROM source_return
                GROUP BY source_return.`date`) as t_return '
        .' ON source_agregate.`date`=t_return.`date` '.
        '    LEFT JOIN '.
                ' (SELECT source_spend.`date`, SUM(source_spend.`spend`) as f_spend
                FROM source_spend
                GROUP BY source_spend.`date`) as t_spend '
        .' ON source_agregate.`date`=t_spend.`date` '.
        ' GROUP BY source_agregate.`date`'.
        " HAVING DATE(source_agregate.`date`) BETWEEN STR_TO_DATE('".$start."', '%Y-%m-%d') and STR_TO_DATE('".$end."', '%Y-%m-%d') ".
        ' ORDER BY 1 ASC'
        )
        ->queryAll();
        $countItems = count($resultQuery) == 0 ? 1 : count($resultQuery);
        $totalItem = [
            'date' => 'Total',
            'day' => '',
            'spendThrive' => 0,
            'spendNetwork' => 0,
            'differenceSpend' => 0,
            'differenceSpendPers' => 0,
            'returnNetw' => 0,
            'returnThrive' => 0,
            'returnNetwAmsterdam' => 0,
            'differenceReturn' => 0,
            'profit' => 0,
            'ROI' => 0,
            'conversions' => 0,
        ];
        foreach($resultQuery as $item){
            $curentItem = [
                'date' => $item['date'],
                'day' => $item['day'],
                'spendThrive' => $item['spend'],
                'spendNetwork' => $item['f_spend'],
                'differenceSpend' => round( $item['spend'] - $item['f_spend'] , 2 ),
                'differenceSpendPers' => round((($item['spend'] - $item['f_spend']) / ($item['f_spend'] == 0 ? 1000000000 : $item['f_spend']) * 100) , 2),
                'returnNetw' => $item['f_return'],
                'returnThrive' => $item['return'],
                'returnNetwAmsterdam' => $item['return_amsterdam'],
                'differenceReturn' => ( $item['return_amsterdam'] - $item['f_return'] ),
                'profit' => ( $item['return_amsterdam'] - $item['f_spend'] ),
                'ROI' => round( ($item['return_amsterdam'] - $item['f_spend']) / ($item['f_spend'] == 0 ? 1000000000 : $item['f_spend']) * 100 , 5),
                'conversions' => $item['conversion'],
            ];
            $resultArrayFinaly[] = $curentItem;
            $totalItem = [
                'date' => 'Total',
                'day' => '',
                'spendThrive' => $totalItem['spendThrive'] + $curentItem['spendThrive'],
                'spendNetwork' => $totalItem['spendNetwork'] + $curentItem['spendNetwork'],
                'differenceSpend' => $totalItem['differenceSpend'] + $curentItem['differenceSpend'],
                'differenceSpendPers' => $totalItem['differenceSpendPers'] + $curentItem['differenceSpendPers'],
                'returnNetw' => $totalItem['returnNetw'] + $curentItem['returnNetw'],
                'returnThrive' => $totalItem['returnThrive'] + $curentItem['returnThrive'],
                'returnNetwAmsterdam' => $totalItem['returnNetwAmsterdam'] + $curentItem['returnNetwAmsterdam'],
                'differenceReturn' => $totalItem['differenceReturn'] + $curentItem['differenceReturn'],
                'profit' => $totalItem['profit'] + $curentItem['profit'],
                'ROI' => $totalItem['ROI'] + $curentItem['ROI'],
                'conversions' => $totalItem['conversions'] + $curentItem['conversions'] ,
            ];
        }
        $averageItem = [
            'date' => 'Average',
            'day' => '',
            'spendThrive' => round($totalItem['spendThrive'] / $countItems, 2),
            'spendNetwork' => round($totalItem['spendNetwork'] / $countItems, 2),
            'differenceSpend' => round($totalItem['differenceSpend'] / $countItems, 2),
            'differenceSpendPers' => round($totalItem['differenceSpendPers'] / $countItems, 2),
            'returnNetw' => round($totalItem['returnNetw'] / $countItems, 2),
            'returnThrive' => round($totalItem['returnThrive'] / $countItems, 2),
            'returnNetwAmsterdam' => round($totalItem['returnNetwAmsterdam'] / $countItems, 2),
            'differenceReturn' => round($totalItem['differenceReturn'] / $countItems, 2),
            'profit' => round($totalItem['profit'] / $countItems, 2),
            'ROI' => round($totalItem['ROI'] / $countItems, 2),
            'conversions' => round($totalItem['conversions'] / $countItems, 0),
        ];
        $resultArrayFinaly[] = $averageItem;
        $resultArrayFinaly[] = $totalItem;
        return $resultArrayFinaly;
    }
}

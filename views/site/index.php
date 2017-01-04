<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\jui\DatePicker;

/* @var $this yii\web\View */
/* @var $searchModel app\models\view\MainAgregateSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Parser - Master page';
?>

<script>
    function load() {
        $('#my_load').show();
        //return location.href = "<?= \yii\helpers\Url::to('load')?>";
    }
</script>

<style>
.MyMainGrid table thead, .MyMainGrid table thead a {
    background-color: #337ab7;
    color: #fff;
}
</style>

<div class="site-index">
    <p>
        <div  style="display: inline-block;">
            <?= Html::a('Load new data (All table)', ['load'], ['class' => 'btn btn-danger', 'id' => 'btnload', 'onClick'=>'load()']) ?>
        </div>
        <div style="height: 25px; width: 25px; display: none; float: left; margin: 0px 20px;" id="my_load"><img src="http://i.stack.imgur.com/FhHRx.gif"></div>
    <form action="filter" name="filterForm" style="float: right;">
        <div style="float: right; display: inline-block;">
            <a href="#" class="btn btn-primary" onclick="document.forms['filterForm'].submit(); return false;">Filter by custom date</a>
        </div>
        <div style="float: right; display: inline-block; clear: right; margin: 10px 0px;">
            <span style="margin: 5px;">From</span>
            <?
            $dateStart =  isset($start) ? $start : mktime(0, 0, 0, date('m'), 1, date("Y"));
            $dateEnd =  isset($end) ? $end : new DateTime('now');
            echo DatePicker::widget([
                'value' => $dateStart,
                'language' => 'en',
                'dateFormat' => 'yyyy-MM-dd',
                'name' => 'start'
            ]) ?>
            <span style="margin: 5px;">to</span>
            <?= DatePicker::widget([
                'value' => $dateEnd,
                'language' => 'en',
                'dateFormat' => 'yyyy-MM-dd',
                'name' => 'end'
            ]) ?>
        </div>
    </form>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'layout' => "{items}\n{pager}",
        'rowOptions' => function ($model) {
            $total = $model['date'];
            if ( ($total == 'Total') || ( $total == 'Average') ) {
                return ['style' => 'background-color: #BFBFBF; color: #000; font-weight: 600;'];
            }
        },
        'options' => [
            'class' => 'MyMainGrid',
        ],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            ['attribute' => 'date',
                'label' => 'Date',],
            ['attribute' => 'day',
                'label' => 'Day',],
            ['attribute' => 'spendThrive',
                'label' => 'Spend Thrive',],
            ['attribute' => 'spendNetwork',
                'label' => 'Spend Network',],
            ['attribute' => 'differenceSpend',
                'label' => 'Difference spend',],
            ['attribute' => 'differenceSpendPers',
                'label' => 'Difference spend(%)',],
            ['attribute' => 'returnThrive',
                'label' => 'Return Thrive',],
            ['attribute' => 'returnNetwAmsterdam',
                'label' => 'Return Network (UTC+2)',],
            ['attribute' => 'returnNetw',
                'label' => 'Return Network',],
            ['attribute' => 'differenceReturn',
                'label' => 'Difference return',],
            ['attribute' => 'profit',
                'label' => 'Profit',],
            ['attribute' => 'ROI',
                'label' => 'ROI',],
            ['attribute' => 'conversions',
                'label' => 'Conversions',],
        ],
    ]); ?>
</div>
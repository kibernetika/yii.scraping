<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\jui\DatePicker;
use app\models\table\Source;

/* @var $this yii\web\View */
/* @var $searchModel app\models\table\SourceAgregateSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Source aggregates (Thrive)';
$this->params['breadcrumbs'][] = $this->title;
?>
<script>
    function load() {
        $('#my_load').show();
        //return location.href = "<?= \yii\helpers\Url::to('load')?>";
    }
</script>
<div class="source-agregate-index">

    <p>
    <div  style="display: inline-block;">
        <?= Html::a('Load new data', ['load'], ['class' => 'btn btn-danger', 'id' => 'btnload', 'onClick'=>'load()']) ?>
    </div>
    <div   style="display: inline-block;">
        <?= Html::a('Create data manual', ['create'], ['class' => 'btn btn-success']) ?>
    </div>
    <div style="height: 25px; width: 25px; display: none; float: left; margin: 0px 20px;" id="my_load"><img src="http://i.stack.imgur.com/FhHRx.gif"></div>
    <div style="float: right; display: flex; flex-direction: column; justify-content:space-around; align-items: flex-end;
    padding: 10px; margin-left: 10px; height: 100%; width: 250px;">
        <?
        $records = $dataProvider->models;
        $recordCount = $dataProvider->count;
        $sumReturn = 0;
        $sumReturnAmsterdam = 0;
        $sumSpend = 0;
        foreach ( $records as $record ){
            $sumReturn += $record['return'];
            $sumReturnAmsterdam += $record['return_amsterdam'];
            $sumSpend += $record['spend'];
        }
        ?>
        <div style="color: #d9534f">Total spend: <strong><?= number_format($sumSpend, 2, '.', ' ') ?> $</strong></div>
        <div style="color: #00aa00">Total return: <strong><?= number_format($sumReturn, 2, '.', ' ') ?> $</strong></div>
        <div style="color: #00aa00">Total return (UTC+2): <strong><?= number_format($sumReturnAmsterdam, 2, '.', ' ') ?> $</strong></div>
    </div>
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
        'rowOptions' => function ($model) {
            if ($model->update_manual == 1) {
                return ['style' => 'background-color: #ffff88;'];
            }
            if ($model->update_manual == 2) {
                return ['style' => 'background-color: #99ff99;'];
            }
        },
        'filterModel' => $searchModel,

        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'date',
            'spend',
            'return',
            'return_amsterdam',
            'conversion',
            'date_create',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>

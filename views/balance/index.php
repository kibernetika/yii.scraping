<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\jui\DatePicker;
use app\models\table\Source;
use app\models\table\Users;

/* @var $this yii\web\View */
/* @var $searchModel app\models\table\BalanceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Balances';
$this->params['breadcrumbs'][] = $this->title;
?>

<script>
    function load() {
        $('#my_load').show();
        //return location.href = "<?= \yii\helpers\Url::to('load')?>";
    }
</script>

<div class="balance-index">
    <p>
        <div  style="display: inline-block;">
            <?= Html::a('Load new data', ['load'], ['class' => 'btn btn-danger', 'id' => 'btnload', 'onClick'=>'load()']) ?>
        </div>
        <div   style="display: inline-block;">
            <?= Html::a('Create data manual', ['create'], ['class' => 'btn btn-success']) ?>
        </div>
    <div style="height: 25px; width: 25px; display: none; float: left; margin: 0px 20px;" id="my_load"><img src="http://i.stack.imgur.com/FhHRx.gif"></div>
    <div style="float: right; display: flex; flex-direction: column; justify-content:space-around; align-items: flex-end;
    padding: 10px; margin-left: 10px; height: 100%; width: 220px; ">
        <?
        $records = $dataProvider->models;
        $recordCount = $dataProvider->count;
        $sumBalance = 0;
        foreach ( $records as $record ){
            $sumBalance += $record['money'];
        }
		if ($recordCount == 0){
			$averageBalance = 0;
		}else{
			$averageBalance = $sumBalance / $recordCount;
		}
        ?>
        <div style="color: #d9534f">Total on page: <strong><?= number_format($sumBalance, 2, '.', ' ') ?> $</strong></div>
        <p></p>
        <div style="color: #00aa00">Average on page: <strong><?= number_format($averageBalance, 2, '.', ' ') ?> $</strong></div>
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
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'source',
                'value' => 'source.name',
                'filter' => Html::activeDropDownList($searchModel, 'id_source', ArrayHelper::map(
                    Source::find()->where('type=1')->orderBy(['name' => SORT_ASC])->asArray()->all(), 'id', 'name'
                ),['class'=>'form-control','prompt' => 'All source']),
            ],
            [
                'attribute' => 'user',
                'value' => 'user.name',
                'filter' => Html::activeDropDownList($searchModel, 'id_user', ArrayHelper::map(
                    Users::find()->orderBy(['name' => SORT_ASC])->asArray()->all(), 'id', 'name'
                ),['class'=>'form-control','prompt' => 'All user']),
            ],
            'date',
            'money',
            'date_create',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>

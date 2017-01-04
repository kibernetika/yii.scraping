<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\table\SourceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Sources';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="source-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create source', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'name',
            'url:url',
            'myType',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>

<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use app\models\table\Source;
use app\models\table\Users;

/* @var $this yii\web\View */
/* @var $searchModel app\models\table\AccountSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Accounts';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="account-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create account', ['create'], ['class' => 'btn btn-success']) ?>
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
                    Source::find()->orderBy(['name' => SORT_ASC])->asArray()->all(), 'id', 'name'
                ),['class'=>'form-control','prompt' => 'All source']),
            ],
            [
                'attribute' => 'users',
                'value' => 'users.name',
                'filter' => Html::activeDropDownList($searchModel, 'id_user', ArrayHelper::map(
                    Users::find()->orderBy(['name' => SORT_ASC])->asArray()->all(), 'id', 'name'
                ),['class'=>'form-control','prompt' => 'All user']),
            ],
            'login',
            'pass',
             'api_first_key',
             'api_second_key',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>

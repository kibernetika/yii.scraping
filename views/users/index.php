<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\table\UsersSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Users';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="users-index">

<!--    <div id="search-form-my" style="display: none">-->
<!--        -->
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
<!--    </div>-->


    <p>
        <?= Html::a('Create user', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

//            'id',
            'username',
//            'password',
//            'authKey',
//            'accessToken',
             'name',
             'description',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>

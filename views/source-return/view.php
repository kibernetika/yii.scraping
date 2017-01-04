<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\table\SourceReturn */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Source Returns', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="source-return-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [                      // the owner name of the model
                'label' => 'Source',
                'value' => $model->source->name,
            ],
            [                      // the owner name of the model
                'label' => 'User',
                'value' => $model->user->name,
            ],
            'date',
            'return',
            'date_create',
        ],
    ]) ?>

</div>

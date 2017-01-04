<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\table\SourceAgregate */

$this->title = 'Update Source aggregate: ';
$this->params['breadcrumbs'][] = ['label' => 'Source Aggregates', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="source-agregate-update" style="display: flex; flex-direction: column; justify-content: center; align-items: center;">

    <h2><?= Html::encode($this->title) ?></h2>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

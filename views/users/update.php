<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\table\Users */

$this->title = 'Update User: ';
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="users-update" style="display: flex; flex-direction: column; justify-content: center; align-items: center;">

    <h2><?= Html::encode($this->title) ?></h2>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

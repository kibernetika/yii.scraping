<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\table\Balance */

$this->title = 'Update Balance: ';
$this->params['breadcrumbs'][] = ['label' => 'Balances', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="balance-update" style="display: flex; flex-direction: column; justify-content: center; align-items: center;">

    <h2><?= Html::encode($this->title) ?></h2>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

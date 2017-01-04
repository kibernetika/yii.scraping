<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\table\AccountSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="account-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

<!--    --><?//= $form->field($model, 'id') ?>

<!--    --><?//= $form->field($model, 'id_source') ?>


    <?= $form->field($model, 'login') ?>

    <?= $form->field($model, 'pass') ?>

<!--    --><?//= $form->field($model, 'id_user') ?>

    <?php $form->field($model, 'api_first_key') ?>

    <?php $form->field($model, 'api_second_key') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

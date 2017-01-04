<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\table\SourceAgregateSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="source-agregate-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
<!---->
<!--    --><?//= $form->field($model, 'id') ?>
<!---->
<!--    --><?//= $form->field($model, 'id_source') ?>

    <?= $form->field($model, 'date') ?>

    <?= $form->field($model, 'date_create') ?>

    <?= $form->field($model, 'spend') ?>

    <?php  echo $form->field($model, 'return') ?>

    <?php  echo $form->field($model, 'spend_amsterdam') ?>

    <?php  echo $form->field($model, 'return_amsterdam') ?>

    <?php  echo $form->field($model, 'conversion') ?>

    <?php // echo $form->field($model, 'id_user') ?>

    <?php // echo $form->field($model, 'update_manual') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

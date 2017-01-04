<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\table\UsersSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="users-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

<!--    --><?//= $form->field($model, 'id') ?>

    <?= $form->field($model, 'username') ?>

<!--    --><?//= $form->field($model, 'password') ?>
<!---->
<!--    --><?//= $form->field($model, 'authKey') ?>
<!---->
<!--    --><?//= $form->field($model, 'accessToken') ?>

    <?php  echo $form->field($model, 'name') ?>

    <?php  echo $form->field($model, 'description') ?>

<!--    --><?php // echo $form->field($model, 'photo') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

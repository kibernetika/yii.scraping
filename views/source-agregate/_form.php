<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\jui\DatePicker;

/* @var $this yii\web\View */
/* @var $model app\models\table\SourceAgregate */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="source-agregate-form" style="width: 300px;">

    <?php $form = ActiveForm::begin(); ?>

    <p><strong>Date</strong></p>
    <? if( is_null($model->date) )
        $model->date = new DateTime('now');
    echo DatePicker::widget([
        'model' => $model,
        'attribute' => 'date',
        'dateFormat' => 'yyyy-MM-dd',
    ]);
    echo '<p></p>' ?>

    <?= $form->field($model, 'spend')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'return')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'return_amsterdam')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'conversion')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

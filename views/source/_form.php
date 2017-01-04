<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\table\Source */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="source-form" style="width: 300px;">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'url')->textInput(['maxlength' => true]) ?>

    <? $items = [
        ['id' => '3', 'name' => 'aggregate'],
        ['id' => '2', 'name' => 'return'],
        ['id' => '1', 'name' => 'spend'],
    ];
    $source_items = ArrayHelper::map($items, 'id', 'name');
    $params = ['prompt' => 'Select type'];
    echo $form->field($model, 'type')->dropDownList($source_items, $params);?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

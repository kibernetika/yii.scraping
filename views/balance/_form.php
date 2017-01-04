<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\table\Source;
use app\models\table\Users;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\jui\DatePicker;

/* @var $this yii\web\View */
/* @var $model app\models\table\Balance */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="balance-form" style="width: 300px;">

    <?php $form = ActiveForm::begin(); ?>

    <?
    $source = Source::find()->where('type=1')->orderBy(['name' => SORT_ASC])->all();
    $source_items = ArrayHelper::map($source,'id','name');
    $params = ['prompt' => 'Select source'];
    echo $form->field($model, 'id_source')->dropDownList($source_items, $params);?>

    <?
    $user = Users::find()->orderBy(['name' => SORT_ASC])->all();
    $user_items = ArrayHelper::map($user,'id','name');
    $params = ['prompt' => 'Select user'];
    echo $form->field($model, 'id_user')->dropDownList($user_items, $params);?>

    <p><strong>Date</strong></p>
    <? if( is_null($model->date) ) $model->date = new DateTime('now');?>
    <?= DatePicker::widget([
        'model' => $model,
        'attribute' => 'date',
        'dateFormat' => 'yyyy-MM-dd',
    ]) ?>
    <p></p>

    <?= $form->field($model, 'money')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

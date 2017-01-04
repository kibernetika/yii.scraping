<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\table\Users;
use app\models\table\Source;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\table\Account */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="account-form" style="width: 300px;">

    <?php $form = ActiveForm::begin(); ?>

    <?
    $source = Source::find()->orderBy(['name' => SORT_ASC])->all();
    $source_items = ArrayHelper::map($source,'id','name');
    $params = ['prompt' => 'Select source'];
    echo $form->field($model, 'id_source')->dropDownList($source_items, $params);?>

    <?
    $user = Users::find()->orderBy(['name' => SORT_ASC])->all();
    $user_items = ArrayHelper::map($user,'id','name');
    $params = ['prompt' => 'Select user'];
    echo $form->field($model, 'id_user')->dropDownList($user_items, $params);?>

    <?= $form->field($model, 'login')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'pass')->passwordInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'api_first_key')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'api_second_key')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

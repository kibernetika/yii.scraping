<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\table\Users */

$this->title = 'Create User';
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="users-create" style="display: flex; flex-direction: column; justify-content: center; align-items: center;">

    <h2><?= Html::encode($this->title) ?></h2>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

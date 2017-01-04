<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\table\SourceReturn */

$this->title = 'Create Source return';
$this->params['breadcrumbs'][] = ['label' => 'Source Returns', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="source-return-create" style="display: flex; flex-direction: column; justify-content: center; align-items: center;">

    <h2><?= Html::encode($this->title) ?></h2>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

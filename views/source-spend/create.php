<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\table\SourceSpend */

$this->title = 'Create Source spend';
$this->params['breadcrumbs'][] = ['label' => 'Source Spends', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="source-spend-create" style="display: flex; flex-direction: column; justify-content: center; align-items: center;">

    <h2><?= Html::encode($this->title) ?></h2>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

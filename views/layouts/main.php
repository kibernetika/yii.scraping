<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => 'Data parser',
        'brandUrl' => '/source-agregate/index',
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
    if ( Yii::$app->user->isGuest ){
        $items = [];
    }else{
        $items = [
			['label' => 'Master', 'url' => ['/site/index']],
            ['label' => '|'],
            ['label' => 'Thrive', 'url' => ['/source-agregate/index']],
            ['label' => 'Spend', 'url' => ['/source-spend/index']],
            ['label' => 'Return', 'url' => ['/source-return/index']],
            ['label' => '|'],
            ['label' => 'Balances', 'url' => ['/balance/index']],
            ['label' => 'Sources', 'url' => ['/source/index']],
            ['label' => 'Accounts', 'url' => ['/account/index']],
            ['label' => 'Users', 'url' => ['/users/index']],
            ['label' => '|'],
            '<li>'
            . Html::beginForm(['/site/logout'], 'post', ['class' => 'navbar-form'])
            . Html::submitButton(
                'Logout (' . Yii::$app->user->identity->username . ')',
                ['class' => 'btn btn-link']
            )
            . Html::endForm()
            . '</li>'];
    }
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' =>
            $items
        ,
    ]);
    NavBar::end();
    ?>

    <div class="container" style="width: 100%">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
            <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; Data parser <?= date('Y') ?></p>

        <p class="pull-right"><?= Yii::powered() ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>

<?php

namespace app\commands;

use app\models\parser\ParserAgregate;
use app\models\parser\ParserNetwork;
use app\models\parser\ParserSource;
use app\models\table\Users;

/**
 * autoload data controller
 */
class LoadController extends \yii\console\Controller {

    public function actionIndex() {
        $users = Users::find()->all();
        foreach ($users as $user) {
            $id = $user->getAttribute('id');
            ParserNetwork::loadNewData($id);
            ParserSource::load($id);
            ParserAgregate::loadNewDataAgregate($id);
        }
    }

}


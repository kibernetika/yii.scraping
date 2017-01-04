<?php

namespace app\models\table;

use Yii;

/**
 * This is the model class for table "account".
 *
 * @property integer $id
 * @property integer $id_source
 * @property string $login
 * @property string $pass
 * @property integer $id_user
 * @property string $api_first_key
 * @property string $api_second_key
 */
class Account extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'account';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_source', 'login', 'pass'], 'required'],
            [['id_source', 'id_user'], 'integer'],
            [['login', 'pass'], 'string', 'max' => 50],
            [['api_first_key', 'api_second_key'], 'string', 'max' => 250],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_source' => 'Source',
            'source' =>  \Yii::t("app", "Source"),
            'login' => 'Login',
            'pass' => 'Pass',
            'id_user' => 'User',
            'users' =>  \Yii::t("app", "Users"),
            'api_first_key' => 'Api First Key',
            'api_second_key' => 'Api Second Key',
        ];
    }

    /**
     * @inheritdoc
     * @return AccountQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new AccountQuery(get_called_class());
    }

    public function getSource()
    {
        return $this->hasOne(Source::className(),
            ['id' => 'id_source']);
    }

    public function setSource()
    {
        return Source::findOne($this->source)->id;
    }

    public function getUsers()
    {
        return $this->hasOne(Users::className(),
            ['id' => 'id_user']);
    }

    public function setUsers()
    {
        return Users::findOne($this->users)->id;
    }
}

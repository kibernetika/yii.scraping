<?php

namespace app\models\table;

use Yii;

/**
 * This is the model class for table "balance".
 *
 * @property integer $id
 * @property integer $id_source
 * @property string $date
 * @property string $date_create
 * @property string $money
 */
class Balance extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'balance';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_source', 'id_user','money'], 'required'],
            [['id_source', 'id_user'], 'integer'],
            [['date'], 'safe'],
            [['user'], 'safe'],
            [['source'], 'safe'],
            [['money'], 'number'],
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
            'id_user' => 'User',
            'source' =>  \Yii::t("app", "Source"),
            'user' =>  \Yii::t("app", "User"),
            'date' => 'Date',
            'date_create' => 'Date create',
            'money' => 'Money',
        ];
    }

    public function beforeSave($insert)
    {
        if ( $insert ) {
            $date = new \DateTime();
            $this->date_create = $date->format('Y-m-d H:i:s');
        }
        return parent::beforeSave($insert);
    }

    /**
     * @inheritdoc
     * @return BalanceQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new BalanceQuery(get_called_class());
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

    public function getUser()
    {
        return $this->hasOne(Users::className(),
            ['id' => 'id_user']);
    }

    public function setUser()
    {
        return Users::findOne($this->user)->id;
    }
}

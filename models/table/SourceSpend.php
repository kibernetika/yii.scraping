<?php

namespace app\models\table;

use Yii;

/**
 * This is the model class for table "source_spend".
 *
 * @property integer $id
 * @property integer $id_source
 * @property string $date
 * @property string $date_create
 * @property string $spend
 * @property integer $conversion
 * @property integer $id_user
 */
class SourceSpend extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'source_spend';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_source', 'date'], 'required'],
            [['id_source', 'conversion', 'id_user'], 'integer'],
            [['date', 'date_create', 'source', 'user'], 'safe'],
            [['spend'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_source' => 'Id Source',
            'source' =>  \Yii::t("app", "Source"),
            'user' =>  \Yii::t("app", "User"),
            'date' => 'Date',
            'date_create' => 'Date Create',
            'spend' => 'Spend',
            'conversion' => 'Conversion',
            'id_user' => 'Id User',
        ];
    }

    public function beforeSave($insert)
    {
        if ( $insert ) {
            $date = new \DateTime();
            $this->date_create = $date->format('Y-m-d H:i:s');
        }elseif( $this->update_manual == 1 ){
            $this->update_manual = 2;
        }
        return parent::beforeSave($insert);
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

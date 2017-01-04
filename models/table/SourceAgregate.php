<?php

namespace app\models\table;

use Yii;

/**
 * This is the model class for table "source_agregate".
 *
 * @property integer $id
 * @property integer $id_source
 * @property string $date
 * @property string $date_create
 * @property string $spend
 * @property string $return
 * @property string $return_amsterdam
 * @property integer $conversion
 * @property integer $id_user
 * @property integer $update_manual
 */
class SourceAgregate extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'source_agregate';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_source', 'conversion', 'id_user', 'update_manual'], 'integer'],
            [['date', 'spend', 'return', 'return_amsterdam', 'conversion'], 'required'],
            [['date', 'date_create', 'source'], 'safe'],
            [['spend', 'return', 'return_amsterdam'], 'number'],
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
            'date' => 'Date',
            'date_create' => 'Date Create',
            'spend' => 'Spend',
            'return' => 'Return',
            'return_amsterdam' => 'Return (UTC+2)',
            'conversion' => 'Conversion',
            'id_user' => 'Id User',
            'update_manual' => 'Update Manual',
        ];
    }

    /**
     * @inheritdoc
     * @return SourceAgregateQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new SourceAgregateQuery(get_called_class());
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

    public function getSource()
    {
        return $this->hasOne(Source::className(),
            ['id' => 'id_source']);
    }

    public function setSource()
    {
        return Source::findOne($this->source)->id;
    }
}

<?php

namespace app\models\table;

use Yii;

/**
 * This is the model class for table "source".
 *
 * @property integer $id
 * @property string $name
 * @property string $url
 * @property integer $type
 * @property integer $need_manual
 */
class Source extends \yii\db\ActiveRecord
{
    private $myType;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'source';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'url', 'type'], 'required'],
            [['type', 'need_manual'], 'integer'],
            [['name'], 'string', 'max' => 50],
            [['url'], 'string', 'max' => 250],
        ];
    }

    public function beforeSave($insert)
    {
        if ( $insert ) {
            $this->need_manual = 1;
        }
        return parent::beforeSave($insert);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'url' => 'Url',
            'type' => 'Type',
            'need_manual' => 'Need Manual',
            'myType' => 'Type'
        ];
    }

    /**
     * @return string
     */
    public function getMyType()
    {
        switch($this->type){
            case 1:
                $this->myType = 'spend';
                break;
            case 2:
                $this->myType  = 'return';
                break;
            case 3:
                $this->myType  = 'aggregate';
                break;
            default: $this->myType  = 'unknown';
        }
        return $this->myType ;
    }

    /**
     * @param string $type
     */
    public function setMyType($type)
    {
        switch($type){
            case 'spend':
                $this->type = 1;
                break;
            case 'return':
                $this->type = 2;
                break;
            case 'aggregate':
                $this->type = 3;
                break;
            default: $this->type = 0;
        }
    }
}

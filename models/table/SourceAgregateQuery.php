<?php

namespace app\models\table;

/**
 * This is the ActiveQuery class for [[SourceAgregate]].
 *
 * @see SourceAgregate
 */
class SourceAgregateQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return SourceAgregate[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return SourceAgregate|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}

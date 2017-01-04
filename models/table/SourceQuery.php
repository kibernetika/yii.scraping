<?php

namespace app\models\table;

/**
 * This is the ActiveQuery class for [[Source]].
 *
 * @see Source
 */
class SourceQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return Source[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Source|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}

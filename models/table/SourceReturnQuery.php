<?php

namespace app\models\table;

/**
 * This is the ActiveQuery class for [[SourceReturn]].
 *
 * @see SourceReturn
 */
class SourceReturnQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return SourceReturn[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return SourceReturn|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}

<?php

namespace app\models\table;

/**
 * This is the ActiveQuery class for [[SourceSpend]].
 *
 * @see SourceSpend
 */
class AccountQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return SourceSpend[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return SourceSpend|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}

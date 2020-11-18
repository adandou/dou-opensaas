<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "auth_account_attr".
 *
 * @property int $id
 * @property int $auth_account_id
 * @property int $type
 * @property string $type_value
 */
class AuthAccountAttr extends \yii\db\ActiveRecord
{
    const TYPE_DOUYIN_OPEN_ID = 1;
    const TYPE_DOUYIN_UNIONID = 2;
    const TYPE_DOUYIN_ACCESS_TOKEN = 3;
    const TYPE_DOUYIN_EXPIRES = 4;
    const TYPE_DOUYIN_REFRESH_TOKEN = 5;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'auth_account_attr';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['auth_account_id', 'type', 'type_value'], 'required'],
            [['auth_account_id', 'type'], 'integer'],
            [['type_value'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'auth_account_id' => 'Auth Account ID',
            'type' => 'Type',
            'type_value' => 'Type Value',
        ];
    }
}

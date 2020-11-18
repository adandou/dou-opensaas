<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "auth_app_attr".
 *
 * @property int $id
 * @property int $auth_app_id
 * @property int $type
 * @property string $type_value
 */
class AuthAppAttr extends \yii\db\ActiveRecord
{
    const TYPE_DOUYIN_APP_KEY = 1;
    const TYPE_DOUYIN_APP_SECRET = 2;

    const TYPE_XIGUA_APP_KEY = 11;
    const TYPE_XIGUA_APP_SECRET = 12;

    const TYPE_TOUTIAO_APP_KEY = 21;
    const TYPE_TOUTIAO_APP_SECRET = 22;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'auth_app_attr';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['auth_app_id', 'type', 'type_value'], 'required'],
            [['auth_app_id', 'type'], 'integer'],
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
            'auth_app_id' => 'Auth App ID',
            'type' => 'Type',
            'type_value' => 'Type Value',
        ];
    }
}

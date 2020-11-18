<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "auth_user_fans".
 *
 * @property int $id
 * @property int $auth_uid 抖音用户ID
 * @property int $fans_uid 粉丝用户ID
 * @property int $is_follow 是否也关注了粉丝
 */
class AuthUserFans extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'auth_user_fans';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['auth_uid', 'fans_uid', 'is_follow'], 'required'],
            [['auth_uid', 'fans_uid', 'is_follow'], 'integer'],
            [['auth_uid', 'fans_uid'], 'unique', 'targetAttribute' => ['auth_uid', 'fans_uid']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'auth_uid' => 'Auth Uid',
            'fans_uid' => 'Fans Uid',
            'is_follow' => 'Is Follow',
        ];
    }
}

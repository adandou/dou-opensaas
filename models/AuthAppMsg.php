<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "auth_app_msg".
 *
 * @property int $id
 * @property int $auth_app_id
 * @property string|null $event 事件类型
 * @property string $content
 * @property string $ctime
 */
class AuthAppMsg extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'auth_app_msg';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['auth_app_id'], 'integer'],
            [['content'], 'required'],
            [['content'], 'string'],
            [['ctime'], 'safe'],
            [['event'], 'string', 'max' => 255],
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
            'event' => 'Event',
            'content' => 'Content',
            'ctime' => 'Ctime',
        ];
    }
}

<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "auth_app_request".
 *
 * @property int $id
 * @property string $request_url
 * @property string|null $request_data
 * @property string $result
 * @property string $ctime
 */
class AuthAppRequest extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'auth_app_request';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['request_url', 'result'], 'required'],
            [['request_data', 'result'], 'string'],
            [['ctime'], 'safe'],
            [['request_url'], 'string', 'max' => 1000],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'request_url' => 'Request Url',
            'request_data' => 'Request Data',
            'result' => 'Result',
            'ctime' => 'Ctime',
        ];
    }
}

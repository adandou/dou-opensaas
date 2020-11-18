<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "auth_user".
 *
 * @property int $id
 * @property int $auth_app_id 应用ID
 * @property int $auth_account_id
 * @property string $open_id
 * @property string|null $union_id
 * @property string $nickname
 * @property string $avatar
 * @property int $gender
 * @property string|null $city
 * @property string|null $province
 * @property string|null $country
 * @property int $role
 * @property int|null $utime
 */
class AuthUser extends \yii\db\ActiveRecord
{
    const ROLE_ARR = [
        0 => '普通用户',
        1 => '抖音普通企业号',
        2 => '抖音认证企业号',
        3 => '抖音品牌企业号',
    ];
    public function showAvatar(){
        $src = $this->avatar;
        if(empty($src)){
            $src = '/static/image/moshengren.jpeg';
        }
        return '<img src="'.$src.'" width="60"/>';
    }
    //更新一个用户信息
    public function updateAuthUserInfo(){
        $this->authAccount->updateAuthUserInfo($this);
    }
    //关联账号
    public function getAuthAccount(){
        return $this->hasOne(AuthAccount::className(),['id'=>'auth_account_id']);
    }
    //关联应用
    public function getAuthApp(){
        return $this->hasOne(AuthApp::className(),['id'=>'auth_app_id']);
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'auth_user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['auth_app_id', 'auth_account_id', 'gender', 'role', 'utime'], 'integer'],
            [['open_id', 'nickname'], 'required'],
            [['open_id', 'union_id', 'nickname', 'avatar', 'city', 'province', 'country'], 'string', 'max' => 255],
            [['open_id'], 'unique'],
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
            'auth_account_id' => 'Auth Account ID',
            'open_id' => 'Open ID',
            'union_id' => 'Union ID',
            'nickname' => '昵称',
            'avatar' => '头像',
            'gender' => '行呗',
            'city' => '城市',
            'province' => '地区',
            'country' => '国家',
            'role' => 'Role',
            'utime' => 'Utime',
        ];
    }
}

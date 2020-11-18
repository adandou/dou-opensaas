<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "auth_app".
 *
 * @property int $id
 * @property string $title
 * @property string|null $logo
 * @property int $state
 */
class AuthApp extends \yii\db\ActiveRecord
{
    static private $apps = [
        1 => 'app\\models\\auth_app\\AuthAppDouyin',
        2 => 'app\\models\\auth_app\\AuthAppXigua',
        3 => 'app\\models\\auth_app\\AuthAppToutiao',
    ];
    protected $attr_arr;
    public function initToken($code){

    }
    public function getAuthUserInfo(AuthAccount $obj){

    }
    public function getMsgObj($data){}
    //获取app对象
    public function getAppObj():AuthApp {
        $class = self::$apps[$this->id];
        $obj = new $class();
        self::populateRecord($obj,$this->getAttributes());
        return $obj;
    }

    public function getAttr($type){
        if(!empty($this->attr_arr)){
            return $this->attr_arr[$type]??'';
        }
        foreach ($this->attrs as $obj){
            $this->attr_arr[$obj->type]=$obj->type_value;
        }
        return $this->attr_arr[$type]??'';
    }
    //关联扩展
    public function getAttrs(){
        return $this->hasMany(AuthAppAttr::className(),['auth_app_id'=>'id']);
    }
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'auth_app';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['logo'], 'string'],
            [['state'], 'integer'],
            [['title'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'logo' => 'Logo',
            'state' => 'State',
        ];
    }
}

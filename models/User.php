<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $realname
 * @property int $id_no
 * @property string $birthday
 * @property int $phone
 * @property int $photo
 * @property string $mini_openid
 * @property string $unionid
 * @property string $nickname
 * @property int $sex
 * @property string $country
 * @property string $province
 * @property string $city
 * @property string $head_url
 * @property int $ctime
 */
class User extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{
    private static $users = [
        '100' => [
            'id' => '100',
            'username' => 'xxx',
            'password' => 'xxx',
            'authKey' => 'xxx',
            'accessToken' => '100-token',
        ],
        '101' => [
            'id' => '101',
            'username' => 'xxx',
            'password' => 'xxx',
            'authKey' => 'xxx',
            'accessToken' => '101-token',
        ],
    ];
    public $username;
    public $password = 'xxx';
    public $authKey;
    public $accessToken;
    static public function getObjByMiniOpenid($openid){
        $obj = self::findOne(['mini_openid'=>$openid]);
        if(!$obj){
            $obj = new User();
            $obj->ctime=time();
            $obj->mini_openid=$openid;
            if(!$obj->save()) throw new \Exception(json_encode($obj->getErrors(),JSON_UNESCAPED_UNICODE),1);
        }
        return $obj;
    }
    public function getListData(){
        return [
            'id'=>$this->id,
            'realname'=>$this->realname,
            'photo_url'=>$this->photoUrl(),
        ];
    }
    //获取加密手机号
    public function getEncryptPhone(){
        return $this->phone;
        if(empty($phone)) return ;
        $phone[3] = '*';
        $phone[4] = '*';
        $phone[5] = '*';
        $phone[6] = '*';
    }
    //获取logo
    public function photoUrl(){
        return $this->photoObj?$this->photoObj->getFileUrl():'/images/default_team.png';
    }
    //关联
    public function getPhotoObj(){
        return $this->hasOne(UploadFile::className(),['id'=>'photo']);
    }
    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return isset(self::$users[$id]) ? new static(self::$users[$id]) : null;
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        foreach (self::$users as $user) {
            if ($user['accessToken'] === $token) {
                return new static($user);
            }
        }

        return null;
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        foreach (self::$users as $user) {
            if (strcasecmp($user['username'], $username) === 0) {
                return new static($user);
            }
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->authKey;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return $this->password === $password;
    }    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_no', 'phone', 'photo', 'sex', 'ctime'], 'integer'],
            [['birthday'], 'safe'],
            [['ctime'], 'required'],
            [['realname', 'nickname', 'country', 'province', 'city', 'head_url'], 'string', 'max' => 255],
            [['mini_openid'], 'string', 'max' => 28],
            [['unionid'], 'string', 'max' => 50],
            [['mini_openid'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'realname' => 'Realname',
            'id_no' => 'Id No',
            'birthday' => 'Birthday',
            'phone' => 'Phone',
            'photo' => 'Photo',
            'mini_openid' => 'Mini Openid',
            'unionid' => 'Unionid',
            'nickname' => 'Nickname',
            'sex' => 'Sex',
            'country' => 'Country',
            'province' => 'Province',
            'city' => 'City',
            'head_url' => 'Head Url',
            'ctime' => 'Ctime',
        ];
    }
}

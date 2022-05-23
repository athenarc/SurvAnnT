<?php

namespace app\models;
use webvimark\modules\UserManagement\models\User;
use Yii;

/**
 * This is the model class for table "badges".
 *
 * @property int $id
 * @property string $name
 * @property int|null $ownerid
 * @property string $created
 * @property int|null $allowusers
 * @property resource $image
 * @property string $type
 * @property int $size
 *
 * @property User $owner0
 * @property Surveytobadges[] $surveytobadges
 * @property Usertobadges[] $usertobadges
 */
class Badges extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    /*
     * @var UploadedFile[]
    */
     
    public $imageFiles;

    public static function tableName()
    {
        return 'badges';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name','type', 'size'], 'required'],
            [['ownerid', 'allowusers'], 'integer'],
            [['created'], 'safe'],
            [['name'], 'string', 'max' => 255],
            [['type'], 'string', 'max' => 20],
            [['ownerid'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['ownerid' => 'id']],
            [['imageFiles'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg', 'maxFiles' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'ownerid' => 'Ownerid',
            'created' => 'Created',
            'allowusers' => 'Allowusers',
            'image' => 'Image',
            'type' => 'Image Type',
            'size' => 'Image Size'
        ];
    }

    public function upload()
    {
        // print_r($this->image->baseName);
        // echo "<br><br> validation: ";

        // print_r($this->validate());
        // echo "<br><br>";

        if ( $this->validate() && ! empty($this->image) ) {
            $this->image->saveAs( Yii::$app->params['dir-badges'] . $this->image->baseName . '.' . $this->image->extension);
            return true;
        } else {
            echo "Errors: ";
            print_r($this->getErrors());
            echo "<br><br> Empty: ";
            print_r(!empty($this->image));
            echo "<br><br> Validate: ";
            print_r($this->validate());
            echo "<br><br>";
            
            if ( $this->id == null ){
                $this->addError('image' , 'Image can not be blank.');
                return false;
            }else{
                $this->addError('image' , 'Image exists.');
                return true;
            }
        }
    }

    /**
     * Gets query for [[Owner0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOwner()
    {
        return $this->hasOne(User::className(), ['id' => 'ownerid']);
    }

    /**
     * Gets query for [[Surveytobadges]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSurveytobadges($surveyid = null)
    {   
        
        if ( $surveyid ){
            return $this->hasMany(Surveytobadges::className(), ['badgeid' => 'id']);
        }else{
            return $this->hasMany(Surveytobadges::className(), ['badgeid' => 'id']);
        }
        
    }

    /**
     * Gets query for [[Usertobadges]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsertobadges()
    {
        return $this->hasMany(Usertobadges::className(), ['badgeid' => 'id']);
    }
}

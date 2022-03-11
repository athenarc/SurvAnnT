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
            [['name'], 'required'],
            [['ownerid', 'allowusers'], 'integer'],
            [['created'], 'safe'],
            [['name'], 'string', 'max' => 255],
            [['ownerid'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['ownerid' => 'id']],
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
        ];
    }

    public function upload()
    {

        if ( $this->validate() && ! empty($this->image) ) {
            $this->image->saveAs( Yii::$app->params['dir-badges'] . $this->image->baseName . '.' . $this->image->extension);
            return true;
        } else {
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
    public function getOwner0()
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

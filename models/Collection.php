<?php

namespace app\models;
use webvimark\modules\UserManagement\models\User;
use Yii;

/**
 * This is the model class for table "collection".
 *
 * @property int $id
 * @property string $name
 * @property int|null $userid
 * @property string|null $about
 * @property int|null $allowusers
 * @property string $created
 *
 * @property Resources[] $resources
 * @property Rate[] $rates
 * @property Surveytocollections[] $surveytocollections
 * @property User $user
 */
class Collection extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'collection';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['userid', 'allowusers'], 'integer'],
            [['about'], 'string'],
            [['created'], 'safe'],
            [['name'], 'string', 'max' => 255],
            [['userid'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['userid' => 'id']],
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
            'userid' => 'Userid',
            'about' => 'Description',
            'allowusers' => 'Public',
            'created' => 'Created',
        ];
    }

    /**
     * Gets query for [[Resources]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getResources()
    {
        return $this->hasMany(Resources::className(), ['collectionid' => 'id']);
    }

    /**
     * Gets query for [[Surveytocollections]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSurveytocollections()
    {
        return $this->hasMany(Surveytocollections::className(), ['collectionid' => 'id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'userid']);
    }

    /**
     * Gets query for [[Rates]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRates()
    {
        return $this->hasMany(Rate::className(), ['collectionid' => 'id']);
    }
}

<?php

namespace app\models;
use webvimark\modules\UserManagement\models\User;
use Yii;

/**
 * This is the model class for table "surveytobadges".
 *
 * @property int $id
 * @property int|null $surveyid
 * @property int|null $badgeid
 * @property int|null $ownerid
 * @property string $created
 * @property int $ratecondition
 * @property int $surveycondition
 *
 * @property Badges $badge
 * @property User $owner
 * @property Surveys $survey
 */
class Surveytobadges extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'surveytobadges';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['surveyid', 'badgeid', 'ownerid'], 'integer'],
            [['created', 'ratecondition', 'surveycondition'], 'safe'],
            [['ratecondition', 'surveycondition'], 'integer'],
            [['ratecondition', 'surveycondition'], 'default', 'value' => 0],
            [['ownerid'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['ownerid' => 'id']],
            [['badgeid'], 'exist', 'skipOnError' => true, 'targetClass' => Badges::className(), 'targetAttribute' => ['badgeid' => 'id']],
            [['surveyid'], 'exist', 'skipOnError' => true, 'targetClass' => Surveys::className(), 'targetAttribute' => ['surveyid' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'surveyid' => 'Surveyid',
            'badgeid' => 'Badgeid',
            'ownerid' => 'Ownerid',
            'created' => 'Created',
            'ratecondition' => 'Ratecondition',
            'surveycondition' => 'Surveycondition',
        ];
    }

    /**
     * Gets query for [[Badge]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBadge()
    {
        return $this->hasOne(Badges::className(), ['id' => 'badgeid']);
    }

    /**
     * Gets query for [[Owner]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOwner()
    {
        return $this->hasOne(User::className(), ['id' => 'ownerid']);
    }

    /**
     * Gets query for [[Survey]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSurvey()
    {
        return $this->hasOne(Surveys::className(), ['id' => 'surveyid']);
    }
}

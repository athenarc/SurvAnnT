<?php

namespace app\models;
use webvimark\modules\UserManagement\models\User;
use Yii;

/**
 * This is the model class for table "usertobadges".
 *
 * @property int $id
 * @property int|null $surveyid
 * @property int|null $badgeid
 * @property int|null $userid
 * @property string $created
 *
 * @property Badges $badge
 * @property Surveys $survey
 * @property User $user
 */
class Usertobadges extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'usertobadges';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['surveyid', 'badgeid', 'userid'], 'integer'],
            [['created'], 'safe'],
            [['userid'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['userid' => 'id']],
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
            'userid' => 'Userid',
            'created' => 'Created',
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
     * Gets query for [[Survey]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSurvey()
    {
        return $this->hasOne(Surveys::className(), ['id' => 'surveyid']);
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
}

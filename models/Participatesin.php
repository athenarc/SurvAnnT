<?php

namespace app\models;
use webvimark\modules\UserManagement\models\User;
use Yii;

/**
 * This is the model class for table "participatesin".
 *
 * @property int $id
 * @property int|null $surveyid
 * @property int|null $userid
 * @property int|null $owner
 * @property string $created
 * @property int $request
 * @property Surveys $survey
 * @property User $user
 */
class Participatesin extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'participatesin';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['surveyid', 'userid', 'owner'], 'integer'],
            [['created', 'request'], 'safe'],
            [['userid'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['userid' => 'id']],
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
            'userid' => 'Userid',
            'owner' => 'Owner',
            'created' => 'Created',
            'request' => 'Request',
        ];
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

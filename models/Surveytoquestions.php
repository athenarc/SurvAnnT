<?php

namespace app\models;
use webvimark\modules\UserManagement\models\User;
use Yii;

/**
 * This is the model class for table "surveytoquestions".
 *
 * @property int $id
 * @property int|null $surveyid
 * @property int|null $ownerid
 * @property int|null $questionid
 * @property string $created
 *
 * @property User $owner
 * @property Questions $question
 * @property Surveys $survey
 */
class Surveytoquestions extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'surveytoquestions';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['surveyid', 'ownerid', 'questionid'], 'integer'],
            [['created'], 'safe'],
            [['ownerid'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['ownerid' => 'id']],
            [['surveyid'], 'exist', 'skipOnError' => true, 'targetClass' => Surveys::className(), 'targetAttribute' => ['surveyid' => 'id']],
            [['questionid'], 'exist', 'skipOnError' => true, 'targetClass' => Questions::className(), 'targetAttribute' => ['questionid' => 'id']],
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
            'ownerid' => 'Ownerid',
            'questionid' => 'Questionid',
            'created' => 'Created',
            'allowusers' => 'Allowusers',
        ];
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
     * Gets query for [[Question]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQuestion()
    {
        return $this->hasOne(Questions::className(), ['id' => 'questionid']);
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

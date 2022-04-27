<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "rate".
 *
 * @property int $id
 * @property int $userid
 * @property int $surveyid
 * @property int $resourceid
 * @property int $questionid
 * @property int $collectionid
 * @property string|null $answer
 * @property string $answertype
 * @property string $created
 * @property int|null $time
 *
 * @property Collection $collection
 * @property Questions $question
 * @property Resources $resource
 * @property Surveys $survey
 * @property User $user
 */
class Rate2 extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'rate';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['userid', 'surveyid', 'resourceid', 'questionid', 'collectionid', 'answertype'], 'required'],
            [['userid', 'surveyid', 'resourceid', 'questionid', 'collectionid', 'time'], 'integer'],
            [['answer'], 'string'],
            [['created'], 'safe'],
            [['answertype'], 'string', 'max' => 20],
            [['userid'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['userid' => 'id']],
            [['surveyid'], 'exist', 'skipOnError' => true, 'targetClass' => Surveys::className(), 'targetAttribute' => ['surveyid' => 'id']],
            [['resourceid'], 'exist', 'skipOnError' => true, 'targetClass' => Resources::className(), 'targetAttribute' => ['resourceid' => 'id']],
            [['questionid'], 'exist', 'skipOnError' => true, 'targetClass' => Questions::className(), 'targetAttribute' => ['questionid' => 'id']],
            [['collectionid'], 'exist', 'skipOnError' => true, 'targetClass' => Collection::className(), 'targetAttribute' => ['collectionid' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'userid' => 'Userid',
            'surveyid' => 'Surveyid',
            'resourceid' => 'Resourceid',
            'questionid' => 'Questionid',
            'collectionid' => 'Collectionid',
            'answer' => 'Answer',
            'answertype' => 'Answertype',
            'created' => 'Created',
            'time' => 'Time',
        ];
    }

    /**
     * Gets query for [[Collection]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCollection()
    {
        return $this->hasOne(Collection::className(), ['id' => 'collectionid']);
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
     * Gets query for [[Resource]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getResource()
    {
        return $this->hasOne(Resources::className(), ['id' => 'resourceid']);
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

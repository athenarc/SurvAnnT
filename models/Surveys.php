<?php

namespace app\models;
use webvimark\modules\UserManagement\models\User;
use Yii;

/**
 * This is the model class for table "surveys".
 *
 * @property int $id
 * @property string $name
 * @property string $created
 * @property string|null $starts
 * @property string|null $ends
 * @property int $locked
 * @property string $about
 * @property int $minRespPerRes
 * @property int $maxRespPerRes
 * @property int $minResEv
 * @property int $maxResEv
 * @property string $fields
 *
 * @property Dataset[] $datasets
 * @property Participatesin[] $participatesins
 * @property Questions[] $questions
 * @property Surveytoquestions[] $surveytoquestions
 * @property Surveytoresources[] $surveytoresources
 */
class Surveys extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'surveys';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'about'], 'required'],
            [['created', 'starts', 'ends', 'fields'], 'safe'],
            ['ends', 'compare', 'compareAttribute' => 'starts', 'operator'=>'>','message' => 'Survey can not expire before it starts!'],
            [['locked', 'minRespPerRes', 'maxRespPerRes', 'minResEv', 'maxResEv'], 'integer'],
            [['about'], 'string'],
            [['name'], 'string', 'max' => 255],
            [['name'], 'unique'],
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
            'created' => 'Created',
            'starts' => 'Starts',
            'ends' => 'Ends',
            'locked' => 'Locked',
            'about' => 'About',
            'fields' => 'Fields',
            'minRespPerRes' => 'Minimum Responses Per Resource',
            'maxRespPerRes' => 'Maximum Responses Per Resource',
            'minResEv' => 'Minimum Resources Evaluated',
            'maxResEv' => 'Maximum Resources Evaluated'
        ];
    }

    /**
     * Gets query for [[Participatesins]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getParticipatesin()
    {
        return $this->hasMany(Participatesin::className(), ['surveyid' => 'id']);
    }

    // public function getOwner()
    // {
    //     return $this->hasMany(Participatesin::className(), ['surveyid' => 'id']);
    // }

    public function isOwner($userid)
    {
        return $this->hasMany(Participatesin::className(), ['surveyid' => 'id'])->where(['owner' => 1, 'userid' => $userid]);
    }

    public function getOwner()
    {
        return $this->hasMany(Participatesin::className(), ['id' => 'userid'])->select(['id', 'username'])->viaTable('participatesin', ['surveyid' => 'id'], function($query){
        $query->where(['owner' => 1]);
    })->asArray()->all();
    }

    public function getUser(){
        return $this->hasMany(User::className(), ['id' => 'userid'])->select(['id', 'username'])->viaTable('participatesin', ['surveyid' => 'id']);
    }

    public function getUserSurveys($userid = null)
    {
        ( $userid == null ) ? $surveys = $this->find()->asArray()->all() : $surveys = $this->find()->where(['userid' => $userid])->asArray()->all();
        return $surveys;
    }
        /**
     * Gets query for [[Surveytoquestions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSurveytoquestions()
    {
        return $this->hasMany(Surveytoquestions::className(), ['surveyid' => 'id']);
    }

    /**
     * Gets query for [[Surveytoresources]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSurveytoresources()
    {
        return $this->hasMany(Surveytoresources::className(), ['surveyid' => 'id']);
    }

    public function getResources()
    {
        return $this->hasMany(Resources::className(), ['id' => 'resourceid'])->viaTable('surveytoresources', ['surveyid' => 'id']);
    }
}

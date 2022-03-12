<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "questions".
 *
 * @property int $id
 * @property int|null $ownerid
 * @property string $created
 * @property string $question
 * @property string|null $tooltip
 * @property string|null $answer
 * @property string $answertype
 * @property string|null $answervalues
 * @property int|null $allowusers
 *
 * @property User $owner
 * @property Rate[] $rates
 * @property Surveytoquestions[] $surveytoquestions
 */
class Questions2 extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'questions';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ownerid', 'allowusers'], 'integer'],
            [['created'], 'safe'],
            [['question', 'answertype'], 'required'],
            [['answervalues'], 'string'],
            [['question', 'tooltip', 'answer'], 'string', 'max' => 255],
            [['answertype'], 'string', 'max' => 20],
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
            'ownerid' => 'Ownerid',
            'created' => 'Created',
            'question' => 'Question',
            'tooltip' => 'Tooltip',
            'answer' => 'Answer',
            'answertype' => 'Answertype',
            'answervalues' => 'Answervalues',
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
     * Gets query for [[Rates]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRates()
    {
        return $this->hasMany(Rate::className(), ['questionid' => 'id']);
    }

    /**
     * Gets query for [[Surveytoquestions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSurveytoquestions()
    {
        return $this->hasMany(Surveytoquestions::className(), ['questionid' => 'id']);
    }
}

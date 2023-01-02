<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "invitations".
 *
 * @property int $id
 * @property string $hash
 * @property int $surveyid
 * @property string $email
 * @property string $created
 *
 * @property Surveys $survey
 */
class Invitations extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'invitations';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['hash', 'surveyid', 'email'], 'required'],
            [['surveyid'], 'integer'],
            [['created'], 'safe'],
            [['hash', 'email'], 'string', 'max' => 255],
            [['hash'], 'unique'],
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
            'hash' => 'Hash',
            'surveyid' => 'Surveyid',
            'email' => 'Email',
            'created' => 'Created',
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

    public function email_send($surveyManager, $surveyName, $surveyDescription){
        if ( $surveyName != null ){
            $message = 'Hello! <br>You have been invited to participate in '.$surveyManager.'\'s survey <b>'.$surveyName.'</b> in <b>'.Yii::$app->params['title'].'</b>.<br>';
        }else{
            $message = 'Hello! <br>You have been invited to participate in '.$surveyManager.'\'s survey in <b>'.Yii::$app->params['title'].'</b>.<br>';
        }

        if ($surveyDescription != null){
            $message += 'Here is a brief summary of the survey: <br>'.$surveyDescription.'<br>';
        }

        $message += 'Please follow this <a href = "'.Yii::$app->params['invitation-url'].$this->hash.' ">link</a> to register!<br><br> Kind regards,<br>'.Yii::$app->params['title'].' team.';

        Yii::$app->mailer->compose()
        ->setFrom(Yii::$app->params['helpdesk-address'])
        ->setTo($this->email)
        ->setSubject('SurvAnnT Invitation')
        ->setTextBody('Welcome to '.Yii::$app->params['title'].'!!')
        ->setHtmlBody($message)
        ->send();
    }
}

<?php

namespace app\models;
use webvimark\modules\UserManagement\models\User;
use Yii;
use app\models\Badges;
use app\models\Surveys;

/**
 * This is the model class for table "leaderboard".
 *
 * @property int $id
 * @property int $userid
 * @property int $surveyid
 * @property int $points
 * @property string $created
 *
 * @property Surveys $survey
 * @property User $user
 */
class Leaderboard extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'leaderboard';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['userid', 'surveyid'], 'required'],
            [['userid', 'surveyid', 'points'], 'integer'],
            [['created'], 'safe'],
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
            'userid' => 'Userid',
            'surveyid' => 'Surveyid',
            'points' => 'Points',
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

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'userid']);
    }

    public function getAllLeaderboards($surveyid = null)
    {   
        if ( $surveyid ){
            $surveys[0] = Surveys::findOne($surveyid);
        }else{
            $surveys = Surveys::find()->where(['active' => 1 ])->all();
        }
        
        $survey_leaderboards = [];

        foreach ($surveys as $survey) {
            $survey_leaderboards[str_replace(" ", "_", $survey->name)] = [];
            $leaderboard = Leaderboard::find()->joinWith(['user'])->select(['leaderboard.*', 'user.username'])->where(['surveyid' => $survey->id])->orderBy(['points' => SORT_DESC])->all();
            foreach ($leaderboard as $key => $value) {

                $survey_leaderboards[str_replace(" ", "_", $survey->name)][$key]['username'] = $value->user->username;
                $survey_leaderboards[str_replace(" ", "_", $survey->name)][$key]['badge'] = '';
                foreach ( $value->user->getUsertobadges()->asArray()->all() as $badge){
                    
                    $badge_image = Badges::find()->select(['image'])->where(['id' => $badge['badgeid']])->one();
                    $survey_leaderboards[str_replace(" ", "_", $survey->name)][$key]['badge'] .= '<img width = "30" height = "30" id = "image-preview-'.$key.'" src="data:image/png;base64,'.base64_encode($badge_image['image']).'"/>&nbsp;';
                }
                $survey_leaderboards[str_replace(" ", "_", $survey->name)][$key]['points'] = $value->points;
                $survey_leaderboards[str_replace(" ", "_", $survey->name)][$key]['annotations'] = sizeof($value->user->getRates()->groupBy(['resourceid'])->asArray()->all());
            }
        }

        return $survey_leaderboards;
    }

    public function getTotalLeaderboard()
    {
        $total_leaderboard_q = Leaderboard::find()->joinWith(['user'])->select(['leaderboard.*', 'user.username'])->orderBy(['points' => SORT_DESC])->all();
        $total_leaderboard = [];
        foreach ($total_leaderboard_q as $key => $value) {

            $total_leaderboard[$key]['username'] = $value->user->username;
            $total_leaderboard[$key]['badge'] = '';
            foreach ( $value->user->getUsertobadges()->asArray()->all() as $badge){
                
                $badge_image = Badges::find()->select(['image'])->where(['id' => $badge['badgeid']])->one();
                $total_leaderboard[$key]['badge'] .= '<img width = "30" height = "30" id = "image-preview-'.$key.'" src="data:image/png;base64,'.base64_encode($badge_image['image']).'"/>&nbsp;';
            }
            $total_leaderboard[$key]['points'] = $value->points;
            $total_leaderboard[$key]['annotations'] = sizeof($value->user->getRates()->groupBy(['resourceid'])->asArray()->all());
        }

        return $total_leaderboard;
    }
}

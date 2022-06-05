<?php

namespace app\models;
use webvimark\modules\UserManagement\models\User;
use Yii;
use Yii\helpers\Html;
use app\models\CsvExport;

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
 * @property int $active
 * @property int $badgesused
 * @property int $completed
 * @property int $time
 * @property int $randomness
 * @property Invitations[] $invitations
 * @property Participatesin[] $participatesins
 * @property Rate[] $rates
 * @property Surveytobadges[] $surveytobadges
 * @property Surveytocollections[] $surveytocollections
 * @property Surveytoquestions[] $surveytoquestions
 * @property Surveytoresources[] $surveytoresources
 * @property Usertobadges[] $usertobadges
 */
class Surveys extends \yii\db\ActiveRecord
{
    public $rates_count;
    public $participants_count;
    public $resources_count;
    public $questions_count;
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
            [['created', 'starts', 'ends', 'fields', 'active', 'badgesused', 'completed'], 'safe'],
            ['ends', 'compare', 'compareAttribute' => 'starts', 'operator'=>'>','message' => 'Survey can not expire before it starts!'],
            [['locked', 'minRespPerRes', 'maxRespPerRes', 'minResEv', 'maxResEv', 'time', 'randomness'], 'integer'],
            ['maxRespPerRes', 'compare', 'compareAttribute' => 'minRespPerRes', 'operator'=>'>=','message' => 'Maximum evaluations per resource can not be smaller than Minimum!'],
            ['maxResEv', 'compare', 'compareAttribute' => 'minResEv', 'operator'=>'>=','message' => 'Maximum evaluations can not be smaller than Minimum!'],
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
            'name' => 'Campaign Id',
            'created' => 'Created',
            'starts' => 'Start Date',
            'ends' => 'End Date',
            'locked' => 'Availability',
            'about' => 'Campaign Description',
            'fields' => 'Research Fields',
            'minRespPerRes' => 'Minimum Evaluations Per Resource',
            'maxRespPerRes' => 'Maximum Evaluations Per Resource',
            'minResEv' => 'Minimum Resources Evaluated',
            'maxResEv' => 'Maximum Resources Evaluated',
            'active' => 'Active',
            'time' => 'Capture Response Times',
            'randomness' => 'Resource Selection Methodology',
        ];
    }

    public function createCsv($survey, $userid)
    {

        $rates = Rate::find()->select([1.' AS ResourceTitle', 'resourceid AS ResourceId', 'questions.id AS QuestionId', 'questions.question as Question', 'rate.answer AS Answer', 'questions.answertype as AnswerType', 'userid as User']);
        $rates->where(['surveyid' => $survey->id])->join('LEFT JOIN', 'questions', 'rate.questionid = questions.id');
        $rates = $rates->asArray()->all();
        $date = date('Y_m_d_H_i_s', time());
        $fname = Yii::$app->basePath. DIRECTORY_SEPARATOR . 'web' . DIRECTORY_SEPARATOR . 'downloads/statistics_'.$survey->name.'_'.$date.'.csv';

        $f = fopen( $fname, 'w');
        fputcsv($f, array($survey->name));
        fputcsv($f, array_values ( array_keys( $rates[0] ) ));
        $users = [];
        foreach ($rates as $key => $item) {
            $resource = Resources::findOne($item['ResourceId']);
            $item['ResourceTitle'] = $resource->title;
            if ( ! in_array( $item['User'], $users ) ){
                array_push($users, $item['User']);
                $userid = 'User_' . ( array_search($item['User'], $users) + 1 );
            }
            $item['User'] = $userid;
            fputcsv($f, $item);
        }        
        fclose($f);

        return \Yii::$app->response->sendFile($fname);
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
    //     return $this->hasMany(Participatesin::className(), ['id' => 'userid'])->select(['id', 'owner'])->viaTable('participatesin', ['surveyid' => 'id'], function($query){
    //     $query->where(['owner' => 1]);
    // })->createCommand()->getRawSql();
        $owner = $this->find()->joinWith(['participatesin'])->select(['surveys.id', 'userid'])->where(['surveys.id' => $this->id, 'owner' => 1 ])->asArray()->all();
        return array_column($owner, 'userid');
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

    public function getSurveytocollections()
    {
        return $this->hasMany(Surveytocollections::className(), ['surveyid' => 'id']);
    }

    public function getCollection()
    {
        return $this->hasMany(Collection::className(), ['id' => 'collectionid'])->viaTable('surveytocollections', ['surveyid' => 'id']);
    }

    public function getQuestions()
    {
        return $this->hasMany(Questions::className(), ['id' => 'questionid'])->viaTable('surveytoquestions', ['surveyid' => 'id']);
    }

    /**
     * Gets query for [[Surveytoresources]].
     *
     * @return \yii\db\ActiveQuery
     */
    // public function getSurveytoresources()
    // {
    //     return $this->hasMany(Surveytoresources::className(), ['surveyid' => 'id']);
    // }

    public function getResources()
    {
        return $this->hasMany(Resources::className(), ['id' => 'resourceid'])->viaTable('surveytocollections', ['id' => 'surveyid'])->viaTable('collection', ['collectionid' => 'id']);
    }

    /**
     * Gets query for [[Invitations]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInvitations()
    {
        return $this->hasMany(Invitations::className(), ['surveyid' => 'id']);
    }
    /**
     * Gets query for [[Rates]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRates()
    {
        return $this->hasMany(Rate::className(), ['surveyid' => 'id']);
    }

    public function getNumberOfRatings()
    {

        $survey = Surveys::findOne($this->id);
        $ratings_count = $survey->find()->joinWith(['rates'])->select(['surveys.id', 'count(distinct userid, resourceid) as RatingsCount'])->where(['surveyid' => $this->id])->groupBy('surveys.id')->asArray()->one();
        return ( isset( $ratings_count['RatingsCount'] ) ) ? $ratings_count['RatingsCount'] : 0;
    } 


    public function getOverview($surveyid, $limited = false){

            $table = '<table style = "width: 100%; text-align: center; ">';
            $survey = (array)Surveys::find()->select(['id', 'name', 'starts', 'ends', 'locked', 'about', 'minRespPerRes as `Minimum Responses Per Resource`', 'maxRespPerRes as `Maximum Responses Per Resource`', 'minResEv as `Minimum Resources Evaluated`', 'maxResEv as `Maximum Resources Evaluated`', 'fields' ])->where(['id' => $surveyid])->asArray()->one();
            
            

            $collection = Collection::find()->joinWith(['surveytocollections'])->where(['surveyid' => $surveyid])->one();
            
            if ( $collection ){

                $resources_q = Resources::find()->where(['collectionid' => $collection->id])->asArray()->all();

                foreach ($resources_q as $key => $value) {
                    if ( $value['type'] == 'image' ){
                        $resources[$key]['image'] = $value['image'];
                    }else if ($value['type'] == 'text'){
                        $resources[$key]['title'] = $value['title'];
                        $resources[$key]['text'] = $value['text'];
                    }else if ( $value['type'] == 'article' ){
                        $resources[$key]['title'] = $value['title'];
                        $resources[$key]['abstract'] = $value['abstract'];
                        $resources[$key]['pmc'] = $value['title'];
                        $resources[$key]['doi'] = $value['text'];
                        $resources[$key]['pubmed_id'] = $value['pubmed_id'];
                        $resources[$key]['authors'] = $value['authors'];
                        $resources[$key]['journal'] = $value['journal'];
                        $resources[$key]['year'] = $value['year'];
                    }else{
                        $resources[$key]['title'] = $value['title'];
                    }
                }       
            }
            
            $questions = Questions::find()->joinWith('surveytoquestions')->select(['questions.id', 'question', 'tooltip', 'answer', 'answervalues', 'answertype as `Answer Type`', 'allowusers as Public'])->where(['surveyid' => $survey['id']])->asArray()->all();

            // foreach ($questions as $key => $question) {
            //     print_r($question->attributeLabels());
            //     echo "<br><br>";
            // }
            // exit(0);
            foreach ($questions as $key => $value) {
                unset($questions[$key]['id']);
                if ( isset($questions[$key]['surveytoquestions']) ){
                    unset($questions[$key]['surveytoquestions']);
                }
                if ( $questions[$key]['Answer Type'] != 'textInput' ){
                    unset($questions[$key]['answer']);
                    if( $questions[$key]['Answer Type'] == 'radioList' ){
                        $questions[$key]['Answer Type'] = 'Radio List';
                    }else if ( $questions[$key]['Answer Type'] == 'Likert-5' ){
                        $questions[$key]['Answer Type'] = 'Likert 5';
                    }else if ( $questions[$key]['Answer Type'] == 'Likert-7' ){
                        $questions[$key]['Answer Type'] = 'Likert 7';
                    }
                }else{
                    $questions[$key]['Answer Type'] != 'Text Input';
                    unset($questions[$key]['answervalues']);
                }

                if ( $questions[$key]['Public'] == '1' ){
                    $questions[$key]['Public'] = 'True';
                }else{
                    $questions[$key]['Public'] = 'False';
                }

                if ( isset( $questions[$key]['answervalues'] ) ){
                    $answer_values = '<table>';
                    foreach ( json_decode($questions[$key]['answervalues']) as $f ){
                        $answer_values .= "<tr><td>".end($f)."</td><td>".key($f)."</td></tr>";
                    }
                    $questions[$key]['answer'] = $answer_values."</table>";
                    unset($questions[$key]['answervalues']);
                }

            }
            
            
            $participants = Participatesin::find()
                ->joinWith(['user' => function($q){
                    $q->select(['id', 'email', 'username', 'name', 'surname']);
                }])->where(['surveyid' => $surveyid])->select(['userid', 'user.username', 'user.name', 'user.surname', 'user.email', 'user.fields'])->asArray()->all();
            
            foreach ($participants as $key => $value) {
                if ( strpos($value['fields'], "&&") !== false ){
                    
                    foreach (explode("&&", $value['fields']) as $v) {
                        $table .= "<tr><td>".$v."</td></tr>";        
                    }   
                    $participants[$key]['fields'] = $table."</table>"; 
                }

                unset($participants[$key]['user']);
                unset($participants[$key]['userid']);
            }

            $badges = Badges::find()->joinWith('surveytobadges')->select(['name', 'image', 'badges.allowusers as Public', 'badges.id', 'surveycondition as `Survey Condition`', 'ratecondition as `Rating Condition`' ])->where(['surveyid' => $surveyid])->asArray()->all();

            foreach ($badges as $key => $value) {
                unset($badges[$key]['id']);
                if ( $badges[$key]['Public'] == '1' ){
                    $badges[$key]['Public'] = 'True';
                }else{
                    $badges[$key]['Public'] = 'False';
                }
                foreach ($value['surveytobadges'] as $k => $v) {
                    $badges[$key]['Survey Condition'] = $v['surveycondition'];
                    $badges[$key]['Rating Condition'] = $v['ratecondition'];
                }
                unset($badges[$key]['surveytobadges']);
            }

            unset($survey['id']);
            if ( $survey['starts'] == '' ){
                $survey['starts'] = 'Not set';
            }
            if ( $survey['ends'] == '' ){
                $survey['ends'] = 'Not set';
            }
            if ( $survey['locked'] == '0' ){
                $survey['locked'] = 'False';
            }else{
                $survey['locked'] = 'True';
            }

            if ( strpos($survey['fields'], "&&") !== false ){
                $table = '<table style = "width: 100%; text-align: center; ">';
                foreach (explode("&&", $survey['fields']) as $v) {
                    $table .= "<tr><td>".$v."</td></tr>";        
                }   
                $survey['fields'] = $table."</table>"; 
            }

            $survey_sections['General Settings'][0] = $survey;
            // $survey_sections['collection'] = $collection;
            $survey_sections['Collection of Resources'] = isset( $resources ) ? $resources : [];
            $survey_sections['questions'] = $questions;
            $survey_sections['participants'] = $participants;
            $survey_sections['badges'] = $badges;

            if ( $limited ){
                unset($survey_sections['questions']);
                unset($survey_sections['resources']);
            }

            return $survey_sections;
    }

    /**
     * Gets query for [[Surveytobadges]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSurveytobadges()
    {
        return $this->hasMany(Surveytobadges::className(), ['surveyid' => 'id']);
    }

    public function getCompletionCriteria(){
        // GET # OF RESOURCES EVALUATED
        $numRes = 0;
        if ( $this->getCollection()->one() ){
            $numRes = ( $this->getCollection()->one()->getResources()->count() ) ? $this->getCollection()->one()->getResources()->count() : 0 ;
        }
        
        $numResEval = $this->getRates()->groupBy(['resourceid'])->count();

        $numRespPerRes = $this->getRates()->select(['resourceid', 'COUNT(*)'])->groupBy(['resourceid'])->having(['>=', 'COUNT(*)', $this->minResEv])->count();
        $minResEv = ( $this->minResEv != null ) ? $this->minResEv : 'Not Set';

        $maxResEv = ( $this->maxResEv != null ) ? $this->maxResEv : 'Not Set';
        $minRespPerRes = ( $this->minRespPerRes != null ) ? $this->minRespPerRes : 'Not Set';
        $maxRespPerRes = ( $this->maxRespPerRes != null ) ? $this->maxRespPerRes : 'Not Set';
        // echo "Resources Evaluated: ".$numResEval."<br>";
        
        $minTotalProgress = '-';
        $maxTotalProgress = '-';
        $minRespPerResProgress = '-';
        $maxRespPerResProgress = '-';

        
        if ( $this->minResEv > 0 ){
            $minTotalProgress = "($numResEval/$this->minResEv) ".( round( $numResEval / $this->minResEv, 2 ) * 100 )."%";
            if( $numResEval / $this->minResEv >= 1){
                $minTotalProgress .= ' <a class ="fas fa-check" title = "Goal achieved!"></a>';
            }
            if ( $this->minRespPerRes > 0 ){
                $minRespPerResProgress = "($numRespPerRes/$this->minResEv) ".( round( $numRespPerRes / $this->minResEv, 2 ) * 100 )."%";
                if($numRespPerRes / $this->minResEv >= 1){
                    $minRespPerResProgress .= ' <a class ="fas fa-check" title = "Goal achieved!"></a>'; 
                }
            }else if ( $this->maxRespPerRes > 0 && $this->maxResEv > 0 ){
                $minRespPerResProgress = "($numRespPerRes/$this->maxResEv) ".( round( $numRespPerRes / $this->maxResEv, 2 ) * 100 )."%";
            }
        }

        if ( $this->maxResEv > 0 ){
            $maxTotalProgress = "($numResEval/$this->maxResEv) ".( round( $numResEval / $this->maxResEv, 2 ) * 100 )."%";
            

            if ( $this->maxRespPerRes > 0 ){
                $maxRespPerResProgress = "($numRespPerRes/$this->maxResEv) ".( round( $numRespPerRes / $this->maxResEv, 2 ) * 100 )."%";
            }else if ( $this->minRespPerRes > 0 && $this->minResEv > 0 ){
                $maxRespPerResProgress = "($numRespPerRes/$this->minResEv) ".( round( $numRespPerRes / $this->minResEv, 2 ) * 100 )."%";
            }
        }
        

        
        // GET # OF EVALUATIONS PER RESOURCE

        // CALCULATE MINIMUM TOTAL PROGRESS

        // CALCULATE MAXIMUM TOTAL PROGRESS

        $progressArray = [
            [
                'description' => 'Minimum Number Of Resources Evaluated',
                'goal' => $minResEv,
                'progress' => $minTotalProgress,
                // 'query' => $this->getRates()->groupBy(['resourceid'])->createCommand()->getRawSql()
            ],
            [
                'description' => 'Maximum Number Of Resources Evaluated',
                'goal' => $maxResEv,
                'progress' => $maxTotalProgress, 
                // 'query' => $this->getRates()->groupBy(['resourceid'])->createCommand()->getRawSql()
            ],
            [
                'description' => 'Minimum Number Of Evaluations Per Resource',
                'goal' => $minRespPerRes,
                'progress' => $minRespPerResProgress, 
                // 'query' => $this->getRates()->groupBy(['resourceid'])->createCommand()->getRawSql()
            ],
            [
                'description' => 'Maximum Number Of Evaluations Per Resource',
                'goal' =>  $maxRespPerRes,
                'progress' => $maxRespPerResProgress, 
                // 'query' => $this->getRates()->groupBy(['resourceid'])->createCommand()->getRawSql()
            ],
        ];
        return $progressArray;
        // return [$minResEv, $maxResEv, $minRespPerRes, $maxRespPerRes, $minTotalProgress, $maxTotalProgress];
    }
}

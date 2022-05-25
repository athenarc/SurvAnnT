<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\Surveys;
use app\models\Participatesin;
use app\models\Questions;
use webvimark\modules\UserManagement\models\User;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;
use yii\base\Model;
use app\models\SurveysSearch;
use yii\web\UploadedFile;
use app\models\UploadForm;
use app\models\Resources;
use app\models\Badges;
use app\models\Surveytoresources;
use app\models\Surveytoquestions;
use app\models\Surveytobadges;
use app\models\Surveytocollections;
use app\models\Invitations;
use app\models\Collection;
use app\models\Fields;
use app\models\Rate;
use app\models\Usertobadges;
use app\models\Leaderboard;
use yii\db\Expression;
use yii\widgets\ActiveForm;

date_default_timezone_set("Europe/Athens"); 

class QuestionsController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
            'ghost-access'=> [
                'class' => 'webvimark\modules\UserManagement\components\GhostAccessControl',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */

    public function beforeAction($event)
    {
        if ( !Yii::$app->user->isGuest ){    
            // NOTIFICATIONS FUNCTIONALITY
            $requests = [];
            $mySurveys = Surveys::find()->joinWith('participatesin')->where(['userid' => Yii::$app->user->identity->id, 'owner' => 1])->asArray()->all();
            foreach ($mySurveys as $key => $value) {
                $participants_requests = Participatesin::find()->where(['surveyid' => $value['id'], 'request' => 0])->asArray()->all();
                foreach ($participants_requests as $value) {
                    $requests[] = $value;
                }
            }
            $this->view->params['requests'] = $requests;
        }
        return parent::beforeAction($event);
    }


    public function actionQuestionEdit(){

        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;

        if (Yii::$app->request->isAjax) {

            if( isset($_POST['action'] ,$_POST['questionId'], $_POST['surveyId'] ) ){
                $action = $_POST['action'];
                $questionId = intval($_POST['questionId']);
                $surveyId = intval($_POST['surveyId']);
                $survey = Surveys::find()->where(['id' => $surveyId])->one();
                $userid = Yii::$app->user->identity->id;
                
                if( !$survey ){

                    $response->data = ['response' => 'Survey not found', 'action' => $action, 'questionId' => $questionId, 'surveyId' => $surveyId];
                    $response->statusCode = 404;
                    return $response;
                }

                if( ! in_array( $userid, array_values( $survey->getOwner() ) ) ){
                    $response->data = ['response' => 'User unauthorized', 'action' => $action, 'questionId' => $questionId, 'surveyId' => $surveyId];
                    $response->statusCode = 401;
                    return $response;
                }

                if( $survey->active == 1 ){
                    $response->data = ['response' => 'Survey active', 'action' => $action, 'questionId' => $questionId, 'surveyId' => $surveyId, 'survey_active' => $survey->active, 'survey_name' => $survey->name, 'survey_id' => $survey->id];
                    $response->statusCode = 404;
                    return $response;
                }

                $question = Questions::find()->where(['id' => $questionId])->one();

                if( $question ){
                    
                    $surveytoquestions = Surveytoquestions::find()->where(['questionid' => $question->id, 'surveyid' => $survey->id])->one();
                    if(!$surveytoquestions){
                        
                        $response->data = ['response' => 'Survey to questions not found', 'action' => $action, 'questionId' => $questionId, 'surveyId' => $surveyId];
                        $response->statusCode = 404;
                        return $response;
                    } 

                }else{
                    
                    $response->data = ['response' => 'Question not found', 'action' => $action, 'questionId' => $questionId, 'surveyId' => $surveyId];
                    $response->statusCode = 404;
                    return $response;
                
                }

                $message = '';

                if( $action == 'delete' ){

                    $surveytoquestions->delete();
                    // $badge->delete();

                }else if ( $action == 'modify' ){
                    
                    if ( isset($_POST['questionQuestion'] ) ){
                        
                        $questionQuestion = $_POST['questionQuestion'];
                        $question->question = $questionQuestion;
                        $question->save();
                        
                    }
                    if ( isset( $_POST['questionAllowUsers'] ) ){
                        $questionAllowUsers = $_POST['questionAllowUsers'];
                        
                        if(  $questionAllowUsers == false || $questionAllowUsers == 'false'){
                            $questionAllowUsers = 0;
                            $message = "question allow false";
                        }else{
                            $questionAllowUsers = 1;
                            $message = "question allow true";
                        }
                        $question->allowusers = $questionAllowUsers;
                        $question->save();
                    }

                    if ( isset( $_POST['questionTooltip'] ) ){
                        $questionTooltip = $_POST['questionTooltip'];
                        $question->tooltip = $questionTooltip;
                        $question->save();
                    }
                    if ( isset( $_POST['questionAnswerType'] ) ){
                        $questionAnswerType = $_POST['questionAnswerType'];
                        $question->answertype = $questionAnswerType;
                        if ( $questionAnswerType == 'textInput' ){
                            $question->answer = (isset($_POST['questionAnswer'])) ? escapeshellcmd($_POST['questionAnswer']) : $question->answer;
                        }else{
                            if ( isset($_POST['questionAnswerValues']) ){
                                $answerValues = explode("<<>>", $_POST['questionAnswerValues']);
                                $answer_values = [];
                                foreach ($answerValues as $value) {
                                    $key_value = explode("<>", $value);
                                    if ( isset($key_value[0], $key_value[1]) ){
                                        $answer_values[] = array($key_value[1]=> $key_value[0]);
                                    }
                                }
                                $question->answervalues = json_encode($answer_values);
                            }   
                            
                        }
                        $question->save();
                    }                    
                }

                $response->data = ['response' => $action.' successfull', 'action' => $action, 'questionId' => $questionId, 'surveyId' => $surveyId, 'question_id' => $question->id, 'survey_id' => $survey->id, 'questionAllowUsers' => $_POST['questionAllowUsers'], 'questionQuestion' => $_POST['questionQuestion'], 'message' => $message];
                $response->statusCode = 200;
            }
                


        }

    }

    public function actionQuestionsDeleteAll(){

        $userid = Yii::$app->user->identity->id;
        if ( isset($_GET['surveyid']) ){
            $survey = Surveys::findOne(escapeshellcmd($_GET['surveyid']));
            if( $survey ){

                if ( in_array( $userid, array_values( $survey->getOwner() ) ) ){

                    Surveytoquestions::deleteAll(['surveyid' => $survey->id]);
                    return $this->redirect(['questions/questions-create', 'surveyid' => $survey->id] ?: Yii::$app->homeUrl);
                }

            }
        }

        

    }

    public function actionQuestionsCreate(){
        $question = new Questions();
        $userid = Yii::$app->user->identity->id;
        
        if ( !isset( $_GET['surveyid'] ) ){
            if ( isset( $_GET['r'] ) && $_GET['r'] == 'site/participants-invite'){
                return $this->goHome();
            }
        }else{
            $surveyid = $_GET['surveyid'];
            $survey = Surveys::findOne($surveyid);
            if ($survey->active || ! in_array( $userid, array_values( $survey->getOwner() ) ) ){
                return $this->goBack();
            }
            
        }
        

        
        $questions = [new Questions()];

        if( isset($_POST['Questions']) ){
            for ($i=0; $i < sizeof($_POST['Questions']) - 1; $i++) { 
                $questions[] = new Questions();
            }
        }

        if ( Model::loadMultiple($questions, Yii::$app->request->post() ) ){

            if ( Model::validateMultiple($questions) ) {

                foreach ($questions as $key => $question) {
                    $surveytoquestions = new Surveytoquestions();
                    $surveytoquestions->ownerid = $userid;
                    $surveytoquestions->surveyid = $surveyid;
                    $answer_values = [];
                    $question->ownerid = $userid; 
                    if ( $question->answertype != 'textInput' ){
                        $num_of_answers = sizeof( preg_grep( "/-$key-".$question->answertype."-[0-9]-answer/", array_keys( $_POST ) ) );
                        $greped_arr = preg_grep( "/-$key-".$question->answertype."-<[0-9]>-answer/", array_keys( $_POST ) );
                        if ( sizeof ($greped_arr) == 0 ){
                            $greped_arr = preg_grep( "/question-$key-".$question->answertype."-[0-9]-answer/", array_keys( $_POST ) );
                        }
                        foreach ($greped_arr as $key => $value) {
                            $answer_values[] = array($_POST[str_replace("answer", "value", $value)] => $_POST[$value]);
                        }

                        $question->answervalues = json_encode($answer_values);
                    }else{
                        $question->answervalues = null;
                    }

                    $question->save();
                        
                    $surveytoquestions->questionid = $question->id;
                    if ( ! Surveytoquestions::find()->where(['questionid' => $question->id, 'surveyid' => $surveyid])->one() ){
                        $surveytoquestions->save();
                    }
                }                
            }
        }
        $SurveyQuestions = Questions::find()->joinWith('surveytoquestions')->where(['surveyid' => $surveyid]);

        $paginationSurveyQuestions = new Pagination(['totalCount' => $SurveyQuestions->count(), 'pageSize'=>10]);
        $SurveyQuestions = $SurveyQuestions->offset($paginationSurveyQuestions->offset)->limit($paginationSurveyQuestions->limit)->all();

        $dbQuestions = Questions::find()->where(['allowusers' => 1])->orWhere(['ownerid' => $userid])->all();
        $questions = [new Questions()];
        
        $fields = array_values ( array_keys ( Questions::attributeLabels() ) );
        
        $excluded = [ 'id', 'created', 'ownerid', 'surveyid', 'answervalues', 'question', 'answertype', 'destroy', 'answer'];
        $colspan = sizeof($fields) - sizeof($excluded);
        $likert_5 = Yii::$app->params['likert-5'];
        $likert_7 = Yii::$app->params['likert-7'];
        $answertypes = ['textInput' => 'Text Input', 'radioList' => 'Radio List', 'Likert-5' => 'Likert-5', 'Likert-7' => 'Likert-7'];
        $options = ['db-load' => 'Load questions from database' , 'user-form' => 'Create your own questions'];
        $option = '';
        $message = 'Questions';
        $tabs = $this->tabsManagement($message, $survey);
        $questionsNew = [new Questions()];
        

        return $this->render('//site/questionscreate', [
            'survey' => $survey,
            'fields' => $fields,
            'questions' => $questions,
            'SurveyQuestions' => $SurveyQuestions,
            'paginationSurveyQuestions' => $paginationSurveyQuestions,
            'dbQuestions' => $dbQuestions,
            'questionsNew' => $questionsNew,
            'method' => 'user-form',
            'action' => 'generate-participants',
            'message' => $message,
            'excluded' => $excluded,
            'tabs' => $tabs,
            'answertypes' => $answertypes,
            'options' => $options,
            'option' => $option,
            'colspan' => $colspan,
            'likert_5' => $likert_5,
            'likert_7' => $likert_7,
        ]);

    }

    public function actionQuestionsUse(){

        $userid = Yii::$app->user->identity->id;
        $questions = new Questions();
        if ( isset($_POST) ){
            $surveyid = isset( $_POST['surveyId'] ) ? $_POST['surveyId'] : '';
            $survey = Surveys::findOne(escapeshellcmd($surveyid));
            if( $survey ){

                $num_of_answers = sizeof( preg_grep( "/agree-question-[0-9]/", array_keys( $_POST ) ) );
                $greped_arr = preg_grep( "/agree-question-[0-9]/", array_keys( $_POST ) );
                foreach ($greped_arr as $key => $value) {
                    $question = Questions::findOne(escapeshellcmd(str_replace("agree-question-", "", $value)));
                    if ( $question ){
                        $newQuestion = new Questions();
                        $newQuestion->attributes = $question->attributes;
                        $newQuestion->save();

                        $surveytoquestions = new Surveytoquestions();
                        $surveytoquestions->surveyid = $survey->id;
                        $surveytoquestions->questionid = $newQuestion->id;
                        $surveytoquestions->ownerid = $userid;
                        $surveytoquestions->save();
                    }
                }
                Yii::$app->response->redirect( array( 'questions/questions-create', 'surveyid' => $surveyid));
            } 
        }
        // return $this->goBack();
    }

    public function tabsManagement($tab = null, $survey = null)
    {
        $tabs = Yii::$app->params['tabs'];

        if ( $survey->isNewRecord ){

            $tabs['General Settings']['enabled'] = 1;
            
        }else{
            $tabs['General Settings']['enabled'] = 1;
            $tabs['General Settings']['set'] = '<i class="fas fa-circle-check"></i>';
            $tabs['Resources']['enabled'] = 1;
            $tabs['Questions']['enabled'] = 1;
            $tabs['Badges']['enabled'] = 1;
            $tabs['Participants']['enabled'] = 1;
            $tabs['Overview']['enabled'] = 1;

            if ( $survey->getCollection()->all() ){
                if ( $survey->getCollection()->one()->getResources()->all() ){
                    $resources_count = ' ('.sizeof( $survey->getCollection()->one()->getResources()->all() ).')';
                    if ( sizeof( $survey->getCollection()->one()->getResources()->all() ) >= $survey->minResEv ){
                        
                        $tabs['Resources']['set'] = '<i class="fas fa-circle-check"></i>'.$resources_count;
                    }else{
                        $tabs['Resources']['set'] = '<i class="fas fa-circle-exclamation" title = "Number of minimum resources evaluated set goal set is greater than the number of actual resources imported. Either lower the goal or import more resources."></i>'.$resources_count;
                    }
                }
            }

            if ( $survey->getQuestions()->all() ){
                $questions_count = ' ('.sizeof( $survey->getQuestions()->all() ).')';
                $tabs['Questions']['set'] = '<i class="fas fa-circle-check"></i>'.$questions_count;
            }

            if ( $survey->badgesused ){
                if ( $survey->getSurveytobadges()->all() ){
                    $tabs['Badges']['set'] = '<i class="fas fa-circle-check"></i> ('.$survey->getSurveytobadges()->count().')';
                }
            }else{
                $tabs['Badges']['set'] = '<i class="fas fa-circle-check"></i> ('.$survey->getSurveytobadges()->count().')';
            }

            $tabs['Participants']['set'] = '<i class="fas fa-circle-check"></i> ('.($survey->getParticipatesin()->count()-1).')';

            if ( $survey->active ){
                $tabs['Overview']['set'] = '<i class="fas fa-circle-check"></i>';
            }
            
        }
        return $tabs;

    }   

}

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

class SiteController extends Controller
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

    public function actionInviteUser(){

        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;

        if (Yii::$app->request->isAjax) {

            if ( isset( $_POST['surveyid'], $_POST['userid'], $_POST['action']) ){
                $userid = escapeshellcmd( $_POST['userid'] );
                $users = User::findOne($userid);
                $response->data = ['response' => 'user mail', 'mail' => $users->email];
                $response->statusCode = 200;
            }

        }

    }

    public function actionUserRequests(){

        if ( isset( $_GET['surveyid'] ) ){
            $surveyid = escapeshellcmd($_GET['surveyid']);
            $survey = Surveys::findOne($surveyid);
            if ( $survey ){
                // FIND PARTICIPANTS
                $participants = Participatesin::find()->where(['surveyid' => $survey->id, 'request' => 0])->all();
                foreach ($participants as $participant) {
                    // print_r($participant->getUser()->select(['id', 'name', 'surname', 'username', 'fields'])->asArray()->all());
                    // echo "<br><br>";

                }
                return $this->render('acceptusers', ['survey' => $survey, 'participants' => $participants]);
            }
        }else{
            return $this->goHome();
        }

    }

    public function actionIndex()
    {

        return $this->redirect(['site/about']);
    }

    public function actionLeaderboard()
    {
        $userid = Yii::$app->user->identity->id;
        $lead = new Leaderboard();
        $survey_leaderboards['global_leaderboard'] = $lead->getTotalLeaderboard();
        $leaderboard = 'global_leaderboard';
        $surveyid = "global_leaderboard";
        if ( Yii::$app->request->post() ){
            
            $surveyid = isset( $_POST['Leaderboard'] ) ?  $_POST['Leaderboard'] : '';
            if ( $surveyid != 'global_leaderboard'){
                $survey_leaderboards = $lead->getAllLeaderboards($surveyid);
            }
            
        }
        
        $surveys = Surveys::find()->joinWith('participatesin')->select(['name', 'surveys.id'])->where(['active' => 1, 'userid' => $userid ])->all();
        $survey_names = array_values( array_column( $surveys, 'name') );
        $survey_ids = array_values( array_column( $surveys, 'id') );
        $tabs = [];
        $tabs['global_leaderboard'] = 'Global Leaderboard';
        foreach ($survey_names as $key => $survey_name) {
            $tabs[$survey_ids[$key]] = ucwords($survey_name);
        }
        return $this->render('leaderboard', ['survey_leaderboards' => $survey_leaderboards, 'tabs' => $tabs, 'survey_names' => $survey_names, 'surveyid' => $surveyid]);


    }

    public function actionSurveysStatistics()
    {
        $userid = Yii::$app->user->identity->id;

        $user = User::findOne(Yii::$app->user->identity->id);

        $surveys = Surveys::find()->joinWith('participatesin');

        if ( ! $user->hasRole(['Superadmin']) ){
            $surveys->where(['owner' => 1, 'userid' => $userid]);
        }
        $surveys = $surveys->all();

        $survey_names['all_surveys'] = 'All Surveys';

        $surveyid = 'all_surveys';

        foreach ($surveys as $survey) {
            $survey_names[$survey->id] = ucwords( $survey->name );

        }

        if( Yii::$app->request->get() && isset($_GET['surveyid']) ){
            $surveyid = isset( $_GET['surveyid'] ) ?  $_GET['surveyid'] : '';

            if ( $surveyid != 'all_surveys'){
                $surveyid = $_GET['surveyid'];
                $surveys = [];
                $surveys[0] = Surveys::findOne($surveyid);
            }
        }

        if ( Yii::$app->request->post() ){
            
            $surveyid = isset( $_POST['surveyid'] ) ?  $_POST['surveyid'] : '';
            if ( $surveyid != 'all_surveys'){
                $_GET = null;
                $surveyid = $_POST['surveyid'];
                $surveys = [];
                $surveys[0] = Surveys::findOne($surveyid);
            }else{
                $surveys = Surveys::find()->joinWith('participatesin');

                if ( ! $user->hasRole(['Superadmin']) ){
                    $surveys->where(['owner' => 1, 'userid' => $userid]);
                }
                $surveys = $surveys->all();
            }
            
        }

        $series = [];
        foreach ($surveys as $key => $survey) {
            $series[$survey->id] = $survey->createStatistics();           
        }
        return $this->render('surveysstatistics', ['survey_names' => $survey_names, 'surveyid' => $surveyid, 'surveys' => $surveys, 'series' => $series]);
    }

    public function actionSurveysView()
    {
        $columns = ['surveys.id', 'name', 'starts', 'ends','participatesin.surveyid', 'participatesin.id', 'participatesin.owner', 'participatesin.userid', 'user.username', 'user.id' ];
        date_default_timezone_set("Europe/Athens"); 
        $date = date('Y-m-d H:i:s', time());
        $query = Surveys::find();

        // LIMIT ONLY TO AVAILABLE SURVEYS UNLESS USER IS ADMIN OR SUPERADMIN
        if ( ! Yii::$app->user->identity->hasRole(['Admin', 'Superadmin']) ){
            $query = Surveys::find()->where(['>', 'starts',  $date])->orWhere(['starts' => null]);
        }

        $searchModel = new SurveysSearch();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $tabs =
            [
                'Active campaigns in SurvAnnT' => 
                    [
                        'link' => 'index.php?r=site%2Fsurveys-view', 
                        'enabled' => 1
                    ],
                'My campaigns' => 
                    [
                        'link' => 'index.php?r=site%2Fmy-surveys-view',
                        'enabled' => 1
                    ]
            ];


        if ( isset( $_GET['surveyid'] ) ){
            $surveyid = $_GET['surveyid'];

            if ( isset( $_POST['finalize'] ) ){
                // exit(0);
                $survey = Surveys::findOne($surveyid);
                if ( $survey ){
                    if ( $survey->getCollection()->one() ){
                        if ( $survey->getCollection()->one()->getResources()->all() ){
                            if ( $survey->getQuestions()->all() ){
                                $survey->active = 1;
                                $survey->save();
                                return $this->redirect(['site/surveys-view']);
                            }
                        }
                    }
                }
                
            }
            if ( Surveys::findOne($surveyid) ){
                $survey = Surveys::findOne($surveyid);        
                // print_r($survey->getCompletionCriteria());
                // exit(0);
                $collection = $survey->getCollection()->one();
                
                $resources = ( $collection !== null ) ? $collection->getResources() : [] ;
                if( $resources ){
                    $paginationResources = new Pagination(['totalCount' => $resources->count(), 'pageSize'=>10]);
                    $resources = $resources->offset($paginationResources->offset)->limit($paginationResources->limit)->all();
                }else{
                    $paginationResources = new Pagination(['totalCount' => [], 'pageSize'=>10]);
                }
                

                $participants = $survey->getParticipatesin()->all();
                $questions = $survey->getQuestions()->all();
                $rates = [];
                // RESOURCES # NUMBER OF RATINGS
                foreach ($resources as $resource) {
                    $rates['resources'][$resource->id]['users'] = [];
                    foreach ($resource->getRates()->groupBy(['resourceid', 'userid'])->all() as $rate){

                        $username = $rate->getUser()->select(['username'])->one()['username'];
                        $user_id = $rate->getUser()->select(['id'])->one()['id'];
                        if ( ! in_array($username, $rates['resources'][$resource->id]['users']) ){
                            $rates['resources'][$resource->id]['users'][] = '<a href = "index.php?r=user-management%2Fuser%2Fview&id='.$user_id.'">'.$username."</a>";
                        }
                    }
                }
                foreach ($questions as $question) {
                    $rates['questions'][$question->id]['users'] = [];
                    $rates['questions'][$question->id]['answer'] = 0;
                    foreach ($question->getRates()->groupBy(['questionid', 'userid'])->all() as $rate){

                        $username = $rate->getUser()->select(['username'])->one()['username'];
                        $user_id = $rate->getUser()->select(['id'])->one()['id'];
                        if ( is_numeric($rate->answer) ){
                            $rates['questions'][$question->id]['answer'] += (int)$rate->answer;
                        }else{
                            $rates['questions'][$question->id]['answer'] = '-';
                        }

                        if ( ! in_array($username, $rates['questions'][$question->id]['users']) ){
                            $rates['questions'][$question->id]['users'][] = '<a href = "index.php?r=user-management%2Fuser%2Fview&id='.$user_id.'">'.$username."</a>";
                        }
                    }
                }        
                $message = '';
                if ( !$survey->active && in_array(Yii::$app->user->identity->id, $survey->getOwner()) ){
                    $message = 'Overview';
                    $tabs = $this->tabsManagement($message, $survey);
                }

                $badges = $survey->getSurveytobadges() ;
                if( $badges ){
                    $paginationBadges = new Pagination(['totalCount' => $badges->count(), 'pageSize'=>10]);
                    $badges = $badges->offset($paginationBadges->offset)->limit($paginationBadges->limit)->all();
                }else{
                    $paginationBadges = new Pagination(['totalCount' => [], 'pageSize'=>10]);
                }

                $paginations[0] = $paginationResources; 
                $paginations[1] = $paginationBadges;

                return $this->render('surveysview', ['survey' => $survey, 'resources' => $resources, 'questions' => $questions, 'rates' => $rates, 'badges' =>$badges, 'tabs' => $tabs, 'message' => $message, 'surveyid' => $surveyid, 'paginations' => $paginations]);
            }
        }


        // NOTIFICATIONS FUNCTIONALITY
        $requests = [];
        $mySurveys = Surveys::find()->joinWith('participatesin')->where(['userid' => Yii::$app->user->identity->id, 'owner' => 1])->asArray()->all();
        foreach ($mySurveys as $key => $value) {
            $participants_requests = Participatesin::find()->where(['surveyid' => $value['id'], 'request' => 0])->asArray()->all();
            foreach ($participants_requests as $value) {
                $requests[] = $value;
            }
        }

        $message = 'Active campaigns in SurvAnnT';

        return $this->render('surveys', [
            'surveys' => $dataProvider,
            'message' => 'Currently running surveys',
            'tabs' => $tabs,
            'message' => $message,
            'requests' => $requests
        ]);
    }

    public function actionMySurveysView()
    {

        $searchModel = new SurveysSearch();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, true);

        $surveys = new ActiveDataProvider([
            'query' => Surveys::find()->joinWith(['participatesin'])->where(['userid' => Yii::$app->user->identity->id]),
            'pagination' => [
                'pagesize' => 10,
            ],
        ]);
        $tabs =
            [
                'Active campaigns in SurvAnnT' => 
                    [
                        'link' => 'index.php?r=site%2Fsurveys-view', 
                        'enabled' => 1
                    ],
                'My campaigns' => 
                    [
                        'link' => 'index.php?r=site%2Fmy-surveys-view',
                        'enabled' => 1
                    ]
            ];
        $message = 'My campaigns';
        return $this->render('surveys', [
            'surveys' => $dataProvider,
            'message' => 'My surveys',
            'tabs' => $tabs,
            'message' => $message
        ]);

    }

    public function actionSurveyParticipants()
    {
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;

        if (Yii::$app->request->isAjax) {

            if ( isset( $_POST['surveyid'], $_POST['userid'], $_POST['action']) ){

                $surveyid = $_POST['surveyid'];
                $userid = $_POST['userid'];
                $action = $_POST['action'];

                if ( $userid == -1 ){
                    if ( isset( $_POST['email'] ) ){
                        $email = escapeshellcmd( $_POST['email'] );
                        

                        if ( $action == 'add' ){
                            $invitation = Invitations::find()->where(['surveyid' => $surveyid, 'email' => $email])->one();
                            if ( $invitation ){
                                $response->data = ['response' => 'User already invited', 'invitation' => $invitation];                                
                            }else{
                                if ( User::find()->where(['email' => $email])->one() ){
                                    $inv_user = User::find()->where(['email' => $email])->one();
                                    if ( Participatesin::find()->where(['userid' => $inv_user->id])->one() ){
                                        $response->data = ['response' => 'User already participating', 'invitation' => $invitation];
                                    }else{
                                        $response->data = ['response' => 'User already registered', 'invitation' => $invitation];
                                    }
                                    
                                }else{
                                    $invitation = new Invitations();
                                    $invitation->email = $email;
                                    $invitation->hash = hash("sha256", $email);
                                    $invitation->surveyid = $surveyid; 
                                    $invitation->save();
                                    
                                    // INVTITATION FEATURE //
                                    $survey = Surveys::findOne($surveyid);
                                    $owner = User::findOne($survey->getOwner());
                                    $ownerNameSurname = $owner->name.' '.$owner->surname;
                                    $surveyDescription = $survey->about;
                                    $surveyName = $survey->name;
                                    $invitation->email_send($ownerNameSurname, $surveyName, $surveyDescription);
                                    $response->data = ['response' => 'User Invited', 'invitation' => $invitation];
                                }  
                            }
                            
                        }else if ( $action == 'delete' ) {
                            $invitation = Invitations::find()->where(['surveyid' => $surveyid, 'email' => $email])->one();
                            $invitation->delete();
                            $response->data = ['response' => 'User invitation Revoked'];
                        }

                        
                        $response->statusCode = 200;
                        return $response;
                    }else{
                        $response->data = ['response' => 'User not invited. No email found.'];
                        $response->statusCode = 400;
                        return $response;
                    }
                    
                }

                if ( User::find()->where(['id' => $userid]) && Surveys::find()->where(['id' => $surveyid]) ){
                    $participant = new Participatesin();
                    $participant->userid = $userid;
                    $participant->surveyid = $surveyid;

                    if ( $action == 'add' ){
                        if ( ! Participatesin::find()->where(['surveyid' => $surveyid])->andWhere(['userid' => $userid])->one() ){
                            $participant->save();
                            $leaderboard = new Leaderboard();
                            $leaderboard->surveyid = $surveyid;
                            $leaderboard->userid = $userid;
                            if ( ! Leaderboard::find()->where(['surveyid' => $surveyid, 'userid' => $userid])->one() ){
                                $leaderboard->save();
                            }
                            $user = User::findOne($userid);
                            User::assignRole($userid, 'Rater');
                            if ( ! $user->hasRole(['Rater']) ){
                                
                                $assigned = 'True';
                            }else{
                                $assigned = 'False';
                            }
                            $response->data = ['response' => 'Participant saved.', 'assigned' => $assigned];
                            $response->statusCode = 200;
                        }else{
                            $participant = Participatesin::find()->where(['surveyid' => $surveyid])->andWhere(['userid' => $userid])->one();
                            if ( $participant->request == 0 ){
                                $participant->request = 1;
                                $participant->save();
                                $leaderboard = new Leaderboard();
                                $leaderboard->surveyid = $surveyid;
                                $leaderboard->userid = $userid;
                                if ( ! Leaderboard::find()->where(['surveyid' => $surveyid, 'userid' => $userid])->one() ){
                                    $leaderboard->save();
                                }
                                $response->data = ['response' => 'User accepted.'];
                                $response->statusCode = 200;
                            }else{
                                $response->data = ['response' => 'User already participates.'];
                                $response->statusCode = 200;
                            }
                            
                        }
                    }else if ( $action == 'delete' ) {
                        if ( Participatesin::find()->where(['surveyid' => $surveyid])->andWhere(['userid' => $userid])->one() ){
                            $participant = Participatesin::find()->where(['surveyid' => $surveyid])->andWhere(['userid' => $userid])->one();
                            $participant->delete(); 
                            $response->data = ['response' => 'Participant deleted.'];
                            $response->statusCode = 200;
                        }else{
                            
                            $response->data = ['response' => 'Participant not found.'];
                            $response->statusCode = 200;
                        }
                    }

                }else{
                    $response->data = ['response' => 'Survey or User not found.'];
                    $response->statusCode = 404;
                }

            }else{
                $response->data = ['response' => 'Did not retrieve proper variables.', 'variables' => $_POST];
                $response->statusCode = 404;
            }

        }

        return $response;
    }
    
    public function actionSurveyDelete()
    {
        
        if ( isset( $_GET['surveyid'] ) ){
            $id = $_GET['surveyid'];
            $userid = Yii::$app->user->identity->id;
            $survey = Surveys::find()->where(['id' => $id])->one();
            // $participants = $survey->getParticipatesin()->all();
            
            // FIND OWNER BEFORE DELETING
            $owner = $survey->isOwner($userid)->asArray()->one();
            
            if ( Yii::$app->user->isSuperadmin || $owner['userid'] == $userid){

                Participatesin::deleteAll(['surveyid' => $survey->id]);
                Surveytoresources::deleteAll(['surveyid' => $survey->id]);
                Surveytoquestions::deleteAll(['surveyid' => $survey->id]);
                Surveytocollections::deleteAll(['surveyid' => $survey->id]);
                Surveytobadges::deleteAll(['surveyid' => $survey->id]);
                Rate::deleteAll(['surveyid' => $survey->id]);
                Leaderboard::deleteAll(['surveyid' => $survey->id]);
                Usertobadges::deleteAll(['surveyid' => $survey->id]);
                $survey->delete();
            }
            
        }
        if(Yii::$app->request->referrer){
            return $this->redirect(Yii::$app->request->referrer);
        }else{
          return $this->goHome();
        }
    }

    public function actionSurveyRate()
    {
        $userid = Yii::$app->user->identity->id;
        if ( isset($_GET['surveyid']) ){

            $surveyid = $_GET['surveyid'];
            $survey = Surveys::findOne($surveyid);
            if ( ! $survey ){
                return $this->goHome();
            }else{

                $participant = Participatesin::find()->where(['userid' => $userid, 'surveyid' => $survey->id])->one();

                $rates_query = Rate::find()->select(['resourceid'])->where(['userid' => $userid]);

                $rates_query_feedback = $rates_query;

                if ( Leaderboard::find()->where(['surveyid' => $survey->id, 'userid' => $userid])->one()){
                    $leaderboard = Leaderboard::find()->where(['surveyid' => $survey->id, 'userid' => $userid])->one();
                }else{
                    $leaderboard = new Leaderboard();
                    $leaderboard->userid = $userid;
                    $leaderboard->surveyid = $surveyid;
                    $leaderboard->save();
                }



                // GET USERS PROVIDED FEEDBACK FOR THIS SURVEY + IN GENERAL
                $user_feedback_provided_general = sizeof ( $rates_query_feedback->groupBy(['resourceid'])->all() );
                $user_feedback_provided = sizeof ( $rates_query_feedback->andWhere(['surveyid' => $surveyid])->groupBy(['resourceid'])->all() );

                // GET NUMBER OF FINISHED SURVEYS FOR USER
                $user_surveys_finished = Participatesin::find()->select(['count(*) as SurveysFinished'])->where(['userid' => $userid, 'finished' => 1])->asArray()->one();
                if ( $user_surveys_finished ){
                    $user_surveys_finished = $user_surveys_finished['SurveysFinished'];
                }else{
                    $user_surveys_finished = 0;
                }
                // IF PROVIDED FEEDBACK == SURVEY'S GOAL -> THANKS MESSAGE

                // ELSE RETRIEVE RESOURCE TO PROVIDE FEEDBACK FOR

                // IF RESOURCE FEEDBACK == SURVEY'S RESOURCE MAX EVALUATIONS THEN SELECT ANOTHER RESOURCE

                $minimum_resources_eval_goal = isset ( $survey->minRespPerRes ) ? (int)$survey->minRespPerRes : null ;
                $maximum_resources_eval_goal = isset ( $survey->maxRespPerRes ) ? (int)$survey->maxRespPerRes : null ;
                $minimum_resources_goal = isset ( $survey->minResEv ) ? (int)$survey->minResEv : null ;
                $maximum_resources_goal = isset ( $survey->maxResEv ) ? (int)$survey->maxResEv : null ;

                $survey_ratings = $survey->getNumberOfRatings();
                

                if ( $minimum_resources_goal ){
                    // MINIMUM RESOURCES EVALUATED x TIMES FOR SURVEY, MAYBE A NOTIFICATION;
                    if ( $minimum_resources_eval_goal && $minimum_resources_eval_goal > 0 ){
                        // IF A LOWER LIMIT IS SET THEN SURVEY RATINGS MUST BE >= MINIMUM RES EVAL GOAL * MIN RESOURCES
                        $expression = $survey_ratings >= $minimum_resources_goal * $minimum_resources_eval_goal;
                    }else if( $maximum_resources_eval_goal && $maximum_resources_eval_goal > 0 ) {
                        // IF AN UPPER LIMIT IS SET THEN SURVEY RATINGS MUST BE >= MAXIMUM RES EVAL GOAL * MIN RESOURCES
                        $expression = $survey_ratings >= $minimum_resources_goal * $maximum_resources_eval_goal;
                    }else{
                        // IF NO LIMIT IS SET THEN SURVEY RATINGS MUST BE >= MIN RESOURCES
                        $expression = $survey_ratings >= $minimum_resources_goal;
                    }

                    if ( $expression && ( ! isset($maximum_resources_goal) || $maximum_resources_goal == 0 ) ){
                        $survey->completed = 1;
                        $survey->active = 0;
                        $survey->save();
                    }
                }

                if ( $maximum_resources_goal ){
                    if ( $minimum_resources_eval_goal && $minimum_resources_eval_goal > 0 ){
                        // IF A LOWER LIMIT IS SET THEN SURVEY RATINGS MUST BE >= MINIMUM RES EVAL GOAL * MAX RESOURCES
                        $expression = $survey_ratings >= $maximum_resources_goal * $minimum_resources_eval_goal;
                    }else if( $maximum_resources_eval_goal && $maximum_resources_eval_goal > 0 ) {
                        // IF AN UPPER LIMIT IS SET THEN SURVEY RATINGS MUST BE >= MAXIMUM RES EVAL GOAL * MAX RESOURCES
                        $expression = $survey_ratings >= $maximum_resources_goal * $maximum_resources_eval_goal;
                    }else{
                        // IF NO LIMIT IS SET THEN SURVEY RATINGS MUST BE >= MAX RESOURCES
                        $expression = $survey_ratings >= $maximum_resources_goal;
                    }
                    if ( $expression ){
                        // MAXIMUM RESOURCES EVALUATED FOR SURVEY, SO SURVEY FINISHES
                        $survey->completed = 1;
                        $survey->active = 0;
                        $survey->save();
                    }
                }

                // GET RESOURCES THAT HAVE NOT BEEN ANNOTATED YET BY CURRENT USER
                $resource = Collection::find()->joinWith(['resources', 'surveytocollections'])->where(['surveytocollections.surveyid' => $survey->id])->andWhere(['NOT IN', 'resources.id', $rates_query]);

                if ( $minimum_resources_eval_goal && ( ! isset( $maximum_resources_eval_goal ) || $maximum_resources_eval_goal == 0 ) ){
                    // IF SURVEY HAS MINIMUM RESOURCE EVALUATIONS SET AND NOT A MAX, THEN WE LIMIT THE RESOURCE QUERY TO THE RESOURCES THAT HAVE NOT EXCEEDED THIS LIMIT
                    $resource_rates_query_min = Rate::find()->select(['resourceid'])->groupBy(['surveyid', 'resourceid'])->where(['surveyid' => $survey->id])->having(['>', 'count(distinct userid, resourceid)', $minimum_resources_eval_goal])->createCommand()->getRawSql();
                                        
                    $resource->andWhere(['NOT IN', 'resources.id', $resource_rates_query_min]);

                }

                if ( $maximum_resources_eval_goal ){
                    // IF SURVEY HAS MAXIMUM RESOURCE EVALUATIONS SET AND NOT A MIN, THEN WE LIMIT THE RESOURCE QUERY TO THE RESOURCES THAT HAVE NOT EXCEEDED THIS LIMIT
                    $resource_rates_query_max = Rate::find()->select(['resourceid'])->groupBy(['surveyid', 'resourceid'])->where(['surveyid' => $survey->id])->having(['>', 'count(distinct userid, resourceid)', $maximum_resources_eval_goal]);
                    
                    $resource->andWhere(['NOT IN', 'resources.id', $resource_rates_query_max]);

                }
                if ( $survey->randomness ){
                    $resource = $resource->select(['resources.*'])->orderBy(new Expression('rand()'))->asArray()->one();
                }else{
                    $resource = $resource->select(['resources.*'])->asArray()->one();
                }
                
                
                // $fetched_resource_id = $resource['id'];
                // $fetched_resource_evals = Rate::find()->select(['count(*)'])->where(['resourceid' => $fetched_resource_id])->groupBy(['resourceid', 'questionid'])->all();

                if ( ! $resource ){
                    $message = '<p>Thank you for participating in Campaign '.$survey->name.'.</p>';
                    $message .= '<p>Feel free to request participation in other surveys as well.</p>';
                } 

                if ( $survey->completed == 1 ){
                    $message = '<p>Thank you for participating in Campaign '.$survey->name.".</p> <p>The Campaign is completed! </p><p> Feel free to request participation in other surveys as well.</p>";   
                }
                    
                if ( $survey->completed == 1 || ! $resource ){

                    $participant->finished = 1;
                    $participant->save();

                    if ( $leaderboard ){
                        $leaderboard = Leaderboard::find()->where(['surveyid' => $survey->id, 'userid' => $userid])->one();
                        $leaderboard->points += Yii::$app->params['Scoring-system']['Survey-Completion'];
                        $leaderboard->save();
                    }
                }

                
                if ( $survey->badgesused == 1 || $survey->getSurveytobadges()->all() ){

                    $surveytobadges = Surveytobadges::find()->where(['surveyid' => $surveyid])->all();

                    foreach ($surveytobadges as $surveytobadge) {
                        $badge = $surveytobadge->getBadge()->one();
                        
                        $usertobadges = new Usertobadges();
                        $usertobadges->userid = $userid;
                        $usertobadges->badgeid = $badge->id;
                        $usertobadges->surveyid = $survey->id;
                        $rate_cond = (int)$surveytobadge->ratecondition - (int)$user_feedback_provided;
                        $rate_conditions[] = ($rate_cond >= 0 ) ? $rate_cond : -1 * $rate_cond;
                        $ratings_expression = (int)$user_feedback_provided >= (int)$surveytobadge->ratecondition && (int)$surveytobadge->ratecondition > 0;

                        if ( $ratings_expression ){
                            if ( ! Usertobadges::find()->where(['surveyid' => $survey->id, 'userid' => $userid, 'badgeid' => $badge->id])->all() ){
                                $usertobadges->save();
                                if ( $leaderboard ){
                                    if ( ! Usertobadges::find()->where(['surveyid' => $surveyid, 'userid' => $userid])->all() ){
                                        // FIRST BADGE FOR THE USER
                                        $leaderboard->points += Yii::$app->params['Scoring-system']['First-Badge-Earned'];
                                    }

                                    if ( ! Usertobadges::find()->where(['surveyid' => $surveyid, 'badgeid' => $badge->id])->all() ){
                                        // IF NO ONE ELSE ON THIS SURVEY HAS ACQUIRED SPECIFIC BADGE
                                        $leaderboard->points += Yii::$app->params['Scoring-system']['First-To-Earn-Badge'];
                                    }

                                    $leaderboard->save();
                                }
                                
                            }
                        }
                    }                    
                }

                $next_badge_goal = 0;
                if ( isset( $rate_conditions ) && sizeof( array_filter($rate_conditions ) ) > 0 ){
                    
                    $next_badge_goal = min( array_filter($rate_conditions, function($v) { return $v > 0; }) );
                    // print_r($next_badge_goal);
                }else{
                    $rate_conditions = [];
                }
                
                if ( $survey->completed == 1 || $participant->finished == 1 ){
                    
                    // USER HAS FINISHED ANNOTATION
                    return $this->render('finished', ['message' => $message]);
                }

                $rates = [];
                $questions = Questions::find()->joinWith('surveytoquestions')->where(['surveyid' => $surveyid])->asArray()->all();
                foreach ($questions as $key => $question) {
                    $rate = new Rate();
                    $rate->userid = $userid;
                    $rate->questionid = $question['id'];
                    $rate->resourceid = $resource['id'];
                    $rate->collectionid = $resource['collectionid'];
                    $rate->answertype = $question['answertype'];
                    $rate->tooltip = $question['tooltip'];
                    $rate->surveyid = $survey->id;
                    $rates[] = $rate;
                    $answer_values = [];
                    if ( $question['answertype'] != 'textInput' ){
                        foreach ((array)json_decode($question['answervalues']) as $k => $v) {
                            if ( isset( $answer_values[key($v)] ) ){
                                $answer_values[key($v) ."//". $k] = end($v);
                            }else{
                                $answer_values[key($v)] = end($v);
                            }
                        }
                        $questions[$key]['answervalues'] = $answer_values;
                    }
                }
            }            
        }

        if ( Model::loadMultiple($rates, Yii::$app->request->post() ) ){
            if ( Model::validateMultiple($rates) ) {
                foreach ($rates as $rate) {
                    if ( strpos( $rate->answer, "//") ){
                        $rate->answer = explode("//", $rate->answer)[0];
                    }
                    $rate->save();
                }
                
                $leaderboard->points += Yii::$app->params['Scoring-system']['Annotation'];
                $leaderboard->save();
                return $this->redirect(['site/survey-rate', 'surveyid' => $surveyid]);
            }
        }

        $acquired_badges = [];
        foreach (Yii::$app->user->identity->getUsertobadges()->where(['surveyid' => $survey->id])->all() as $usertobadge) {
            $acquired_badges[] = $usertobadge->getBadge()->select(['image'])->one()['image'];
        }
        $progresses =
            [
                'annotation_goal' =>
                    [
                        'title' => 'Annotation Goal',
                        'progress' => ( $minimum_resources_goal > 0 ) ? substr( ( $user_feedback_provided / $minimum_resources_goal ) * 100, 0, 4 ) : 0,
                        'message' => "% (".($minimum_resources_goal - $user_feedback_provided)." more Resources need to be annotated) "
                    ],
                'additional_annotation_goal' =>
                    [
                        'title' => 'Additional Annotation Goal',
                        'progress' => ( $maximum_resources_goal > 0 ) ? substr( ( ( $user_feedback_provided - $minimum_resources_goal ) /  $maximum_resources_goal ) * 100, 0, 4 ) : 0,
                        'message' => "% (".(  $maximum_resources_goal - $user_feedback_provided )." more Resources can be annotated) "
                    ],
                'next_badge_goal' =>
                    [
                        'title' => 'Next Badge Goal',
                        'progress' => ( $next_badge_goal > 0 ) ? substr( ( $user_feedback_provided /  ( $user_feedback_provided + $next_badge_goal ) ) * 100, 0, 4 ) : 0,
                        'message' => "% (".$next_badge_goal." more resources for the next badge) "
                    ]
            ];

            if(  $minimum_resources_goal > 0 && $user_feedback_provided >= $minimum_resources_goal ){
                $progresses['annotation_goal']['message'] = '% ';
                $progresses['annotation_goal']['title'] .= ' <a class = "fas fa-check link-icon" title = "completed"></a> ';
            }

            if( $maximum_resources_goal > 0 && $user_feedback_provided >= $maximum_resources_goal ){
                $progresses['additional_annotation_goal']['message'] = '% ';
                $progresses['additional_annotation_goal']['title'] .= ' <a class = "fas fa-check link-icon" title = "completed"></a> ';
            }
       
        return $this->render('rate.php', ['resource' => $resource, 'questions' => $questions, 'rates' => $rates, 'user_feedback_provided' => $user_feedback_provided, 'survey' => $survey, 'user_feedback_provided_general' => $user_feedback_provided_general, 'minimum_resources_goal' => $minimum_resources_goal, 'maximum_resources_goal' => $maximum_resources_goal, 'rate_conditions' => $rate_conditions, 'next_badge_goal' => $next_badge_goal, 'acquired_badges' => $acquired_badges, 'progresses' => $progresses]);
    }

    public function actionRequestParticipation()
    {

        if ( isset( $_GET['surveyid'] ) ){
            $surveyid = escapeshellcmd( $_GET['surveyid'] );
            if ( Surveys::findOne( $surveyid ) ){
                $survey = Surveys::findOne( $surveyid );
                if ( Yii::$app->user->identity->hasRole(['Rater', 'Admin', 'Superadmin']) ){
                    $userid = Yii::$app->user->identity->id;
                    $participant = new Participatesin();
                    $participant->surveyid = $surveyid;
                    $participant->userid = $userid;
                    $participant->request = 0;
                    $participant->save();
                    return $this->redirect(['site/surveys-view']);
                }    

            }
        }else{
            return $this->goHome();
        }

    }

    public function actionSurveyCreate()
    {
        $userid = Yii::$app->user->identity->id;

        if ( isset( $_GET['surveyid'] ) ){
            $survey = Surveys::findOne($_GET['surveyid']);
            if ( $survey ){
                $participant = $survey->getParticipatesin()->where(['userid' => $userid])->one();
                if ( ! $participant ){
                    $participant = new Participatesin();
                }
            }
        }else{
            $survey = new Surveys();
            $participant = new Participatesin();
        }
        
        
        $users = User::find()->select(['id', 'username'])->all();

        $message = 'General Settings';
        $tabs = $this->tabsManagement($message, $survey);
        
        $fields = [];
        $db_survey_fields = Surveys::find()->select(['fields'])->asArray()->all();
        
        foreach ( array_filter( array_column( $db_survey_fields, 'fields' ) ) as $key => $value) {
            // array_merge($fields, explode("&&", $value));

            foreach (explode("&&", $value) as $v) {
                $fields[$v] = $v;
            }
            
        }

        if ( $survey->load( Yii::$app->request->post() ) ) {
            if ($survey->validate()) {

                $surv_fields = [];
                if ( is_array($survey->fields) ){
                    foreach ($survey->fields as $key => $value) {
                        $field = new Fields();
                        $field->name = ucwords( $value );
                        if ( ! Fields::find()->where(['name' => $value])->all() ){
                            $field->save();
                        }
                        $surv_fields[$field->name] = $field->name;
                    }
                    $survey->fields = implode("&&", $surv_fields);
                }
                
               

                if ( $survey->save() ){
                    $participant->userid = $userid;
                    $participant->owner = 1;
                    $participant->surveyid = $survey->id;
                    $participant->save();
                    Yii::$app->response->redirect( array( '//resources/resource-create', 'surveyid' => $survey->id ));
                }                
                
            }

        }

        return $this->render('surveycreatenew', [
            'surveyid' => $survey->id,
            'survey' => $survey,
            'participant' => $participant,
            'users' => $users,
            'message' => $message,
            'tabs' => $tabs,
            'fields' => $fields
        ]);
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
            $collection = $survey->getCollection()->one();

            if ( $collection ){
                $resources = $collection->getResources()->asArray()->all();
                if ( $resources ){
                    $resources_count = ' ('.sizeof( $resources ).')';
                    if ( sizeof( $resources ) >= $survey->minResEv ){
                        
                        $tabs['Resources']['set'] = '<i class="fas fa-circle-check"></i>'.$resources_count;
                    }else{
                        $tabs['Resources']['set'] = '<i class="fas fa-circle-exclamation" title = "Number of minimum resources evaluated goal is set to be greater than the number of actual resources imported. Either change the goal or import more resources."></i>'.$resources_count;
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

    public function actionParticipantsInvite()
    {
        date_default_timezone_set("Europe/Athens"); 
        $userid = Yii::$app->user->identity->id;
        $users = User::find()->select(['id', 'username', 'name', 'surname', 'email', 'fields'])->where(['!=', 'username', 'superadmin'])->andWhere(['availability' => 1])->asArray()->all();
        
        if ( !isset( $_GET['surveyid'] ) ){
            if ( isset( $_GET['r'] ) && $_GET['r'] == 'site/participants-invite'){
                return $this->goHome();
            }
        }else{
            $surveyid = $_GET['surveyid'];

        }

        $survey = Surveys::findOne( $surveyid );

        $message = 'Participants';
        $tabs = $this->tabsManagement($message, $survey);

        if ( $survey->getCollection()->all() && $survey->getQuestions()->all() ){
            $tabs['Overview']['enabled'] = 1;
        }

        if ($survey->active || ! in_array( $userid, array_values( $survey->getOwner() ) ) ){
            return $this->goBack();
        }
        $fields = array_filter( explode("&&", $survey->fields) );
        $user_participants = Participatesin::find()->where(['surveyid' => $surveyid])->asArray()->all(); // 'owner' => 0
        $user_invited = Invitations::find()->where(['surveyid' => $surveyid])->asArray()->all();
        $limit_on_fields = false;

        if ( Yii::$app->request->post() ){

            if( isset($_POST['reset-filter']) ){
                $limit_on_fields = $_POST['reset-filter'];
            }
        }

        

        foreach ($users as $key => $user) {

            if ( isset( $user_participants ) && in_array( $user['id'], array_column( $user_participants, 'userid')) ){
                $users[$key]['participates'] = 1;
            }else{
                $users[$key]['participates'] = 0;
            }
            if ( Participatesin::find()->where(['userid' => $user['id'], 'request' => 0, 'surveyid' => $surveyid])->all() ){
                $users[$key]['request'] = 0;
            }else{
                $users[$key]['request'] = 1;
            }
           
            if ( $limit_on_fields ){
                if ( sizeof( array_intersect( $fields, explode("&&", $users[$key]['fields']) ) ) == 0 && sizeof($fields) > 0 && $users[$key]['participates'] == 0 ){
                    // IF USERS IN DB HAVE NO REASEARCH FIELDS IN COMMON WITH THE SURVEY FIELDS UNSET THEM

                    unset($users[$key]);
                    continue;
                }
            } 

            if ( $users[$key]['id'] == Yii::$app->user->identity->id ){
                $users[$key]['owner'] = 1;
            }
        }

        foreach ($user_invited as $usr_inv) {
            $users[] = ['userid' => $usr_inv['id'], 'name' => '-', 'surname' => '-', 'email' => $usr_inv['email'], 'participates' => -1];
        }


        // FIND REQUESTED SURVEY
        
        if ( $survey ){
            // IF USER IS OWNER
            if ( $survey->isOwner( $userid )->one() ){
            }

        }else{

            return $this->goHome();
        
        }
        if ( Yii::$app->request->post() ){
            if ( isset($_POST['finalize']) ){
                return $this->redirect(['site/badges-create-new', 'surveyid' => $surveyid]);
            }
        }
        $users = array_values($users);

        return $this->render('participatesin', [
                    'surveyid' => $surveyid,
                    'survey' => $survey,
                    'users' => $users,
                    'action' => 'generate-participants',
                    'message' => $message,
                    'tabs' => $tabs,
                    'limit_on_fields' => $limit_on_fields
                ]);
    }

    public function actionSurveyOverview()
    {

        $collection = null;
        $survey_sections = [];
        $resources = [];
        $questions = [];
        $badges = [];
        $participants = [];
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
            
            $survey_sections = $survey->getOverview($surveyid);

        }

        $message = 'Overview';
        $tabs = $this->tabsManagement($message, $survey);

        if ( isset( $_POST['finalize'] ) ){
            $survey = Surveys::findOne($surveyid);
            $survey->active = 1;
            $survey->save();
            return $this->redirect(['site/surveys-view']);
        }

        return $this->render('surveyoverview', [
                    'tabs' => $tabs,
                    'message' => $message,
                    'surveyid' => $surveyid,
                    'survey' => $survey,
                    'collection' => $collection,
                    'resources' => $resources,
                    'questions' => $questions,
                    'participants' => $participants,
                    'badges' => $badges,
                    'survey_sections' => $survey_sections
                ]);

    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {   
        
        $tabs = Yii::$app->params['about'];

        $message =  isset ( $_GET['tab'] ) ? $_GET['tab'] : 'What is SurvAnnT?';
        $about = Yii::$app->params['about'];
        return $this->render('about', [ 'about' => $about, 'tabs' => $tabs, 'message' => $message ]);
    }
}

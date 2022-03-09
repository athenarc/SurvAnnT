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
use app\models\Dataset;
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
        // print_r(Yii::$app->user->identity->getLeaderboards()->asArray()->all());
        // exit(0);
        return $this->render('index', []);
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

    public function actionSurveysView()
    {
        $columns = ['surveys.id', 'name', 'starts', 'ends','participatesin.surveyid', 'participatesin.id', 'participatesin.owner', 'participatesin.userid', 'user.username', 'user.id' ];
        date_default_timezone_set("Europe/Athens"); 
        $date = date('Y-m-d h:m:s', time());
        $query = Surveys::find();

        // LIMIT ONLY TO AVAILABLE SURVEYS UNLESS USER IS ADMIN OR SUPERADMIN
        if ( ! Yii::$app->user->identity->hasRole(['Admin', 'Superadmin']) ){
            $query = Surveys::find()->where(['>', 'starts',  $date])->orWhere(['starts' => null]);
        }

        // print_r($_GET);

        // exit(0);

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
            if ( Surveys::findOne($surveyid) ){
                $query->andWhere(['id' => $surveyid]);
                $tabs = ['Campaign' => ['link' => '', 'enabled' => 1]];
                $survey = Surveys::findOne($surveyid);
                $survey_sections = $survey->getOverview($surveyid, true);
                $message = 'Campaign';
                $survey_sections['resources'] = [];
                
                
                return $this->render('surveysview', ['survey' => $survey, 'tabs' => $tabs, 'message' => $message, 'surveyid' => $surveyid, 'survey_sections' => $survey_sections]);
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
                                    $invitation->email_send();
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

    

    public function actionDatasetCreate(){
        $dataset = new Dataset();
        $questions = new Questions();
        $userid = Yii::$app->user->identity->id;
        $message = 'Dataset';
        $tabs = Yii::$app->params['tabs'];
        $tabs['Survey']['enabled'] = 1;
        $tabs['Resources']['enabled'] = 1;
        
        if ( !isset( $_GET['surveyid'] ) ){
            if ( isset( $_GET['r'] ) && $_GET['r'] == 'site/participants-invite'){
                return $this->goHome();
            }
        }else{
            $surveyid = $_GET['surveyid'];
            $tabs['Questions']['enabled'] = 1;
            $tabs['Participants']['enabled'] = 1;
        }

        

        if ( isset( $surveyid ) && Dataset::find()->where(['surveyid' => $surveyid])->all() ){
            $datasets = Dataset::find()->where(['surveyid' => $surveyid])->all();

        }else{
            if( file_exists(Yii::$app->params['dataset']) ){
                $datasets = $dataset->read(Yii::$app->params['dataset'], $surveyid, $userid);
            }else{
                $datasets = [new Dataset()];
            }
        }


        if ( isset( $_POST['Dataset'] ) && sizeof( $_POST['Dataset'] ) > sizeof( $datasets ) ){
            $diff = sizeof( $_POST['Dataset'] ) - sizeof($datasets);
            for ($i = 0; $i < $diff; $i++) {
                $new_dataset = new Dataset();
                $new_dataset->surveyid = $surveyid; 
                $new_dataset->ownerid = $userid; 
                $datasets[] = $new_dataset;
                // echo "$i Creating new dataset ", $diff, " <br><br>";
            }

        }
   
        $fields = array_values ( array_keys ( Dataset::attributeLabels() ) );
        $excluded = [ 'id', 'created', 'ownerid', 'surveyid', 'abstract', 'title', 'destroy'];
        $colspan = sizeof($fields) - sizeof($excluded);

        
        if ( Model::loadMultiple($datasets, Yii::$app->request->post() ) ){
            if ( Model::validateMultiple($datasets) ) {
                foreach ($datasets as $dataset) {
                    
                    if ( ! $dataset->destroy ){
                        $dataset->save(false);
                    }else{
                        if ( $dataset->id ){
                            $dataset->delete();
                        }
                    }
                }

                Yii::$app->response->redirect( array( 'site/questions-create', 'surveyid' => $surveyid));
            }
        }
        return $this->render('datasetcreate', [
            'surveyid' => $surveyid,
            'fields' => $fields,
            'datasets' => $datasets,
            'questions' => $questions,
            'action' => 'generate-participants',
            'message' => $message,
            'excluded' => $excluded,
            'tabs' => $tabs,
            'colspan' => $colspan
        ]);
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
                // Leaderboard::deleteAll(['surveyid' => $survey->id]);
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
                
                // if ( $minimum_resources_goal ){
                //      MINIMUM RESOURCES EVALUATED FOR SURVEY, MAYBE A NOTIFICATION;
                //     if ( $survey_ratings >= $minimum_resources_goal ){
                //     }
                // }

                if ( $maximum_resources_goal ){
                    if ( $survey_ratings >= $maximum_resources_goal ){
                        // MAXIMUM RESOURCES EVALUATED FOR SURVEY, SO SURVEY FINISHES
                        $survey->completed = 1;
                        $survey->active = 0;
                        $survey->save();
                    }
                }


                $resource = Collection::find()->joinWith(['resources', 'surveytocollections'])->where(['surveytocollections.surveyid' => $survey->id])->andWhere(['NOT IN', 'resources.id', $rates_query]);

                if ( $maximum_resources_eval_goal ){
                    // IF SURVEY HAS MAXIMUM RESOURCE EVALUATIONS SET, THEN WE LIMIT THE RESOURCE QUERY TO THE RESOURCES THAT HAVE NOT EXCEEDED THIS LIMIT
                    
                    $resource_rates_query = Rate::find()->select(['resourceid'])->groupBy(['surveyid', 'resourceid'])->having(['>=', 'count(distinct userid, resourceid)', $maximum_resources_eval_goal]);
                    
                    $resource->andWhere(['NOT IN', 'resources.id', $resource_rates_query]);

                }

                $resource = $resource->select(['resources.*'])->asArray()->one();
                
                // $fetched_resource_id = $resource['id'];
                // $fetched_resource_evals = Rate::find()->select(['count(*)'])->where(['resourceid' => $fetched_resource_id])->groupBy(['resourceid', 'questionid'])->all();

                if ( ! $resource ){
                    $message = 'Thank you for participating in Campaign '.$survey->name.". <br> You have annotated every resource! <br><br> Feel free to request participation in other surveys as well.";
                } 

                if ( $survey->completed == 1 ){
                    $message = 'Thank you for participating in Campaign '.$survey->name.". <br> The survey is completed! <br><br> Feel free to request participation in other surveys as well.";   
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

                
                if ( $survey->badgesused == 1 ){

                    $surveytobadges = Surveytobadges::find()->where(['surveyid' => $surveyid])->all();

                    foreach ($surveytobadges as $surveytobadge) {
                        $badge = $surveytobadge->getBadge()->one();
                        
                        $usertobadges = new Usertobadges();
                        $usertobadges->userid = $userid;
                        $usertobadges->badgeid = $badge->id;
                        $usertobadges->surveyid = $survey->id;
                        $rate_conditions[] = (int)$surveytobadge->ratecondition - (int)$user_feedback_provided;
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
                
                $next_badge_goal = min( array_filter($rate_conditions, function($v) { return $v > 0; }) );

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
                    print_r($question['tooltip']);
                    echo "<br><br><br>";
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
        return $this->render('rate.php', ['resource' => $resource, 'questions' => $questions, 'rates' => $rates, 'user_feedback_provided' => $user_feedback_provided, 'survey' => $survey, 'user_feedback_provided_general' => $user_feedback_provided_general, 'minimum_resources_goal' => $minimum_resources_goal, 'rate_conditions' => $rate_conditions, 'next_badge_goal' => $next_badge_goal]);
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

    public function actionSurveyCreateNew()
    {
        $survey = new Surveys();
        $participant = new Participatesin();
        $users = User::find()->select(['id', 'username'])->all();
        $tabs = Yii::$app->params['tabs'];
        $tabs['Campaign']['enabled'] = 1;
        $message = 'Campaign';
        $userid = Yii::$app->user->identity->id;
        $fields = [];


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

    public function actionSurveyCreate()
    {
        date_default_timezone_set("Europe/Athens"); 
        $survey = new Surveys();
        $participant = new Participatesin();
        $users = User::find()->select(['id', 'username'])->all();
        $tabs = Yii::$app->params['tabs'];
        $tabs['Campaign']['enabled'] = 1;
        $message = 'Campaign';
        $userid = Yii::$app->user->identity->id;
        $fields = [];
        $db_survey_fields = Surveys::find()->select(['fields'])->asArray()->all();
        
        foreach ( array_filter( array_column( $db_survey_fields, 'fields' ) ) as $key => $value) {
            // array_merge($fields, explode("&&", $value));

            foreach (explode("&&", $value) as $v) {
                $fields[$v] = $v;
            }
            
        }
        
        foreach (Yii::$app->params['fields'] as $key => $value) {
            $fields[$value] = $value;
        }

        foreach ($fields as $key => $value) {
            $field = new Fields();
            $field->name = ucwords( $value );
            if ( ! Fields::find()->where(['name' => $value])->all() ){
                $field->save();
            }
        }

        if ( isset( $_GET['surveyid'] ) ){
            $surveyid = $_GET['surveyid'];
            $survey = Surveys::findOne($surveyid);
            if ($survey->active || ! in_array( $userid, array_values( $survey->getOwner() ) ) ){
                return $this->goBack();
            }
            $survey->fields = explode("&&", $survey->fields);
            $tabs['Resources']['enabled'] = 1;
            $tabs['Questions']['enabled'] = 1;
            $tabs['Participants']['enabled'] = 1;
            $tabs['Badges']['enabled'] = 1;
            $tabs['Overview']['enabled'] = 1;
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

                $survey->save();
                if ( ! isset ( $_GET['edit'] ) ){
                    if ( sizeof ( Participatesin::find()->where(['userid' => $userid, 'surveyid' => $survey->id])->all() ) == 0 ){
                        $participant->userid = $userid;
                        $participant->owner = 1;
                        $participant->surveyid = $survey->id;
                        $participant->save();
                    }else{

                    }

                    
                }
                
                Yii::$app->response->redirect( array( 'site/resource-create', 'surveyid' => $survey->id, 'edit' => 1));
            }

        }
        return $this->render('surveycreate', [
            'surveyid' => $survey->id,
            'survey' => $survey,
            'participant' => $participant,
            'users' => $users,
            'message' => $message,
            'tabs' => $tabs,
            'fields' => $fields
        ]);
    }

    public function actionResourceCreate()
    {

        $userid = Yii::$app->user->identity->id;
        if ( isset( $_GET['surveyid'] ) ){
            $surveyid = $_GET['surveyid'];
            $survey = Surveys::findOne($surveyid);
            if ($survey->active || ! in_array( $userid, array_values( $survey->getOwner() ) )){
                return $this->goBack();
            }
        }

        $tabs = Yii::$app->params['tabs'];
        
        $tabs['Campaign']['enabled'] = 1;
        $tabs['Resources']['enabled'] = 1;
        $tabs['Questions']['enabled'] = 1;
        $tabs['Participants']['enabled'] = 1;
        $tabs['Badges']['enabled'] = 1;
        
        $message = 'Resources';

        $options = ['db-load' => 'Load Collections from database' , 'dir-load' => 'Load Collections from directory' ]; 

        $resource_types = Yii::$app->params['resources_allowlist'];
        
        $db_available_resources = [];

        foreach (array_column ( Resources::find()->select(['DISTINCT (type)'])->where(['allowusers' => 1])->orWhere(['ownerid' => $userid])->asArray()->all(), 'type' ) as $key => $value) {
            if ( $value == 'questionaire' ){
                $db_available_resources[$value] = 'No resources (Single Questionaire)';
            }else{
                $db_available_resources[$value] = ucwords($value);
            }
            
        }

        $dir_available_resources = [];
        foreach ( array_diff( scandir( Yii::$app->params['resources'] ), array(".", "..")) as $key => $value) {
           
            $dir_available_resources[$value] = ucwords($value);
        }

        $option = 'dir-load';
        $resource_types_option = 'article';
        
        // 1) RETRIEVE COLLECTIONS FOR SURVEY
        // 2) IF NONE RETRIEVE ALLOWED COLLECTIONS
        // 3) IF NONE LOAD FROM DIRECTORY


        
        $collections = Collection::find()->joinWith('surveytocollections')->where(['surveyid' => $surveyid])->all();
        $my_collection = Collection::find()->joinWith('surveytocollections')->where(['surveyid' => $surveyid])->one();


        $tool = "block";
        $resources = [];
        if ( empty( $collections ) ){
            
            $collections = Collection::find()->joinWith('resources')->where(['type' => $resource_types_option, 'collection.allowusers' => 1, 'resources.allowusers' => 1])->all();
            if ( empty( $collections ) ){
                // $resource_types_option = 'image';
                $collection = new Collection();
                $collection->userid = $userid;
                $collection->name = '';
                $collections = [ $collection ];

                $resource = new Resources();
                $resources = $resource->read( $userid, $resource_types_option );
            }
            

        }else{
            $tool = "none";
            $resource_types_option = isset($collections[0]->getResources()->all()[0]['type']) ? $collections[0]->getResources()->all()[0]['type'] : $resource_types_option;
            $resource_types = [$resource_types_option => ucwords($resource_types_option)];
            $dir_available_resources = [$resource_types_option => ucwords($resource_types_option)];
            $db_available_resources = [$resource_types_option => ucwords($resource_types_option)];
            // $option = "user-form";

        }

        if ( Yii::$app->request->post() ){
            
            if ( isset($_POST['resources-function'], $_POST['resources-type']) ){
                $option = escapeshellcmd( $_POST['resources-function'] );
                $resource_types_option = escapeshellcmd( $_POST['resources-type'] );
            }

            if ( $option == 'db-load' ){
                $resources = [];

                $collections = Collection::find()->joinWith('surveytocollections')->where(['surveyid' => $surveyid])->all();
                if ( empty( $collections ) ){
                    $collections = Collection::find()->joinWith('resources')->where(['type' => $resource_types_option, 'collection.allowusers' => 1, 'resources.allowusers' => 1])->orWhere(['type' => $resource_types_option, 'collection.userid' => $userid, 'resources.ownerid' => $userid])->all();
                }
                foreach ($collections as $collection) {
                    foreach ($collection->getResources()->where(['type' => $resource_types_option])->all() as $resource) {

                        if ( $resource->allowusers == 1 || $resource->ownerid == $userid){
                            $resources[] = $resource;
                        }
                    }
                }
            }else{
                // OPTION == DIR LOAD 
                $collections = Collection::find()->joinWith('surveytocollections')->where(['surveyid' => $surveyid])->all();
                    if ( empty( $collections ) ){
                        // USER LOADS FROM DIRECTORY
                        $collection = new Collection();
                        $collection->userid = $userid;
                        $collection->name = '';
                        $collections = [ $collection ];

                        if ( $option == 'dir-load' ){
                            $resource = new Resources();
                            $resources = $resource->read( $userid, $resource_types_option );
                        }
                    }else{
                        
                        // USER IS IN FINAL STEP OF COLLECTION CREATION
                        foreach ($collections as $collection) {
                            foreach ($collection->getResources()->where(['type' => $resource_types_option])->all() as $resource) {
                                $resources[] = $resource;       
                            }
                        }
                    }
            }

            if ( isset( $_POST['submit-resource-form'] ) ){
                
                $new_collection_name = isset( $_POST['Collection']['name'] ) ? $_POST['Collection']['name'] : '';
                $new_resources = [];

                foreach ($collections as $col_key => $collection) {
                    // echo "Name: ".$collection->name." id: $collection->id <br><br>";
                    if ( isset( $_POST['agree-collection-'.$col_key] ) || $option == 'dir-load'  ){
                        if ( $option == 'dir-load' ){
                            $collection->name = $new_collection_name;
                        }

                        $collection->allowusers = isset( $_POST['Collection'][$col_key]['allowusers'] ) ? (int) $_POST['Collection'][$col_key]['allowusers'] : $collection->allowusers;
                        $new_collection_allowusers = $collection->allowusers;

                        foreach ($resources as $res_key => $resource) {
                            if ( isset( $_POST['agree-'.$col_key.'-'.$resource->type."-".$res_key] ) || $option == 'dir-load' ){

                                // NEW COLLECTION FROM DIRECTORY ( RESOURCE DOES NOT HAVE ID )
                                // NEW COLLECTION FROM DB ( RESOURCE HAS ID )
                                // MY COLLECTION ( RESOURCE HAS ID )
                                $resource->allowusers = isset( $_POST['Resources'][$res_key]['allowusers'] ) ? (int) $_POST['Resources'][$res_key]['allowusers'] : $resource->allowusers;

                                if ( empty( $my_collection ) ){
                                    $new_resource = new Resources();                                        
                                    $new_resource->attributes = $resource->attributes;
                                    if ( $resource->type == 'image' ){  
                                        if ( $resource->isNewRecord ){
                                            $new_resource->image = file_get_contents( $resource->image );
                                        }else{
                                            $new_resource->image = $resource->image;
                                        }
                                    }
                                    $new_resource->isNewRecord = true;
                                    $new_resource->id = null;
                                    $new_resources[] = $new_resource;
                                    // echo "Creating new resource from $resource->id <br><br>";
                                }else{
                                    $new_resources[] = $resource;
                                    // echo "Saving new resource from $resource->id <br><br>";
                                }
                            }
                        }
                    }
                }
                if ( empty( $my_collection ) ){
                    $collection = new Collection();
                    $collection->userid = $userid;

                    $surveytocollections = new Surveytocollections();
                    $surveytocollections->ownerid = $userid;
                    $surveytocollections->surveyid = $surveyid;
                    $url = 'site/resource-create';
                }else{
                    $url = 'site/questions-create';
                    $collection = $my_collection;
                    $surveytocollections = $collection->getsurveytocollections()->one();
                }
                
                    $collection->name = $new_collection_name;
                    $collection->allowusers = (int)$new_collection_allowusers;
                    $collection->save();

                    $surveytocollections->collectionid = $collection->id;
                    $surveytocollections->save();

                foreach ($new_resources as $new_resource) {
                    $new_resource->collectionid = $collection->id;
                    $new_resource->ownerid = $userid;
                    // echo "Saving resource $resource->id <br><br>";
                    $new_resource->save();
                }
                return $this->redirect([$url, 'surveyid' => $surveyid, 'edit' => 1]);
            }
              
            if ( isset($_POST['discard-collection']) && $_POST['discard-collection'] == 'discard' ){

                $surveytocollections = Surveytocollections::find()->where(['surveyid' => $surveyid])->all();
                foreach ($surveytocollections as $surveytocollection) {
                    $col_id = $surveytocollection->collectionid;
                    $surveytocollection->delete();
                    $resources_to_delete = Resources::find()->where(['collectionid' => $col_id])->all();
                    foreach ($resources_to_delete as $res) {
                        $res->delete();
                    }
                    $col = Collection::findOne($col_id);
                    $col->delete();
                }
                return $this->redirect(['site/resource-create', 'surveyid' => $surveyid, 'edit' => 1]);
            }

        }

        if ( empty( $my_collection ) ){
            $my_collection = new Collection();
            $my_collection->userid = $userid;
        }
        // foreach ($collections as $col) {
        //     print_r($col->getResources()->where(['allowusers' => 1])->orWhere(['ownerid' => $userid])->asArray()->all());

        // }
        // exit(0);
        return $this->render('resourcecreate', 
            [
                'tool' => $tool,
                'options' => $options, 
                'option' => $option, 
                'tabs' => $tabs, 
                'message' => $message, 
                'surveyid' => $surveyid,
                'resource_types_option' => $resource_types_option,
                'resource_types' => $resource_types,
                'db_available_resources' => $db_available_resources,
                'dir_available_resources' => $dir_available_resources,
                'userid' => $userid,
                'collections' => $collections,
                'resources' => $resources,
                'collection' => $my_collection
            ]);

    }

    public function actionQuestionsCreate(){
        $question = new Questions();
        $userid = Yii::$app->user->identity->id;
        $message = 'Questions';
        $tabs = Yii::$app->params['tabs'];
        $tabs['Campaign']['enabled'] = 1;
        $tabs['Resources']['enabled'] = 1;
        $tabs['Questions']['enabled'] = 1;
        $tabs['Participants']['enabled'] = 1;
        $tabs['Badges']['enabled'] = 1;
        
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
        $questions = Questions::find()->joinWith('surveytoquestions')->where(['surveyid' => $surveyid])->all();
        $survey_questions = sizeof($questions);
        if ( empty($questions) ){
            if( file_exists(Yii::$app->params['questions']) ){
                $questions = $question->read(Yii::$app->params['questions'], $surveyid, $userid);
            }else{
                $questions = [new Questions()];
            }
        }

        if ( isset( $_POST['Questions'] ) && sizeof( $_POST['Questions'] ) > sizeof( $questions ) ){
            $diff = sizeof( $_POST['Questions'] ) - sizeof($questions);
            for ($i = 0; $i < $diff; $i++) {
                $new_question = new Questions();
                $new_question->ownerid = $userid; 
                $questions[] = $new_question;
            }

        }
        $likert_5 = Yii::$app->params['likert-5'];
        $likert_7 = Yii::$app->params['likert-7'];
        $fields = array_values ( array_keys ( Questions::attributeLabels() ) );
        $excluded = [ 'id', 'created', 'ownerid', 'surveyid', 'answervalues', 'question', 'answertype', 'destroy', 'answer'];
        $colspan = sizeof($fields) - sizeof($excluded);

        if ( Model::loadMultiple($questions, Yii::$app->request->post() ) ){
            if ( Model::validateMultiple($questions) ) {
                foreach ($questions as $key => $question) {
                    $surveytoquestions = new Surveytoquestions();
                    $surveytoquestions->ownerid = $userid;
                    $surveytoquestions->surveyid = $surveyid;
                    $answer_values = [];

                    if ( $question->answertype != 'textInput' ){
                        $num_of_answers = sizeof( preg_grep( "/question-$key-".$question->answertype."-[0-9]-answer/", array_keys( $_POST ) ) );
                        $greped_arr = preg_grep( "/question-$key-".$question->answertype."-[0-9]-answer/", array_keys( $_POST ) );
                        foreach ($greped_arr as $key => $value) {
                            $answer_values[] = array($_POST[str_replace("answer", "value", $value)] => $_POST[$value]);
                        }

                        $question->answervalues = json_encode($answer_values);
                    }else{
                        $question->answervalues = null;
                    }

                    if ( ! $question->destroy ){
                        $question->save();
                        
                        $surveytoquestions->questionid = $question->id;
                        if ( ! Surveytoquestions::find()->where(['questionid' => $question->id, 'surveyid' => $surveyid])->one() ){
                            $surveytoquestions->save();
                        }
                    }else{
                        if ( $question->id ){
                            $surveytoquestions = Surveytoquestions::find()->where(['questionid' => $question->id, 'surveyid' => $surveyid, 'ownerid' => $userid])->one();
                            if( $surveytoquestions->delete() ){
                                $question->delete();
                            }
                            
                        }
                    }
                }
                $questions = Questions::find()->joinWith('surveytoquestions')->where(['surveyid' => $surveyid])->all();
                $survey_questions = sizeof($questions);
                if ( $survey_questions > 0 ){
                    Yii::$app->response->redirect( array( 'site/participants-invite', 'surveyid' => $surveyid));
                }else{
                    Yii::$app->response->redirect( array( 'site/questions-create', 'surveyid' => $surveyid));
                }
                
            }
        }

        $answertypes = ['textInput' => 'Text Input', 'radioList' => 'Radio List', 'Likert-5' => 'Likert-5', 'Likert-7' => 'Likert-7'];

        return $this->render('questionscreate', [
            'surveyid' => $surveyid,
            'fields' => $fields,
            'questions' => $questions,
            'action' => 'generate-participants',
            'message' => $message,
            'excluded' => $excluded,
            'tabs' => $tabs,
            'answertypes' => $answertypes,
            'colspan' => $colspan,
            'likert_5' => $likert_5,
            'likert_7' => $likert_7,
        ]);

    }

    
    public function actionParticipantsInvite()
    {
        date_default_timezone_set("Europe/Athens"); 
        $userid = Yii::$app->user->identity->id;
        $users = User::find()->select(['id', 'username', 'name', 'surname', 'email', 'fields'])->where(['!=', 'username', 'superadmin'])->andWhere(['availability' => 1])->asArray()->all();
        $tabs = Yii::$app->params['tabs'];
        $message = 'Participants';
        $tabs['Campaign']['enabled'] = 1;
        $tabs['Resources']['enabled'] = 1;
        $tabs['Questions']['enabled'] = 1;
        $tabs['Participants']['enabled'] = 1;
        $tabs['Badges']['enabled'] = 1;
        // $tabs['Overview']['enabled'] = 1;
        
        if ( !isset( $_GET['surveyid'] ) ){
            if ( isset( $_GET['r'] ) && $_GET['r'] == 'site/participants-invite'){
                return $this->goHome();
            }
        }else{
            $surveyid = $_GET['surveyid'];

        }

        $survey = Surveys::findOne( $surveyid );
        if ($survey->active || ! in_array( $userid, array_values( $survey->getOwner() ) ) ){
            return $this->goBack();
        }
        $fields = array_filter( explode("&&", $survey->fields) );
        $user_participants = Participatesin::find()->where(['surveyid' => $surveyid, 'owner' => 0])->asArray()->all();
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
                unset($users[$key]);
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
                return $this->redirect(['site/badges-create', 'surveyid' => $surveyid]);
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

    public function actionBadgesCreate(){

        if ( !isset( $_GET['surveyid'] ) ){
            if ( isset( $_GET['r'] ) && $_GET['r'] == 'site/participants-invite'){
                return $this->goHome();
            }
        }else{
            $surveyid = $_GET['surveyid'];
        }



        $survey = Surveys::findOne( $surveyid );
        $userid = Yii::$app->user->identity->id;
        if ($survey->active || ! in_array( $userid, array_values( $survey->getOwner() ) ) ){
            return $this->goBack();
        }
        
        $users = User::find()->select(['id', 'username'])->asArray()->all();
        $tabs = Yii::$app->params['tabs'];
        $options = ['db-load' => 'Load badges from database' , 'user-form' => 'Insert your own badges'];
        $option = 'user-form';
        $message = 'Badges';
        $tabs['Campaign']['enabled'] = 1;
        $tabs['Resources']['enabled'] = 1;
        $tabs['Questions']['enabled'] = 1;
        $tabs['Participants']['enabled'] = 1;
        $tabs['Badges']['enabled'] = 1;
        $tabs['Overview']['enabled'] = 1;
        $use_badges = true;
        $badges = Badges::find()->joinWith('surveytobadges')->where(['surveyid' => $surveyid])->all();

       
        
        if ( sizeof($badges) == 0 ){
            $badge = new Badges();
            $badge->name = "badge-1";
            $badges = [ $badge ];
        }


        
        
        if ( Yii::$app->request->post() ){

            if ( !isset($_POST['badges-used']) ){
                $survey->badgesused = 0;
                if ( Surveytobadges::find()->where(['surveyid' => $surveyid])->all() ){
                    Surveytobadges::deleteAll(['surveyid' => $surveyid]);
                }
                $survey->save();
                return $this->redirect(['site/survey-overview', 'surveyid' => $surveyid]);

            }else{
                $survey->badgesused = 1;
                $survey->save();
            }
            if ( isset($_POST['resources-function'] ) ){
                $option = $_POST['resources-function'];
                if ( $option == 'db-load' ){
                    $badges = Badges::find()->joinWith('surveytobadges')->where(['allowusers' => 1])->orWhere(['badges.ownerid' => $userid])->all();
                }else{
                    $badges = Badges::find()->joinWith('surveytobadges')->where(['surveyid' => $surveyid])->all();
                    if ( isset($_POST['Badges']) ){
                        if ( sizeof($_POST['Badges']) > sizeof ($badges) ){
                            $diff = sizeof($_POST['Badges']) - sizeof ($badges);
                            if ( ! isset( $_POST['submit-resource-form'] ) ){
                                $diff = 0;
                            }
                            for ($i=0; $i < $diff; $i++) { 
                                $badge = new Badges();
                                $badges[] = $badge; 
                            }
                            if ( sizeof ( $badges ) == 0 ){
                                $badge = new Badges();
                                $badges[] = $badge; 
                            }
                        }
                    }else{
                        $badge = new Badges();
                        $badges[] = $badge;
                    }
                }
            }

            if ( isset( $_POST['submit-resource-form'] ) ){
                $surveytobadges_arr =  isset( $_POST['Surveytobadges'] ) ? $_POST['Surveytobadges'] : [];
                // print_r($_POST);
                if (Model::loadMultiple($badges, Yii::$app->request->post()) ) {
                    $validated = 0;
                    foreach ($badges as $key => $badge) {
                        $surveytobadges = new Surveytobadges();

                        if ( $badge->ownerid == '' ){
                            $badge->ownerid = $userid;
                        }
                        

                        if ( $badge->id != '' ){
                            $test = Badges::findOne($badge->id);
                            $badge->image = $test->image;
                        }
                        $badge->allowusers = ( $badge->allowusers != 0 || $badge->allowusers == 'on' ) ? 1 : 0;

                        if ( $badge->name == '' ){
                            $badge->addError('name', 'Name can not be blank.');
                        }
                        if ( empty( UploadedFile::getInstanceByName("Badges[$key][image]") ) ){

                            if ( $badge->id == '' ){
                                $badge->addError('image', 'File input can not be blank.');
                                $validated --;
                            }else{
                                if ( ! $badge->hasErrors() ){
                                    $badge->save();
                                    $validated ++;
                                    $surveytobadges->badgeid = $badge->id;
                                    if ( isset( $_POST['agree-badge-'.$key] ) ){
                                        if ( Surveytobadges::find()->where(['surveyid' => $surveyid, 'ownerid' => $userid, 'badgeid' => $badge->id])->one() ){
                                            $surveytobadges = Surveytobadges::find()->where(['surveyid' => $surveyid, 'ownerid' => $userid, 'badgeid' => $badge->id])->one();
                                        }
                                        $surveytobadges->ownerid = $userid;
                                        $surveytobadges->surveyid = $surveyid;
                                        $surveytobadges->ratecondition = isset( $surveytobadges_arr[$key]['ratecondition'] ) ? $surveytobadges_arr[$key]['ratecondition'] : 0;
                                        $surveytobadges->surveycondition = isset( $surveytobadges_arr[$key]['surveycondition'] ) ? $surveytobadges_arr[$key]['surveycondition'] : 0;
                                        $surveytobadges->save();
                                    }else{
                                        $surveytobadges = Surveytobadges::find()->where(['surveyid' => $surveyid, 'ownerid' => $userid, 'badgeid' => $badge->id])->one();
                                        if ( $surveytobadges ){
                                            $surveytobadges->delete();
                                        }
                                    }
                                }
                                
                            }
                        }else{
                           
                            $badge->image = UploadedFile::getInstanceByName("Badges[$key][image]");
                            if ( $badge->upload() ) {
                                $badge->image = file_get_contents(Yii::$app->params['images'].$badge->image->baseName.'.'.$badge->image->extension);  
                                if ( ! $badge->hasErrors() ){
                                    $badge->save();
                                    $validated ++;
                                    $surveytobadges->badgeid = $badge->id;
                                    if ( isset( $_POST['agree-badge-'.$key] ) ){
                                        $surveytobadges->ownerid = $userid;
                                        $surveytobadges->surveyid = $surveyid;
                                        $surveytobadges->ratecondition = isset( $surveytobadges_arr[$key]['ratecondition'] ) ? $surveytobadges_arr[$key]['ratecondition'] : 0;
                                        $surveytobadges->surveycondition = isset( $surveytobadges_arr[$key]['surveycondition'] ) ? $surveytobadges_arr[$key]['surveycondition'] : 0;
                                        $surveytobadges->save();
                                    }else{
                                        $surveytobadges = Surveytobadges::find()->where(['surveyid' => $surveyid, 'ownerid' => $userid, 'badgeid' => $badge->id])->one();
                                        if ( $surveytobadges ){
                                            $surveytobadges->delete();
                                        }
                                    }
                                }
                                
                                
                            }else{
                                $badge->addError('image', 'File could not be uploaded.');
                                $validated --;
                            }
                        }
                    }
                    // return $this->redirect('index');
                    $option = 'user-form';
                    if ( $validated == sizeof($badges) ){
                        return $this->redirect(['site/survey-overview', 'surveyid' => $surveyid]);                        
                    }else{
                        $badges = Badges::find()->joinWith('surveytobadges')->where(['surveyid' => $surveyid])->all();
                    }
                    
                }
            }
        }
        $surveytobadges_arr = Surveytobadges::find()->where(['surveyid' => $surveyid])->all();
        if ( empty($surveytobadges_arr) ){
            foreach ($badges as $key => $badge) {
                $surveytobadge = new Surveytobadges();
                $surveytobadge->ratecondition = 0;
                $surveytobadge->surveycondition = 0;
                $surveytobadges_arr[] = $surveytobadge;
            }
        }
        if ( sizeof($badges) > sizeof($surveytobadges_arr) ){
            $diff = sizeof($badges) - sizeof($surveytobadges_arr);
            for ($i=0; $i < $diff; $i++) { 
                $surveytobadge = new Surveytobadges();
                $surveytobadge->ratecondition = 0;
                $surveytobadge->surveycondition = 0;
                $surveytobadges_arr[] = $surveytobadge;
            }
        }
        return $this->render('badgescreate', [
                    'badges' => $badges,
                    'surveytobadges_arr' => $surveytobadges_arr,
                    'surveyid' => $surveyid,
                    'survey' => $survey,
                    'userid' => $userid,
                    'action' => 'generate-participants',
                    'message' => $message,
                    'tabs' => $tabs,
                    'options' => $options,
                    'option' => $option,
                    'use_badges' => $use_badges,
                ]);

    }

    public function actionSurveyOverview()
    {
        $tabs = Yii::$app->params['tabs'];
        $tabs['Campaign']['enabled'] = 1;
        $tabs['Resources']['enabled'] = 1;
        $tabs['Questions']['enabled'] = 1;
        $tabs['Participants']['enabled'] = 1;
        $tabs['Badges']['enabled'] = 1;
        $tabs['Overview']['enabled'] = 1;
        $message = 'Overview';
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

    public function actionUpload()
    {
        $model = new UploadForm();

        if (Yii::$app->request->isPost) {
            $model->imageFiles = UploadedFile::getInstances($model, 'imageFiles');
            var_dump(UploadedFile::getInstances($model, 'imageFiles'));
            exit(0);
            if ($model->upload()) {
                // file is uploaded successfully
                return;
            }
        }

        return $this->render('upload', ['model' => $model]);
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

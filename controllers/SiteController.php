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
        
        exit(0);

    }

    public function actionIndex()
    {

         
        $searchModel = new SurveysSearch();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        // return $this->redirect(['badges/index']);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);

        // return $this->render('index', ['']);
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
                $tabs = ['Survey' => ['link' => '', 'enabled' => 1]];
            }
        }

        $surveys = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['attributes' => ['participants', 'user', 'starts', 'ends', 'name' , 'created']],
            'pagination' => ['pagesize'=> 10], //->where(['owner' => 1])
        ]);

        // foreach ($query->all() as $key => $value) {
        //     echo $key."<br><br>";
        //     print_r($value);
        //     echo "<br><br>";
        // }
        // NOTIFICATIONS FUNCTIONALITY
        $requests = [];
        $mySurveys = Surveys::find()->joinWith('participatesin')->where(['userid' => Yii::$app->user->identity->id, 'owner' => 1])->asArray()->all();
        foreach ($mySurveys as $key => $value) {
            $participants_requests = Participatesin::find()->where(['surveyid' => $value['id'], 'request' => 0])->asArray()->all();
            foreach ($participants_requests as $value) {
                $requests[] = $value;
            }
        }
        // print_r($requests);
        // exit(0);

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

        $columns = ['surveys.id', 'name', 'starts', 'surveys.created', 'ends','participatesin.*', 'participatesin.id', 'participatesin.owner', 'participatesin.userid', 'user.*', 'user.id' ];
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
            'surveys' => $surveys,
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
                            $user = User::findOne($userid);
                            User::assignRole($userid, 'Rater');
                            if ( ! $user->hasRole(['Rater']) ){
                                
                                $assigned = 'True';
                            }else{
                                $assigned = 'False';
                            }
                            $response->data = ['response' => 'Participant saved.', 'assigned' => $assigned, 'user' => $user];
                            $response->statusCode = 200;
                        }else{
                            $participant = Participatesin::find()->where(['surveyid' => $surveyid])->andWhere(['userid' => $userid])->one();
                            if ( $participant->request == 0 ){
                                $participant->request = 1;
                                $participant->save();
                                $response->data = ['response' => 'User accepted.'];
                                $response->statusCode = 200;
                            }else{
                                $response->data = ['response' => 'User already participates.', 'participant' => $participant];
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
                Surveytobadges::deleteAll(['surveyid' => $survey->id]);
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

                $resource = Resources::find()->joinWith('surveytoresources')->where(['surveyid' => $survey->id])->asArray()->one();
                $questions = Questions::find()->joinWith('surveytoquestions')->where(['surveyid' => $surveyid])->asArray()->all();
                // print_r($questions);
            }

            
        }
        return $this->render('rate.php', ['resource' => $resource, 'questions' => $questions]);
    }

    public function actionRateSubmit()
    {

        print_r($_POST);
        exit(0);
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
            $survey->fields = explode("&&", $survey->fields);
            $tabs['Resources']['enabled'] = 1;
            $tabs['Questions']['enabled'] = 1;
            $tabs['Participants']['enabled'] = 1;
            $tabs['Badges']['enabled'] = 1;
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
                // Yii::$app->response->redirect( array( 'site/resource-create', 'surveyid' => $survey->id));
            }

        }
        // print_r($survey);
        // exit(0);
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

        }

        $tabs = Yii::$app->params['tabs'];
        
        $tabs['Campaign']['enabled'] = 1;
        $tabs['Resources']['enabled'] = 1;
        $tabs['Questions']['enabled'] = 1;
        $tabs['Participants']['enabled'] = 1;
        $tabs['Badges']['enabled'] = 1;
        
        $message = 'Resources';

        $options = ['db-load' => 'Load Collections from database' , 'dir-load' => 'Load Collections from directory' ]; //'dir-load' => 'Load resources from directory' 'user-form' => 'Insert your own resources', 

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

        $option = 'db-load';
        $resource_types_option = 'article';

        
        $collections = Collection::find()->all(); //->joinWith('surveytocollections')->where(['surveyid' => $surveyid])
        $tool = "block";
        $resources = [];
        if ( empty( $collections ) ){
            $resource_types_option = 'image';
            $option = "dir-load";
            $collection = new Collection();
            $collection->userid = $userid;
            $collection->name = '';
            $collections = [ $collection ];

            $resource = new Resources();
            $resources = $resource->read( $userid, $resource_types_option );

        }else{

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
            
                $collections = Collection::find()->joinWith('resources')->where(['type' => $resource_types_option])->all();
            
            }else{

                $collection = new Collection();
                $collection->userid = $userid;
                $collection->name = '';
                $collections = [ $collection ];

                if ( $option == 'dir-load' ){
                    $resource = new Resources();
                    $resources = $resource->read( $userid, $resource_types_option );
                }
            }

            if ( isset( $_POST['submit-resource-form'] ) ){
                $new_collection_name = isset( $_POST['Collection']['name'] ) ? $_POST['Collection']['name'] : '';
                if ( Model::loadMultiple( $collections, Yii::$app->request->post() ) && Model::loadMultiple( $resources, Yii::$app->request->post() ) ){
                    // CASE WHERE USER UPLOADS HIS OWN
                    
                    
                    foreach ($collections as $collection) {
                        // echo "Name: ".$new_collection_name."<br><br>";
                        $collection->name = $new_collection_name;
                        $collection->save();
                        $surveytocollections = new Surveytocollections();
                        $surveytocollections->ownerid = $userid;
                        $surveytocollections->surveyid = $surveyid;
                        $surveytocollections->collectionid = $collection->id;
                        $surveytocollections->save();

                        foreach ( $resources as $resource_key => $resource) {
                            // echo "Resouce: ".$resource_key."<br><br>";
                            if ( $resource->type == 'image' ){
                                // print_r(file_get_contents($resource->image));
                                $resource->image = file_get_contents( $resource->image );
                            }

                            // print_r($resource);
                            // echo "<br><br>";
                            $resource->collectionid = $collection->id;
                            $resource->ownerid = $userid;
                            $resource->save();
                        }
                    }
                }else{
                    $collection = new Collection();
                    $collection->name = $new_collection_name;
                    $collection->userid = $userid;
                    $collection->save();
                    $collection_id = $collection->id;

                    $surveytocollections = new Surveytocollections();
                    $surveytocollections->ownerid = $userid;
                    $surveytocollections->surveyid = $surveyid;
                    $surveytocollections->collectionid = $collection->id;
                    $surveytocollections->save();

                    foreach ($collections as $col_key => $collection) {
                        if ( isset( $_POST['agree-collection-'.$col_key] ) ){
                            // echo "Saving this collection ".$collection->name." as ".$new_collection_name."<br>";
                            foreach ($collection->getResources()->all() as $res_key => $resource) {
                                if ( isset( $_POST['agree-'.$resource->type."-".$res_key] ) ){
                                    echo "Using resource ".$resource->id." of type: ".$resource->type." for collection $new_collection_name<br><br>";
                                    $new_resource = $resource;
                                    $new_resource->id = null;
                                    $new_resource->isNewRecord = true;
                                    $new_resource->collectionid = $collection_id;
                                    $new_resource->save();
                                }
                            }
                        }
                    }
                    // echo "<br><br>";
                    // echo "Did not receive <br>";
                }
                // exit(0);
            }
        }


        // foreach ($collections as $collection) {
        //     echo "Name: ".$collection->name."<br><br>";
        //     print_r($collection->getResources()->asArray()->all());
        //     echo "<br><br>";
        // }

        $new_collection = new Collection();
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
                'new_collection' => $new_collection,
            ]);

    }

    public function actionResourceCreateBkp()
    {
        $userid = Yii::$app->user->identity->id;
        if ( isset( $_GET['surveyid'] ) ){
            $surveyid = $_GET['surveyid'];

        }

        $tabs = Yii::$app->params['tabs'];
        
        $tabs['Campaign']['enabled'] = 1;
        $tabs['Resources']['enabled'] = 1;
        $tabs['Questions']['enabled'] = 1;
        $tabs['Participants']['enabled'] = 1;
        $tabs['Badges']['enabled'] = 1;
        
        $message = 'Resources';

        $options = ['db-load' => 'Load resources from database' , 'user-form' => 'Insert your own resources', 'dir-load' => 'Load resources from directory' ]; //'dir-load' => 'Load resources from directory'

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

        $option = 'user-form';
        $resource_types_option = 'article';

        
        $resources = Resources::find()->joinWith('surveytoresources')->where(['surveyid' => $surveyid])->all();

        if ( empty( $resources ) ){
            $tool = "block";
            $resource_types_option = 'image';
            $option = "user-form";
            $resource = new Resources();
            $resource->type = $resource_types_option;
            $resources[] = $resource;
            // if( file_exists(Yii::$app->params['dataset']) ){
            //     $resources = $resource->read(Yii::$app->params['dataset'], $surveyid, $userid);
            // }
        }else{
            $tool = "block";
            $resource_types_option = $resources[0]['type'];
            $resource_types = [$resource_types_option => ucwords($resource_types_option)];
            $dir_available_resources = [$resource_types_option => ucwords($resource_types_option)];
            $db_available_resources = [$resource_types_option => ucwords($resource_types_option)];
            $option = "user-form";
        }
        
        if ( Yii::$app->request->post() ){

            if ( isset($_POST['resources-function'], $_POST['resources-type']) ){
                $option = $_POST['resources-function'];
                $resource_types_option = $_POST['resources-type'];
                if ( $option == 'db-load' ){
                    $resources = Resources::find()->joinWith('surveytoresources')->where(['type' => $resource_types_option, 'allowusers' => 1])->orWhere(['type' => $resource_types_option, 'resources.ownerid' => $userid])->all();
                    // $resources = Resources::find()->joinWith('surveytoresources')->where(['type' => $resource_types_option, 'allowusers' => 1])->orWhere(['type' => $resource_types_option, 'resources.ownerid' => $userid])->asArray()->all();
                    // foreach ($resources as $resource) {
                    //     print_r($resource);
                    //     echo "<br><br>";
                    // }
                    // exit(0);

                }else if( $option == 'user-form' ) {
                    $resources = Resources::find()->joinWith('surveytoresources')->where(['surveyid' => $surveyid])->all();
                    if ( isset($_POST['Resources']) ){
                        if ( sizeof($_POST['Resources']) > sizeof ($resources) ){
                            $diff = sizeof($_POST['Resources']) - sizeof ($resources);
                            if ( ! isset( $_POST['submit-resource-form'] ) ){
                                $diff = 0;
                            }
                            for ($i=0; $i < $diff; $i++) { 
                                $resource = new Resources();
                                $resource->type = $resource_types_option;
                                $resources[] = $resource; 
                            }
                            if ( sizeof ( $resources ) == 0 ){
                                $resource = new Resources();
                                $resource->type = $resource_types_option;
                                $resources[] = $resource; 
                            }
                        }
                    }
                }
            }
            
            if ( isset( $_POST['submit-resource-form'] ) ){
                if ( Model::loadMultiple( $resources, Yii::$app->request->post() ) ){
                    $validated = 0;
                    $collection_name = isset( $_POST['Resources']['collection'] ) ? $_POST['Resources']['collection'] : '';
                   
                    if ( Model::validateMultiple($resources)) {
                        
                        foreach ($resources as $key => $resource) {
                            $resource_types_option = $resource['type'];
                            $surveytoresources = new Surveytoresources();
                            $surveytoresources->surveyid = $surveyid;
                            $surveytoresources->ownerid = $userid;
                            $resource->collection = $collection_name;

                            if ( $resource->ownerid == '' ){
                                $resource->ownerid = $userid;
                            }
                            // echo $resource->allowusers." before <br><br><br>";
                            $resource->allowusers = $allowusers = ( $resource->allowusers != '0' || $resource->allowusers == 'on' ) ? 1 : 0;
                            // echo $resource->allowusers." after <br><br><br>";
                            // exit(0);

                            if ( $resource->type == 'image' ){

                                if ( empty( UploadedFile::getInstanceByName("Resources[$key][image]") ) ){
                                    if ( $resource->id == '' ){
                                        $resource->addError('image', 'File input can not be blank.');
                                        $validated --;
                                    }else{
                                        $resource->save();
                                        $validated ++;
                                        $surveytoresources->resourceid = $resource->id;
                                    }
                                }else{
                                    $validated ++;
                                    $resource->image = UploadedFile::getInstanceByName("Resources[$key][image]");
                                    if ( $resource->upload() ) {
                                        $resource->image = file_get_contents(Yii::$app->params['images'].$resource->image->baseName.'.'.$resource->image->extension);  
                                        $resource->save();
                                        $validated ++;
                                        $surveytoresources->resourceid = $resource->id;
                                        
                                    }else{
                                        $resource->addError('image', 'File could not be uploaded.');
                                        $validated --;
                                    }
                                }
                                
                            }else{
                                if ( isset($resource->id) && $resource->id != '' ){
                                    $resource = Resources::findOne($resource->id);
                                }
                                $resource->allowusers = isset($allowusers) ? $allowusers : $resource->allowusers;
                                $resource->collection = $collection_name;
                                $resource->save();
                                $validated ++;
                                $surveytoresources->resourceid = $resource->id;
                                
                            }
                            
                            if ( isset( $surveytoresources->resourceid ) && $surveytoresources->resourceid != '' ){
                                if ( ! Surveytoresources::find()->where(['surveyid' => $surveyid, 'ownerid' => $userid, 'resourceid' => $resource->id])->one() ){
                                    if ( isset( $_POST['agree-'.$resource->type.'-'.$key] ) ){
                                        $surveytoresources->save();  
                                    }
                                }else{
                                    if ( ! isset( $_POST['agree-'.$resource->type.'-'.$key] ) ){
                                        $surveytoresources = Surveytoresources::find()->where(['surveyid' => $surveyid, 'ownerid' => $userid, 'resourceid' => $resource->id])->one();
                                        $surveytoresources->delete();
                                        unset($resources[$key]);
                                    }
                                }
                            }
                        }
                        $option = 'user-form';
                        $resources = Resources::find()->joinWith('surveytoresources')->where(['surveyid' => $surveyid])->all();
                        if ( empty( $resources ) ){
                            $tool = "block";
                            $resource_types_option = 'article';
                            $resource_types = Yii::$app->params['resources_allowlist'];
                            $option = "user-form";
                            $resource = new Resources();
                            $resource->type = $resource_types_option;
                            $resources[] = $resource;
                        }else{
                            Yii::$app->response->redirect( array( 'site/questions-create', 'surveyid' => $surveyid));
                            $resource_types = [$resource_types_option => ucwords($resource_types_option)];
                            $dir_available_resources = [$resource_types_option => ucwords($resource_types_option)];
                            $db_available_resources = [$resource_types_option => ucwords($resource_types_option)];
                        }  
                    }                  
                }
            }
        }

        $collections = Collection::find()->asArray()->all();

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
                'resources' => $resources,
                'userid' => $userid,
                'collections' => array_column($collections, 'collection')
            ]);

    }

    public function actionQuestionsCreateNew(){
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
            
        }
        $questions = Questions::find()->joinWith('surveytoquestions')->where(['surveyid' => $surveyid])->all();

        // if ( empty($questions) ){
        //     if( file_exists(Yii::$app->params['questions']) ){
        //         $questions = $question->read(Yii::$app->params['questions'], $surveyid, $userid);
        //     }else{
        //         $questions = [new Questions()];
        //     }
        // }

        $questions = [new Questions()];

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
                    echo $key, " question type: ".$question->answertype."<br><br>";
                    if ( $question->answervalues == "" ){
                        if (  $question->answertype == 'Likert(5)' ){
                            for ($i=0; $i < 5; $i++) { 
                                $answer_values[$_POST['question-'.$key.'-likert-5-'.$i.'-value']] = $_POST['question-'.$key.'-likert-5-'.$i.'-answer'];
                            }
                            $question->answervalues = json_encode($answer_values);
                        }else if (  $question->answertype == 'Likert(7)' ){
                            for ($i=0; $i < 7; $i++) { 
                                $answer_values[$_POST['question-'.$key.'-likert-7-'.$i.'-value']] = $_POST['question-'.$key.'-likert-7-'.$i.'-answer'];
                            }
                            $question->answervalues = json_encode($answer_values);
                        }else{
                            $question->answervalues = null;
                        }
                        
                    }
                    if ( ! $question->destroy ){
                        $question->save(false);
                        
                        $surveytoquestions->questionid = $question->id;
                        $surveytoquestions->save();
                    }else{
                        if ( $question->id ){
                            $question->delete();
                            $surveytoquestions = Surveytoquestions::findOne($question->id);
                            $surveytoquestions->delete();
                        }
                    }
                }

                Yii::$app->response->redirect( array( 'site/participants-invite', 'surveyid' => $surveyid));
            }
        }
        $answertypes = ['textInput' => 'Text Input', 'radioList' => 'Radio List', 'Likert(5)' => 'Likert(5)', 'Likert(7)' => 'Likert(7)'];
        // 
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
            
        }
        $questions = Questions::find()->joinWith('surveytoquestions')->where(['surveyid' => $surveyid])->all();

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
                    echo $key, " question type: ".$question->answertype."<br><br>";
                    if ( $question->answervalues == "" ){
                        if (  $question->answertype == 'Likert(5)' ){
                            for ($i=0; $i < 5; $i++) { 
                                $answer_values[$_POST['question-'.$key.'-likert-5-'.$i.'-value']] = $_POST['question-'.$key.'-likert-5-'.$i.'-answer'];
                            }
                            $question->answervalues = json_encode($answer_values);
                        }else if (  $question->answertype == 'Likert(7)' ){
                            for ($i=0; $i < 7; $i++) { 
                                $answer_values[$_POST['question-'.$key.'-likert-7-'.$i.'-value']] = $_POST['question-'.$key.'-likert-7-'.$i.'-answer'];
                            }
                            $question->answervalues = json_encode($answer_values);
                        }else{
                            $question->answervalues = null;
                        }
                        
                    }
                    if ( ! $question->destroy ){
                        $question->save(false);
                        
                        $surveytoquestions->questionid = $question->id;
                        $surveytoquestions->save();
                    }else{
                        if ( $question->id ){
                            $question->delete();
                            $surveytoquestions = Surveytoquestions::findOne($question->id);
                            $surveytoquestions->delete();
                        }
                    }
                }

                Yii::$app->response->redirect( array( 'site/participants-invite', 'surveyid' => $surveyid));
            }
        }
        $answertypes = ['textInput' => 'Text Input', 'radioList' => 'Radio List', 'Likert(5)' => 'Likert(5)', 'Likert(7)' => 'Likert(7)'];
        // 
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

    public function actionQuestionsCreateOld2(){

        $question = new Questions();
        $userid = Yii::$app->user->identity->id;
        $message = 'Questions';
        $tabs = Yii::$app->params['tabs'];
        $tabs['Campaign']['enabled'] = 1;
        // $tabs['Resources']['enabled'] = 1;
        $tabs['Resources']['enabled'] = 1;
        $tabs['Questions']['enabled'] = 1;
        
        if ( !isset( $_GET['surveyid'] ) ){
            if ( isset( $_GET['r'] ) && $_GET['r'] == 'site/participants-invite'){
                return $this->goHome();
            }
        }else{
            $surveyid = $_GET['surveyid'];
            $tabs['Participants']['enabled'] = 1;
            
        }

        if ( Questions::find()->where(['surveyid' => $surveyid])->all() ){
            $questions = Questions::find()->where(['surveyid' => $surveyid])->all();
        }else{
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
                $new_question->surveyid = $surveyid; 
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
                foreach ($questions as $question) {
                    $surveytoquestions = new Surveytoquestions();
                    $surveytoquestions->ownerid = $userid;
                    $surveytoquestions->surveyid = $surveyid;
                    
                    if ( $question->answervalues == "" ){
                        $question->answervalues = null;
                    }
                    if ( ! $question->destroy ){
                        $question->save(false);
                        
                        $surveytoquestions->questionid = $question->id;
                        $surveytoquestions->save();
                    }else{
                        if ( $question->id ){
                            $question->delete();
                            $surveytoquestions = Surveytoquestions::findOne($question->id);
                            $surveytoquestions->delete();
                        }
                    }
                }

                Yii::$app->response->redirect( array( 'site/participants-invite', 'surveyid' => $surveyid));
            }
        }
        $answertypes = ['textInput' => 'Text Input', 'radioList' => 'Radio List'];
        // 

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
        $users = User::find()->select(['id', 'username', 'name', 'surname', 'email', 'fields'])->where(['!=', 'username', 'superadmin'])->asArray()->all();
        $tabs = Yii::$app->params['tabs'];
        $message = 'Participants';
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
        }

        $survey = Surveys::findOne( $surveyid );
        $fields = array_filter( explode("&&", $survey->fields) );
        $user_participants = Participatesin::find()->where(['surveyid' => $surveyid])->asArray()->all();
        $user_invited = Invitations::find()->where(['surveyid' => $surveyid])->asArray()->all();
        
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

            if ( sizeof( array_intersect( $fields, explode("&&", $users[$key]['fields']) ) ) == 0 && sizeof($fields) > 0 && $users[$key]['participates'] == 0 ){
                // IF USERS IN DB HAVE NO REASEARCH FIELDS IN COMMON WITH THE SURVEY FIELDS UNSET THEM

                unset($users[$key]);
                continue;
            }
        }

        foreach ($user_invited as $usr_inv) {
            $users[] = ['userid' => $usr_inv['id'], 'name' => '-', 'surname' => '-', 'email' => $usr_inv['email'], 'participates' => -1];
        }


        // FIND REQUESTED SURVEY
        
        if ( $survey ){
            // IF USER IS OWNER
            if ( $survey->isOwner( $userid )->one() ){
                // echo "User ".$userid." is owner of surveyid: ".$surveyid."<br><br>";
                // $participants->surveyid = $survey->id;
                // return $this->render('dataset', [
                //     'survey' => $survey,
                //     'users' => $users,
                //     'action' => 'generate-participants',
                //     'message' => $message
                // ]);
                // Yii::$app->response->redirect( array( 'site/dataset-create', 'surveyid' => $survey->id));
            }

        }else{

            return $this->goHome();
        
        }

        $users = array_values($users);

        return $this->render('participatesin', [
                    'surveyid' => $surveyid,
                    'survey' => $survey,
                    'users' => $users,
                    'action' => 'generate-participants',
                    'message' => $message,
                    'tabs' => $tabs
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

        $badges = Badges::find()->joinWith('surveytobadges')->where(['surveyid' => $surveyid])->all();

       
        
        if ( sizeof($badges) == 0 ){
            $badge = new Badges();
            $badge->name = "badge-1";
            $badges = [ $badge ];
        }


        
        
        if ( Yii::$app->request->post() ){

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
                        return $this->redirect(['site/surveys-view']);                        
                    }else{
                        $badges = Badges::find()->joinWith('surveytobadges')->where(['surveyid' => $surveyid])->all();
                    }
                    
                }
            }
        }
        $surveytobadges_arr = Surveytobadges::find()->where(['surveyid' => $surveyid])->all();
        if ( empty($surveytobadges_arr) ){
            foreach ($badges as $key => $badge) {
                $surveytobadges_arr[] = new Surveytobadges();
            }
        }
        if ( sizeof($badges) > sizeof($surveytobadges_arr) ){
            $diff = sizeof($badges) - sizeof($surveytobadges_arr);
            for ($i=0; $i < $diff; $i++) { 
                $surveytobadges_arr[] = new Surveytobadges();
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
                    'option' => $option
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
        $message =  isset ( $_GET['tab'] ) ? $_GET['tab'] : 'Purpose';
        $about = Yii::$app->params['about'];

        return $this->render('about', [ 'about' => $about, 'tabs' => $tabs, 'message' => $message ]);
    }
}

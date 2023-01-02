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
use app\models\ResourcesSearch;
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
use yii\helpers\ArrayHelper;

date_default_timezone_set("Europe/Athens"); 

class ResourcesController extends Controller
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

    public function actionResourceEdit(){
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;

        if (Yii::$app->request->isAjax) {
            if( isset($_POST['action'] ,$_POST['resourceId'], $_POST['surveyId'] ) ){
                $action = $_POST['action'];
                $resourceId = intval($_POST['resourceId']);
                $surveyId = intval($_POST['surveyId']);
                $survey = Surveys::find()->where(['id' => $surveyId])->one();
                $userid = Yii::$app->user->identity->id;
                $resourcePublic = '';

                if( !$survey ){
                    $response->data = ['response' => 'Survey not found', 'action' => $action, 'resourceId' => $resourceId, 'surveyId' => $surveyId];
                    $response->statusCode = 404;
                    return $response;
                }

                if( ! in_array( $userid, array_values( $survey->getOwner() ) ) ){
                    $response->data = ['response' => 'User unauthorized', 'action' => $action, 'resourceId' => $resourceId, 'surveyId' => $surveyId];
                    $response->statusCode = 401;
                    return $response;
                }

                if( $survey->active == 1 ){
                    $response->data = ['response' => 'Survey active', 'action' => $action, 'resourceId' => $resourceId, 'surveyId' => $surveyId, 'survey_active' => $survey->active, 'survey_name' => $survey->name, 'survey_id' => $survey->id];
                    $response->statusCode = 404;
                    return $response;
                }

                $resource = Resources::find()->where(['id' => $resourceId])->one();

                if( ! $resource ){
                    $response->data = ['response' => 'Resource not found', 'action' => $action, 'resourceId' => $resourceId, 'surveyId' => $surveyId];
                    $response->statusCode = 404;
                    return $response;
                }

                $message = '';

                if( $action == 'delete' ){

                    $resource->delete();

                }else if ( $action == 'modify' ){

                    if ( isset($_POST['resourceTitle'] ) ){
                        $resourceTitle = $_POST['resourceTitle'];
                        $resource->title = $resourceTitle;
                    }

                    if ( isset($_POST['resourceAbstract'] ) ){
                        $resourceAbstract = $_POST['resourceAbstract'];
                        $resource->abstract = $resourceAbstract;
                    }

                    if ( isset($_POST['resourceText'] ) ){
                        $resourceText = $_POST['resourceText'];
                        $resource->text = $resourceText;
                    }

                    if ( isset($_POST['resourceYear'] ) ){
                        $resourceYear = $_POST['resourceYear'];
                        $resource->year = $resourceYear;
                    }

                    if ( isset($_POST['resourcePmc']) ){
                        $resourcePmc = $_POST['resourcePmc'];
                        $resource->pmc = $resourcePmc;
                    }

                    if ( isset($_POST['resourceDoi']) ){
                        $resourceDoi = $_POST['resourceDoi'];
                        $resource->doi = $resourceDoi;
                    }

                    if ( isset($_POST['resourceAuthors']) ){
                        $resourceAuthors = $_POST['resourceAuthors'];
                        $resource->authors = $resourceAuthors;
                    }

                    if ( isset($_POST['resourceJournal']) ){
                        $resourceJournal = $_POST['resourceJournal'];
                        $resource->journal = $resourceJournal;
                    }

                    if ( isset($_POST['resourcePubmedId']) ){
                        $resourcePubmedId = $_POST['resourcePubmedId'];
                        $resource->pubmed_id = $resourcePubmedId;
                    }

                    if ( isset($_POST['resourcePublic']) ){
                        $resourcePublic = $_POST['resourcePublic'];
                        if(  $resourcePublic == false || $resourcePublic == 'false'){
                            $resourcePublic = 0;
                            $message = "Resource allow false";
                        }else{
                            $resourcePublic = 1;
                            $message = "Resource allow true";
                        }
                        $resource->allowusers = $resourcePublic;
                    }

                    $resource->save();

                }

                $response->data = ['response' => $action.' successfull', 'action' => $action, 'resourceId' => $resourceId, 'surveyId' => $surveyId, 'resource_id' => $resource->id, 'survey_id' => $survey->id, 'message' => $message, 'resourcePublic' => $resourcePublic];
                $response->statusCode = 200;
                return $response;
                

            }
        }
    }

    public function actionResourceCreate(){
        $command = escapeshellcmd("python3 json_resource_parser.py 'localhost' 'survannt_test' 'survannt_test_user' 'test' '../data/files/pmc_paper.csv' '2' '5' 'article' 'random' '40'"); //localhost survannt_test survannt_test_user test ../data/files/pmc_paper.csv 2 5 article random 40 
        echo $command."<br><br>";
        // $command = "ls -l ../python";
        exec(escapeshellcmd($command), $out, $ret);

        echo "out: ";
        print_r($out);
        echo "<br><br>ret: ";
        print_r($ret);
        echo "<br><br>";

        $output = shell_exec($command);
        var_dump($output);
        exit(0);
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
        $status = isset($_GET['status']) ? $_GET['status'] : 200;
        $status_message = isset($_GET['status_message']) ? $_GET['status_message'] : '';
        $type = ''; 
        
        $SurveyCollection = Collection::find()->joinWith('surveytocollections')->where(['surveyid' => $survey->id, 'ownerid' => $userid])->one();
        if( ! $SurveyCollection ){
            $SurveyCollection = new Collection();
        }

        $resources = [new Resources()];
        if( isset($_POST['Resources']) ){
            for ($i=0; $i < sizeof($_POST['Resources']) - 1; $i++) { 
                $resources[] = new Resources();
            }
        }

        if ( Yii::$app->request->isPost && $SurveyCollection->load( Yii::$app->request->post() ) ){
            if ( $SurveyCollection->validate() ) {
                $SurveyCollection->userid = $userid;
                $SurveyCollection->save();
                if ( $SurveyCollection ){
                    $surveytocollections = Surveytocollections::find()->where(['surveyid' => $survey->id, 'collectionid' => $SurveyCollection->id, 'ownerid' => $userid])->one();
                    if ( ! $surveytocollections ){
                        $surveytocollections = new Surveytocollections();
                    }
                    $surveytocollections->surveyid = $survey->id;
                    $surveytocollections->collectionid = $SurveyCollection->id;
                    $surveytocollections->ownerid = $userid;
                    $surveytocollections->save();
                }               
            }
            
        }

        if ( Model::loadMultiple($resources, Yii::$app->request->post() ) && isset($SurveyCollection->id) ){
            foreach ($resources as $key => $resource) {
                $resource->ownerid = $userid;
                $resource->collectionid =  $SurveyCollection->id;
                if ( $resource->type == 'image' ){
                    if ( ! empty( UploadedFile::getInstanceByName("Resources[$key][image]") ) ){
                        $resource->image = UploadedFile::getInstanceByName("Resources[$key][image]");
                        if ( $resource->upload() ) {
                            $resource->image = file_get_contents(Yii::$app->params['dir-images'].$resource->image->baseName.'.'.$resource->image->extension);
                        }else{
                            $resource->addError('image', 'Image could not be uploaded');
                        }
                    }else{
                        $resource->addError('image', 'Image not found');
                    }
                }
                $resource->save();
            }
        }
        
        $resources = [new Resources()];
        $paginationResources = [];
        $SurveyResources = [];

        if ( $SurveyCollection &&  ! $SurveyCollection->isNewRecord){

            $SurveyResources = Collection::find()->where(['id' => $SurveyCollection->id])->one()->getResources();

            $paginationResources = new Pagination(['totalCount' => $SurveyResources->count(), 'pageSize'=>10]);

            $SurveyResources = $SurveyResources->offset($paginationResources->offset)->limit($paginationResources->limit)->all();

            if ( $SurveyResources ){
                $type = $SurveyResources[0]['type'];
            }
        }else{
            $SurveyCollection = new Collection();
        }

        $message = 'Resources';
        if ( $type == '' ){
            $resourceTypeOptions = ArrayHelper::map(Resources::find()->select(['type'])->distinct()->asArray()->all(), 'type', 'type');
            $type = 'article';
        }else{
            $resourceTypeOptions = array($type => $type);
        }

        $resourceTypeOptions = array_map('ucfirst', $resourceTypeOptions);
        

        $resourcesSearch = new ResourcesSearch();  //Resources::find()->where(['allowusers' => 1])->orWhere(['ownerid' => $userid]);
        $dbResources = $resourcesSearch->search(Yii::$app->request->queryParams, $type);

        $columns = $dbResources[1];
        $type = $dbResources[2];
        $dbResources = $dbResources[0];
        $zip = new Resources();
        $zip->method = 'import';
        $resourceZip = [$zip];
        if ( $survey->getCollection()->one() ){
            if ( ! $survey->getCollection()->one()->getResources()->all() ){ // BUG
                $resourceTypeOptions = ['article' => 'Article', 'image' => 'Image', 'text' => 'Text', 'questionnaire' => 'Questionnaire'];
            }
        }
        
        $tabs = $this->tabsManagement($message, $survey);

        return $this->render('//site/resourcecreatenew', 
            [
                'tabs' => $tabs, 
                'survey' => $survey, 
                'message' => $message,
                'SurveyCollection' => $SurveyCollection,
                'SurveyResources' => $SurveyResources,
                'dbResources' => $dbResources,
                'resourcesSearch' => $resourcesSearch,
                'resources' => $resources,
                'paginationResources' => $paginationResources,
                'columns' => $columns,
                'type' => $type,
                'status' => $status,
                'status_message' => $status_message,
                'columns_to_show',
                'resourceTypeOptions' => $resourceTypeOptions,
                'resourceZip' => $resourceZip
            ]);
    }

    public function actionResourcesUse(){

        $userid = Yii::$app->user->identity->id;
        $resources = new Resources();

        if ( isset($_POST) ){
            $num_of_answers = sizeof( preg_grep( "/resource-use-[0-9]/", array_keys( $_POST ) ) );
            $resource_ids = preg_grep( "/resource-use-[0-9]/", array_keys( $_POST ) );
            $surveyid = isset( $_POST['surveyId'] ) ? $_POST['surveyId'] : '';
            $survey = Surveys::findOne($surveyid);
            $collection = $survey->getCollection()->one();
            // print_r($resource_ids);

            if( $survey && $collection ){
                // echo "<br><br>";
                
                foreach ($resource_ids as $res) {
                    $resource_id = str_replace("resource-use-", "", $res);
                    // echo $resource_id."<br><br>";
                    $resource = Resources::findOne($resource_id);
                    if ( $resource ){
                        $new_resource = new Resources();
                        $new_resource->attributes = $resource->attributes;
                        $new_resource->id = null;
                        $new_resource->created = null;
                        $new_resource->image = $resource->image;
                        $new_resource->collectionid = $collection->id;
                        $new_resource->ownerid = $userid;
                        // $new_resource->getRule();
                        // print_r($new_resource->getRule('zipFile'));
                        if ( $new_resource->save(false) ){

                        }else{
                            print_r($new_resource->getErrors());
                            echo "<br><br>";
                        }
                    }
                }

                // exit(0);

                Yii::$app->response->redirect( array( 'resources/resource-create', 'surveyid' => $surveyid));

            } 
        }
        // return $this->goBack();
    }

    public function actionResourcesDeleteAll(){

        $userid = Yii::$app->user->identity->id;
        $status = 200;
        $status_message = '';

        if ( isset($_GET['surveyid']) ){
            $survey = Surveys::findOne(escapeshellcmd($_GET['surveyid']));
            if( $survey ){

                if ( in_array( $userid, array_values( $survey->getOwner() ) ) ){

                    // Surveytobadges::deleteAll(['surveyid' => $survey->id]);
                    $collection = $survey->getCollection()->one();

                    if ( $collection ){

                        if( Resources::deleteAll(['collectionid' => $collection->id]) ){

                            $status_message = 'Resources deleted succesfully';
                            $status = 200;

                        }else{
                            $status_message = 'Resources delete failed';
                            $status = 500;
                        }
                        
                        
                    }else{
                        $status_message = 'Collection not found';
                        $status = 404;
                    }

                }else{
                    
                    $status_message = 'User unauthorized';
                    $status = 401;
                }

            }else{
                $status_message = 'Survey not found';
                $status = 404;
            }
        }

        return $this->redirect(['resources/resource-create', 'surveyid' => $survey->id, 'status' => $status, 'status_message' => $status_message] ?: Yii::$app->homeUrl);

    }

    public function actionCollectionDelete(){

        $userid = Yii::$app->user->identity->id;
        $status = 200;
        $status_message = '';

        if ( isset($_GET['surveyid']) ){
            $survey = Surveys::findOne(escapeshellcmd($_GET['surveyid']));
            if( $survey ){

                if ( in_array( $userid, array_values( $survey->getOwner() ) ) ){

                    $collection = $survey->getCollection()->one();

                    if ( $collection ){
                        Surveytocollections::deleteAll(['surveyid' => $survey->id]);
                        Resources::deleteAll(['collectionid' => $collection->id]);
                        $collection->delete();
                        $status_message = 'Collection deleted succesfully';
                        $status = 200;

                    }else{
                        $status_message = 'Collection not found';
                        $status = 404;
                    }

                }else{
                    $status_message = 'User unauthorized survey';
                    $status = 401;
                }

            }else{
                $status_message = 'Survey not found';
                $status = 404;
            }
        }

        return $this->redirect(['resources/resource-create', 'surveyid' => $survey->id, 'status' => $status, 'status_message' => $status_message] ?: Yii::$app->homeUrl);

    }

    public function actionResourcesImport(){

        $resource = new Resources();
        $userid = Yii::$app->user->identity->id;
        if ( isset($_POST['surveyId']) ) {
            $surveyId = $_POST['surveyId'];
            $numAbstracts = isset($_POST['numAbstracts']) && $_POST['numAbstracts'] != '' && $_POST['numAbstracts'] != ' ' ? $_POST['numAbstracts'] : -1;
            $selectionOption = isset($_POST['selectionOption']) ? $_POST['selectionOption'] : 'relevance';

            if (Surveys::findOne($surveyId)) {
                $survey = Surveys::findOne($surveyId);
                $collection = $survey->getCollection()->one();
            }else{
                return $this->goHome();
            }
        }else{
            return $this->goHome();
        }

        if (Yii::$app->request->isPost) {

            $resource->zipFile = UploadedFile::getInstanceByName("Resources[0][zipFile]");
            $status = $resource->uploadZip($userid, $collection->id, 'article', $numAbstracts, $selectionOption);
            if (! in_array(500, $status[0]) ) {
                return $this->redirect(['resources/resource-create', 'surveyid' => $survey->id, 'status' => 200, 'status_message' => $status[1]  ] ?: Yii::$app->homeUrl);
            }else{
                $resource->getErrors();
                return $this->redirect(['resources/resource-create', 'surveyid' => $survey->id, 'status' => 500, 'status_message' => $status[1]  ] ?: Yii::$app->homeUrl);
            }
        }

        // return $this->render('upload', ['model' => $model]);
        
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

}

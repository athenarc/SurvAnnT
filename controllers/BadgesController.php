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

class BadgesController extends Controller
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


    public function actionBadgeEdit(){

        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;

        if (Yii::$app->request->isAjax) {

            if( isset($_POST['action'] ,$_POST['badgeId'], $_POST['surveyId'] ) ){
                $action = $_POST['action'];
                $badgeId = intval($_POST['badgeId']);
                $surveyId = intval($_POST['surveyId']);
                $survey = Surveys::find()->where(['id' => $surveyId])->one();
                $userid = Yii::$app->user->identity->id;
                
                if( !$survey ){

                    $response->data = ['response' => 'Survey not found', 'action' => $action, 'badgeId' => $badgeId, 'surveyId' => $surveyId];
                    $response->statusCode = 404;
                    return $response;
                }

                if( ! in_array( $userid, array_values( $survey->getOwner() ) ) ){
                    $response->data = ['response' => 'User unauthorized', 'action' => $action, 'badgeId' => $badgeId, 'surveyId' => $surveyId];
                    $response->statusCode = 401;
                    return $response;
                }

                if( $survey->active == 1 ){
                    $response->data = ['response' => 'Survey active', 'action' => $action, 'badgeId' => $badgeId, 'surveyId' => $surveyId, 'survey_active' => $survey->active, 'survey_name' => $survey->name, 'survey_id' => $survey->id];
                    $response->statusCode = 404;
                    return $response;
                }

                $badge = Badges::find()->where(['id' => $badgeId])->one();

                if( $badge ){
                    
                    $surveytobadges = Surveytobadges::find()->where(['badgeid' => $badge->id, 'surveyid' => $survey->id])->one();
                    if(!$surveytobadges){
                        
                        $response->data = ['response' => 'Survey to badges not found', 'action' => $action, 'badgeId' => $badgeId, 'surveyId' => $surveyId];
                        $response->statusCode = 404;
                        return $response;
                    } 

                }else{
                    
                    $response->data = ['response' => 'Badge not found', 'action' => $action, 'badgeId' => $badgeId, 'surveyId' => $surveyId];
                    $response->statusCode = 404;
                    return $response;
                
                }
                $message = '';
                if( $action == 'delete' ){

                    $surveytobadges->delete();
                    // $badge->delete();

                }else if ( $action == 'modify' ){
                    
                    if ( isset($_POST['badgeName'] ) ){
                        
                        $badgeName = $_POST['badgeName'];
                        $badge->name = $badgeName;
                        $badge->save();
                        
                    }
                    if ( isset( $_POST['badgeAllowUsers'] ) ){
                        $badgeAllowUsers = $_POST['badgeAllowUsers'];
                        
                        if(  $badgeAllowUsers == false || $badgeAllowUsers == 'false'){
                            $badgeAllowUsers = 0;
                            $message = "badge allow false";
                        }else{
                            $badgeAllowUsers = 1;
                            $message = "badge allow true";
                        }
                        $badge->allowusers = $badgeAllowUsers;
                        $badge->save();
                    }

                    if ( isset( $_POST['badgeRateCondition'] ) ){
                        $badgeRateCondtion = $_POST['badgeRateCondition'];
                        $surveytobadges->ratecondition = $badgeRateCondtion;
                        $surveytobadges->save();
                    }
                    
                }

                $response->data = ['response' => $action.' successfull', 'action' => $action, 'badgeId' => $badgeId, 'surveyId' => $surveyId, 'badge_id' => $badge->id, 'survey_id' => $survey->id, 'badgeAllowUsers' => $_POST['badgeAllowUsers'], 'message' => $message];
                $response->statusCode = 200;
            }
                


        }

    }

    public function actionBadgesDeleteAll(){

        $userid = Yii::$app->user->identity->id;
        if ( isset($_GET['surveyid']) ){
            $survey = Surveys::findOne(escapeshellcmd($_GET['surveyid']));
            if( $survey ){

                if ( in_array( $userid, array_values( $survey->getOwner() ) ) ){

                    Surveytobadges::deleteAll(['surveyid' => $survey->id]);

                }

            }
        }

        return $this->redirect(Yii::$app->request->referrer ?: Yii::$app->homeUrl);

    }


    public function actionCreateNewBadges()
    {   
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;

        if ( Yii::$app->request->isAjax ){
            if ( isset( $_POST['files_length'] ) ){
                $badgesNew = [];
                for ($i=0; $i < $_POST['files_length']; $i++) { 
                    $badge = new Badges();
                    $badgesNew[] = $badge;
                }
                $form = ActiveForm::begin(['options' => ['id'=> 'badges-import', 'enctype' => 'multipart/form-data']]);                
                return $this->renderPartial('//site/badgescreatetable', ['badgesNew' => $badgesNew, 'form' => $form], true);
            }
        }
    }

    public function actionBadgesCreate()
    {

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
        $use_badges = true;
        

        $badgesNew = [new Badges()];
        $badge = new Badges();

        

        if (Yii::$app->request->ispost) {
            if( isset($_POST['new-badges']) && $_POST['new-badges'] == 'new-badges' ){
                $names = isset($_POST['Badges']['name']) ? $_POST['Badges']['name'] : [];
                $allowusers = isset($_POST['Badges']['allowusers']) ? $_POST['Badges']['allowusers'] : [];
                $badge->imageFiles = UploadedFile::getInstances($badge, 'imageFiles');
                foreach ($badge->imageFiles as $key => $value) {
                    $badge = new Badges();
                    $badge->ownerid = $userid;
                    $badge->allowusers = isset( $allowusers[$key] ) ? $allowusers[$key] : 0;
                    $badge->name = isset( $names[$key] ) ? $names[$key] : $value->name ;
                    $badge->size = intval($value->size);
                    $badge->type = $value->type;
                    $badge->image = file_get_contents( $value->tempName );
                    $badge->save();   

                    $surveytobadge = new Surveytobadges();
                    $surveytobadge->ownerid = $userid;
                    $surveytobadge->surveyid = $surveyid;
                    $surveytobadge->badgeid = $badge->id;
                    $surveytobadge->ratecondition = 0;  
                    $surveytobadge->save();           
                }
            }else{
                $badges_to_use = preg_grep( "/agree-badge-[0-9]+/", array_keys( $_POST ) );
                foreach ($badges_to_use as $key => $value) {
                    $new_badge_id = str_replace("agree-badge-", "", $value);
                    $newBadge = new Badges();
                    $badge = Badges::findOne(escapeshellcmd($new_badge_id));
                    if( $badge ){
                        $newBadge->attributes = $badge->attributes;
                        $newBadge->ownerid = $userid;
                        $newBadge->image = $badge->image;

                        if( $newBadge->save()){
                            if( !Surveytobadges::find()->where(['badgeid' => $newBadge->id, 'surveyid' => $survey->id])->one() ){
                                $surveytobadge = new Surveytobadges();
                                $surveytobadge->ownerid = $userid;
                                $surveytobadge->surveyid = $survey->id;
                                $surveytobadge->badgeid = $newBadge->id;
                                $surveytobadge->ratecondition = 0;  
                                $surveytobadge->save(); 
                            }
                        }
                    }
                    
                }
                $badges_to_use_mods = preg_grep( "/rate-condition-[0-9]+/", array_keys( $_POST ) );
                $badges_allow_users = preg_grep( "/allowusers-[0-9]+/", array_keys( $_POST ) );
                $badges_name = preg_grep( "/badge-name-[0-9]+/", array_keys( $_POST ) );
                foreach ($badges_to_use_mods as $key => $value) {
                    $new_badge_id = str_replace("rate-condition-", "", $value);
                    $new_badge = Badges::find($new_badge_id);
                    if( Surveytobadges::find()->where(['badgeid' => $new_badge_id, 'surveyid' => $survey->id])->one() ){
                        $surveytobadge = Surveytobadges::find()->where(['badgeid' => $new_badge_id, 'surveyid' => $survey->id])->one();
                        $surveytobadge->ratecondition = $_POST[$value];
                        $surveytobadge->save();
                    }
                }

            }
        }

        $myBadges = [];
        $myBadges = $clone = Badges::find()->joinWith('surveytobadges')->where(['surveyid' => $survey->id]);
        $cloneMyBadges = clone $myBadges;

        $badges = Badges::find()->where(['allowusers' => 1])->orWhere(['ownerid' => $userid])->andWhere(['not in','badges.id',array_column( $cloneMyBadges->asArray()->all(), 'id')]); // 

        $paginationMyBadges = new Pagination(['totalCount' => $myBadges->count(), 'pageSize'=>10]);
        $myBadges = $myBadges->offset($paginationMyBadges->offset)->limit($paginationMyBadges->limit)->all();
        
        

        $pagination = new Pagination(['totalCount' => $badges->count(), 'pageSize'=>10]);
        $badges = $badges->all(); //->offset($pagination->offset)->limit($pagination->limit)

        $surveytobadges_arr = Surveytobadges::find()->where(['surveyid' => $surveyid])->all();
        
        $message = 'Badges';
        $tabs = $this->tabsManagement($message, $survey);

        // echo sizeof($myBadges);
        // exit(0);
        return $this->render('//site/badgescreate', [
                    'badge' => $badge,
                    'badges' => $badges,
                    'myBadges' => $myBadges,
                    'badgesNew' => $badgesNew,
                    'surveytobadges_arr' => $surveytobadges_arr,
                    'pagination' => $pagination,
                    'paginationMyBadges' => $paginationMyBadges,
                    'survey' => $survey,
                    'userid' => $userid,
                    'message' => $message,
                    'tabs' => $tabs,
                    'use_badges' => $use_badges,
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

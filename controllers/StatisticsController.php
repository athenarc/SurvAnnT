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

class StatisticsController extends Controller
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

    public function actionCampaignStatistics(){

        if(isset($_GET['surveyid'])){
            $surveyid = $_GET['surveyid'];
            $survey = Surveys::findOne($surveyid);
            $userid = Yii::$app->user->identity->id;
            if( $survey ){
                
                if( $survey->getRates()->count() == 0 ){
                    return $this->goBack();
                }else{
                    $survey->createCsv($survey, $userid);
                }
                
            }
        }

    }

    public function actionCampaignStatisticsAll(){


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

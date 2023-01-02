<?php

namespace app\models;
use yii\base\Model;
use webvimark\modules\UserManagement\models\User;
use yii\web\UploadedFile;
use Yii;
date_default_timezone_set("Europe/Athens"); 
/**
 * This is the model class for table "resources".
 *
 * @property int $id
 * @property int|null $ownerid
 * @property string $created
 * @property string $type
 * @property string|null $text
 * @property string|null $title
 * @property string|null $abstract
 * @property resource|null $image
 * @property resource|null $zipFile
 * @property string|null $pmc
 * @property string|null $doi
 * @property int|null $pubmed_id
 * @property string|null $authors
 * @property string|null $journal
 * @property string|null $year
 * @property int|null $allowusers
 * @property string $collection
 *
 * @property Collection $collection
 * @property User $owner
 * @property Rate[] $rates
 * @property Surveytoresources[] $surveytoresources
 */
class Resources extends \yii\db\ActiveRecord
{

    public $agree = true;
    public $zipFile;
    public $method = '';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'resources';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ownerid', 'pubmed_id', 'allowusers'], 'integer'],
            [['created', 'year', 'id', 'relationalid', 'agree', 'method'], 'safe'],
            [['created'], 'default', 'value' => date('Y-m-d H:i:s', time())],
            [['type'], 'required'],
            [['text', 'abstract', 'authors'], 'string'],
            [['type'], 'string', 'max' => 20],
            [['title'], 'string', 'max' => 500],
            [['pmc', 'doi'], 'string', 'max' => 40],
            [['journal'], 'string', 'max' => 100],
            [['ownerid'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['ownerid' => 'id']],
            [['collectionid'], 'exist', 'skipOnError' => true, 'targetClass' => Collection::className(), 'targetAttribute' => ['collectionid' => 'id']],
            [['image'], 'file', 'extensions' => 'png, jpg, jpeg', 'skipOnEmpty' => true ],
            [['zipFile'], 'file', 'extensions' => 'rar, zip, tar'],
            ['zipFile', 'required', 'when' => function ($model) {
                    return $model->method == 'import';
                }, 'whenClient' => "function (attribute, value) {
                    var counter = attribute.id.match(/\d+/);
                    return $('#resource-method-' + counter).val() == 'import';
                }"
            ],
            ['title', 'required', 'when' => function ($model) {
                    return $model->type == 'article' || $model->type == 'questionnaire' || $model->type == 'text' ;
                }, 'whenClient' => "function (attribute, value) {
                    var counter = attribute.id.match(/\d+/);
                    return $('#resource-type-' + counter).val() == 'article' || $('#resource-type-' + counter).val() == 'questionnaire' || $('#resource-type-' + counter).val() == 'text';
                }"
            ],
            ['image', 'required', 'when' => function ($model) {
                    return $model->type == 'image' ;
                }, 'whenClient' => "function (attribute, value) {
                    var counter = attribute.id.match(/\d+/);
                    return $('#resource-type-' + counter).val() == 'image';
                }"
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */


     public function upload()
    {
        
        if ( $this->validate() && ! empty($this->image) ) {
            $this->image->saveAs( Yii::$app->params['dir-images'] . $this->image->baseName . '.' . $this->image->extension);
            // echo "Image path: ".Yii::$app->params['dir-images'] . $this->image->baseName . '.' . $this->image->extension. "<br><br>";
            return true;
        } else {
            if ( $this->id == null ){
                $this->addError('image' , 'Image can not be blank.');
                return false;
            }else{
                $this->addError('image' , 'Image exists.');
                return true;
            }
        }
    }

    public function uploadZip($userid, $collectionid, $type, $numAbstracts = -1, $selectionOption = 'relevance')
    {
 
        $file_path = Yii::$app->params['dir-files'] . $this->zipFile->baseName . '.' . $this->zipFile->extension;
        $this->zipFile->saveAs($file_path);

        if ( in_array( $this->zipFile->extension, ['zip', 'tar', 'rar']  ) && file_exists( $file_path ) ){
            $zip = new \ZipArchive();
            $zip->open( $file_path );
            $includedFiles = [];
            for( $i = 0; $i < $zip->numFiles; $i++ ){ 
                $stat = $zip->statIndex( $i ); 
                array_push( $includedFiles, Yii::$app->params['dir-files'].basename( $stat['name'] ) );
            }
            $zip->extractTo(Yii::$app->params['dir-files']);
            $zip->close();
        }else{
            array_push( $includedFiles, $file_path);
        }

        $host_db = explode(";", Yii::$app->db->dsn);
        $host = explode("=", $host_db[0])[1];
        $db = explode("=", $host_db[1])[1];
        $user = Yii::$app->db->username;
        $password = Yii::$app->db->password;
        $script_loc = Yii::$app->params['dir-python'].'json_resource_parser.py';
        $status = [];
        $messages = [];
        foreach ($includedFiles as $file) {
           
            exec("python3 $script_loc $host $db $user $password $file $userid $collectionid $type $selectionOption $numAbstracts", $output, $retval);
            if ( in_array( 'Import successfull', $output ) ){
                $status[$file] = 200;
            }else{
                $status[$file] = 500;
                $messages[$file] = "python3 $script_loc $host $db $user $password $file $userid $collectionid $type $selectionOption $numAbstracts";
            }
        }
        return [$status, $messages];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ownerid' => 'Ownerid',
            'created' => 'Created',
            'type' => 'Type',
            'text' => 'Text',
            'title' => 'Title',
            'abstract' => 'Abstract',
            'image' => 'Image',
            'pmc' => 'PMC ID',
            'doi' => 'DOI ID',
            'pubmed_id' => 'PUBMED ID',
            'authors' => 'Authors',
            'journal' => 'Journal',
            'year' => 'Year',
            'allowusers' => 'Public',
            'collection' => 'Collection Name',
            'zipFile' => 'Compressed File'
        ];
    }

    /**
     * Gets query for [[Collection]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCollection()
    {
        return $this->hasOne(Collection::className(), ['id' => 'collectionid']);
    }

    /**
     * Gets query for [[Owner]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOwner()
    {
        return $this->hasOne(User::className(), ['id' => 'ownerid']);
    }
    /**
     * Gets query for [[Surveytoresources]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSurveytoresources()
    {
        return $this->hasMany(Surveytoresources::className(), ['resourceid' => 'id']);
    }

    public function getSurveys()
    {
        return $this->hasMany(Surveys::className(), ['id' => 'surveyid'])->viaTable('surveytoresources', ['resourceid' => 'id']);
    }

    public function read( $userid, $resource_types_option){

        $dir = Yii::$app->params['dir-'.$resource_types_option."s"];
        $resources = [];
        if( ! file_exists(Yii::$app->params['dir-'.$resource_types_option."s"]) ){
            return $resources;
        }
        if ( $resource_types_option == 'image' ){
            $images = array_diff( scandir( $dir ), array('.', '..', '.gitkeep'));
            foreach ($images as $image) {
                $resource = new Resources();
                $resource->type = $resource_types_option;
                $resource->ownerid = $userid;
                $resource->image = Yii::$app->params['dir-'.$resource_types_option."s"].$image;
                $resources[] = $resource;
            }
        }else{
            $string = file_get_contents($dir);
            $string = json_decode($string, true);
            
            foreach ($string[$resource_types_option] as $key => $value) {
                $resource = new Resources();
                $resource->ownerid = $userid;
                $resource->type = $resource_types_option;
                foreach ($value as $abstract_field => $abstract_value) {
                    $resource[$abstract_field] = $abstract_value; 
                }
                $resources[$key] = $resource;
            }
        }
        
        
        if ( sizeof($resources) == 0 ){
            $resource = new Resources();
            $resource->type = $resource_types_option;
            $resources = [$resource];
        }
        return $resources;
    }

    /**
     * Gets query for [[Rates]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRates()
    {
        return $this->hasMany(Rate::className(), ['resourceid' => 'id']);
    }

    public function getNumberOfRatings($surveyid)
    {

        $resource = Resources::findOne($this->id);
        $ratings_count = $resource->find()->joinWith(['rates'])->select(['resources.id', 'surveyid', 'count(distinct userid, resourceid) as RatingsCount'])->where(['rate.surveyid' => $surveyid])->groupBy('resources.id')->asArray()->one();
        return ( isset( $ratings_count['RatingsCount'] ) ) ? $ratings_count['RatingsCount'] : 0;
    }
}

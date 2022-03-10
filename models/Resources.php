<?php

namespace app\models;
use yii\base\Model;
use webvimark\modules\UserManagement\models\User;
use yii\web\UploadedFile;
use Yii;

/**
 * This is the model class for table "resources".
 *
 * @property int $id
 * @property int|null $ownerid
 * @property string|null $created
 * @property string $type
 * @property string|null $text
 * @property string|null $title
 * @property string|null $abstract
 * @property resource|null $image
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
            [['created', 'year', 'id', 'relationalid'], 'safe'],
            [['type'], 'required'],
            [['text', 'abstract', 'authors'], 'string'],
            [['type'], 'string', 'max' => 20],
            [['title'], 'string', 'max' => 500],
            [['pmc', 'doi'], 'string', 'max' => 40],
            [['journal'], 'string', 'max' => 100],
            [['ownerid'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['ownerid' => 'id']],
            [['collectionid'], 'exist', 'skipOnError' => true, 'targetClass' => Collection::className(), 'targetAttribute' => ['collectionid' => 'id']],
            // [['image'], 'file', 'skipOnEmpty' => false ],
            [['type', 'text', 'title', 'abstract'], 'validateType'],
            

        ];
    }

    /**
     * {@inheritdoc}
     */

    public function validateType($attribute, $params)
    {

        if ( $this->type == 'text'  ){
            if ( empty( $this->text ) || $this->text == ' ' ){
                $this->addError('text', "Text can not be empty."); 
            } 
        }else if( $this->type == 'article' ){

            if ( empty( $this->title ) || $this->title == ' ' ){
                $this->addError('title', "Title can not be empty.");
            } 

            if ( empty( $this->abstract ) || $this->abstract == ' ' ){
                $this->addError('abstract', "Abstract can not be empty.");
            } 

        }else{
            return true;
        }

    }

     public function upload()
    {

        if ( $this->validate() && ! empty($this->image) ) {
            $this->image->saveAs( Yii::$app->params['images'] . $this->image->baseName . '.' . $this->image->extension);
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
            'pmc' => 'Pmc',
            'doi' => 'Doi',
            'pubmed_id' => 'Pubmed ID',
            'authors' => 'Authors',
            'journal' => 'Journal',
            'year' => 'Year',
            'allowusers' => 'Allowusers',
            'collection' => 'Collection Name',
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

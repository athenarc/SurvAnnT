<?php

namespace app\models;
use yii\base\Model;
use webvimark\modules\UserManagement\models\User;
use yii\web\UploadedFile;
use Yii;
use yii\data\ActiveDataProvider;
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
class ResourcesSearch extends Surveys
{

    public $type = 'article';

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
            [['created', 'year', 'id', 'relationalid', 'agree'], 'safe'],
            [['created'], 'default', 'value' => date('Y-m-d H:i:s', time())],
            [['type'], 'required'],
            [['text', 'abstract', 'authors'], 'string'],
            [['type'], 'string', 'max' => 20],
            [['title'], 'string', 'max' => 500],
            [['pmc', 'doi'], 'string', 'max' => 40],
            [['journal'], 'string', 'max' => 100],
            [['ownerid'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['ownerid' => 'id']],
            [['collectionid'], 'exist', 'skipOnError' => true, 'targetClass' => Collection::className(), 'targetAttribute' => ['collectionid' => 'id']],
            // [['image'], 'file', 'skipOnEmpty' => false ],
            [['type', 'text', 'title', 'abstract'], 'safe'],
            

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
    public function search($params, $type = '', $view = null) {

        $columns = ['id', 'type', 'title', 'abstract', 'pmc', 'doi', 'pubmed_id', 'authors', 'journal', 'year', 'allowusers'];
        $query = Resources::find();
        $type = isset($params['ResourcesSearch']['type']) ? $params['ResourcesSearch']['type'] : $type;
        $title = isset($params['ResourcesSearch']['title']) ? $params['ResourcesSearch']['title'] : '';
        $abstract = isset($params['ResourcesSearch']['abstract']) ? $params['ResourcesSearch']['abstract'] : '';
        $pmc = isset($params['ResourcesSearch']['pmc']) ? $params['ResourcesSearch']['pmc'] : '';
        $pubmed_id = isset($params['ResourcesSearch']['pubmed_id']) ? $params['ResourcesSearch']['pubmed_id'] : '';
        $doi = isset($params['ResourcesSearch']['doi']) ? $params['ResourcesSearch']['doi'] : '';
        $journal = isset($params['ResourcesSearch']['journal']) ? $params['ResourcesSearch']['journal'] : '';
        $authors = isset($params['ResourcesSearch']['authors']) ? $params['ResourcesSearch']['authors'] : '';
        $year = isset($params['ResourcesSearch']['year']) ? $params['ResourcesSearch']['year'] : '';

        if(  $type != '' ){
            if( $type == 'article' ){
                $columns = ['id', 'type', 'title', 'abstract', 'pmc', 'doi', 'pubmed_id', 'authors', 'journal', 'year'];
            }else if( $type == 'text') {
                $columns = ['id', 'type', 'title', 'text'];
            }else if( $type == 'image'){
                $columns = ['id', 'type', 'title', 'image'];
            }else{
                $columns = ['id', 'type', 'title'];
            }
            $query->where(['type' => $type]);
        }

        if ( $title != '' ){
            $query->andWhere(['like', 'title', '%' . $title . '%', false]);;
        }
        if ( $abstract != '' ){
            $query->andWhere(['like', 'abstract', '%' . $abstract . '%', false]);;
        }
        if ( $pmc != '' ){
            $query->andWhere(['like', 'pmc', '%' . $pmc . '%', false]);;
        }
        if ( $pubmed_id != '' ){
            $query->andWhere(['like', 'pubmed_id', '%' . $pubmed_id . '%', false]);;
        }
        if ( $doi != '' ){
            $query->andWhere(['like', 'doi', '%' . $doi . '%', false]);;
        }
        if ( $journal != '' ){
            $query->andWhere(['like', 'journal', '%' . $journal . '%', false]);;
        }


        $dataProvider = new ActiveDataProvider([
            'query' => isset($columns) ? $query->select($columns) : $query,
            'sort' => ['attributes' => ['id', 'owner', 'type', 'title', 'title', 'doi', 'pmc', 'pubmed_id', 'journal', 'authors', 'year']]
        ]);
        
        if (!($this->load($params) && $this->validate())) {
            return [$dataProvider, $columns, $type];
        }
        return [$dataProvider, $columns, $type];
    }
}

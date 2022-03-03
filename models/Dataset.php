<?php

namespace app\models;

use Yii;
use webvimark\modules\UserManagement\models\User;

/**
 * This is the model class for table "dataset".
 *
 * @property int $id
 * @property int|null $surveyid
 * @property int|null $userid
 * @property string $created
 * @property string $title
 * @property string $abstract
 * @property string|null $pmc
 * @property string|null $doi
 * @property int|null $pubmed_id
 * @property string|null $authors
 * @property string|null $journal
 * @property string|null $year
 * @property string $destroy
 *
 * @property Surveys $survey
 * @property User $user
 */
class Dataset extends \yii\db\ActiveRecord
{
    public $destroy = 0;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'dataset';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['surveyid', 'ownerid', 'pubmed_id'], 'integer'],
            [['created', 'year', 'destroy'], 'safe'],
            [['title', 'abstract'], 'required'],
            [['abstract', 'authors'], 'string'],
            [['title'], 'string', 'max' => 500],
            [['pmc', 'doi'], 'string', 'max' => 40],
            [['journal'], 'string', 'max' => 100],
            [['ownerid'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['ownerid' => 'id']],
            [['surveyid'], 'exist', 'skipOnError' => true, 'targetClass' => Surveys::className(), 'targetAttribute' => ['surveyid' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'surveyid' => 'Surveyid',
            'ownerid' => 'Ownerid',
            'created' => 'Created',
            'title' => 'Title',
            'abstract' => 'Abstract',
            'pmc' => 'Pmc',
            'doi' => 'Doi',
            'pubmed_id' => 'Pubmed ID',
            'authors' => 'Authors',
            'journal' => 'Journal',
            'year' => 'Year',
            'destroy' => 'Destroy'
        ];
    }

    /**
     * Gets query for [[Survey]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSurvey()
    {
        return $this->hasOne(Surveys::className(), ['id' => 'surveyid']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'ownerid']);
    }

    public function read($file, $surveyid, $userid){

        $string = file_get_contents($file);
        $string = json_decode($string, true);
        $datasets = [new Dataset()];
        foreach ($string['dataset'] as $key => $value) {
            $dataset = new Dataset();
            $dataset->surveyid = $surveyid;
            $dataset->ownerid = $userid;
            foreach ($value as $abstract_field => $abstract_value) {
                $dataset[$abstract_field] = $abstract_value; 
            }
            $datasets[$key] = $dataset;
        }
        
        return $datasets;
    }
}

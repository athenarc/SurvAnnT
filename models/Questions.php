<?php

namespace app\models;

use Yii;
use webvimark\modules\UserManagement\models\User;

/**
 * This is the model class for table "questions".
 *
 * @property int $id
 * @property int|null $surveyid
 * @property int|null $ownerid
 * @property string $created
 * @property string $question
 * @property string|null $tooltip
 * @property string|null $answer
 * @property string $answertype
 * @property string $answervalues
 * @property int|null $allowusers
 *
 * @property User $owner
 * @property Rate[] $rates
 * @property Surveys $survey
 */
class Questions extends \yii\db\ActiveRecord
{
    public $destroy = 0;
    public $surveyid = 0;
    // public $answertype = 'textInput';
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'questions';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ownerid', 'allowusers'], 'integer'],
            [['created', 'destroy'], 'safe'],
            [['question', 'answertype'], 'required'],
            [['answervalues'], 'string'],
            [['question', 'tooltip', 'answer'], 'string', 'max' => 255],
            [['answertype'], 'string', 'max' => 20],
            [['ownerid'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['ownerid' => 'id']],
            // [['answervalues', 'answertype'], 'validateJson']
        ];
    }

    public function validateJson($attribute, $params)
    {
        // echo $this->id, "<br>==============================<br>";
        // echo $this->answertype,"<br>";
        // $error_message = "";
        // if ( $this->answertype == 'radioList'  ){
                    
        //     if ( empty( $this->answervalues ) || $this->answervalues == ' ' ){

        //         $error_message = "Answer values can not be blank.";
                
        //     }else if ( ! json_decode( $this->answervalues )  ){
                
        //         $error_message = "Answer values not in correct JSON format.";
            
        //     }
        //     if ( $error_message != "" ){
            
        //         $this->addError('answervalues', $error_message);
            
        //     }
            
        // }
        // echo "<br><br>";
        
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
            'question' => 'Question',
            'tooltip' => 'Tooltip',
            'answer' => 'Answer',
            'answertype' => 'Answertype',
            'answervalues' => 'Answervalues',
            'destroy' => 'Destroy',
            'allowusers' => 'Allowusers',
        ];
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
     * Gets query for [[Survey]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSurveytoquestions()
    {
        return $this->hasMany(Surveytoquestions::className(), ['questionid' => 'id']);
    }

    public function getSurvey()
    {
        return $this->hasOne(Surveys::className(), ['id' => 'surveyid']);
    }

    /**
     * Gets query for [[Rates]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRates()
    {
        return $this->hasMany(Rate::className(), ['questionid' => 'id']);
    }

    public function read($file, $surveyid, $userid){

        $string = file_get_contents($file);
        $string = json_decode($string, true);
        $questions = [new Questions()];
        foreach ($string['questions'] as $key => $value) {
            $question = new Questions();
            $question->ownerid = $userid;
            $array = [];
            foreach ($value as $abstract_field => $abstract_value) {
                if ( is_array ( $abstract_value ) && $abstract_field = 'answervalues'){
                    foreach ($abstract_value as $k => $v) {
                        $array[] = array($k => $v);
                    }
                    $question[$abstract_field] = json_encode( $array ); 
                }else{
                    $question[$abstract_field] = $abstract_value; 
                } 
            }
            $questions[$key] = $question;
        }
        return $questions;
    }
}

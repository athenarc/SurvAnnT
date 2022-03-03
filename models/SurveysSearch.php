<?php

namespace app\models;
use webvimark\modules\UserManagement\models\User;
use Yii;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "surveys".
 *
 * @property int $id
 * @property string $name
 * @property string $created
 * @property string|null $starts
 * @property string|null $ends
 * @property int $locked
 * @property string $about
 *
 * @property Participatesin[] $participatesins
 */
class SurveysSearch extends Surveys
{
    /**
     * {@inheritdoc}
     */
    public $participants;
    public $user;
    public $owner;
    public $name;
    public $username;
    public $starts;
    public $ends;
    public $participants_count;
    public $owner_username;

    // now set the rules to make those attributes safe
    public function rules()
    {
        return [
            // ... more stuff here
            [['participants', 'user', 'name', 'username', 'starts', 'ends', 'participants_count', 'owner_username'], 'safe'],
            // ... more stuff here
        ];
    }

    // public function getUser(){
    //     return $this->hasMany(User::className(), ['id' => 'userid'])->via('participatesin', ['userid' => 'id']);
    // }

    public function search($params) {
        // $query = Surveys::find()->innerJoinWith('user', true)->innerJoinWith('participatesin', true);
        $query = Surveys::find(); 
        $query->joinWith(['user'])->select(
            ['surveys.*', 'count(participatesin.surveyid) as participants_count', 
            '(select GROUP_CONCAT(username) from participatesin join user on participatesin.userid = user.id where owner = 1 and participatesin.surveyid = surveys.id and participatesin.request = 1 GROUP BY surveyid) as owner_username'])->groupBy('surveys.id');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort->attributes['user'] = [
            // The tables are the ones our relation are configured to
            // in my case they are prefixed with "tbl_"
            'asc' => ['user.username' => SORT_ASC],
            'desc' => ['user.username' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['participants_count'] = [
            // The tables are the ones our relation are configured to
            // in my case they are prefixed with "tbl_"
            'asc' => ['participants_count' => SORT_ASC],
            'desc' => ['participants_count' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['owner_username'] = [
            // The tables are the ones our relation are configured to
            // in my case they are prefixed with "tbl_"
            'asc' => ['owner_username' => SORT_ASC],
            'desc' => ['owner_username' => SORT_DESC],
        ];

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        if ( $this->owner_username ){
            // print_r(explode(",",$this->owner_username));
            $query->andFilterWhere(['like', 'username', $this->owner_username]);
            $query->andFilterWhere(['owner' => 1]);
        }
        // exit(0);
       
        $query->andFilterWhere(['like', 'user', $this->user])
                ->andFilterWhere(['like', 'ends', $this->ends])
                ->andFilterWhere(['like', 'name', $this->name]);
                

        if (is_numeric($this->participants_count)) {
            $query->having(['>=', 'participants_count', $this->participants_count]);
        }


        // print_r(Surveys::find()->innerJoinWith('user', true)->asArray()->all());
        // exit(0);

        // $res = $query->asArray()->all();
        // foreach ($res as $key => $value) {
        //     echo $value['name'], "<br>";
        //     // if ( isset( $value['participatesin'] ) ){
        //     //     foreach ($value['participatesin'] as $k => $v) {
        //     //         echo " $k => ";
        //     //         print_r($v);
        //     //         echo "<br>";
        //     //     }
        //     // }
            
        //     echo "<br><br>";
        //     print_r($value);
        //     echo "<br><br>";
        // }
        // // print_r($res);
        // exit(0);
        // print_r($query->createCommand()->getRawSql());
        // exit(0);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['attributes' => ['owner', 'owner_username', 'ends', 'name', 'username', 'participants_count']]
        ]);
        return $dataProvider;
        // $this->load($params);
        // if (!$this->validate()) {
        //     // uncomment the following line if you do not want to return any records when validation fails
        //     // $query->where('0=1');
        //     return $dataProvider;
        // }
    }
}

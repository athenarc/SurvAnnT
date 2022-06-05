<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $username
 * @property string $auth_key
 * @property string $password_hash
 * @property string|null $confirmation_token
 * @property int $status
 * @property int|null $superadmin
 * @property int $created_at
 * @property int $updated_at
 * @property string|null $registration_ip
 * @property string|null $bind_to_ip
 * @property string|null $email
 * @property int $email_confirmed
 * @property string $name
 * @property string $surname
 * @property string $fields
 * @property string|null $orcidid
 * @property int $availability
 * @property int $consent_leaderboard
 * @property int $consent_details 
 *
 * @property AuthAssignment[] $authAssignments
 * @property Badges[] $badges
 * @property Collection[] $collections
 * @property AuthItem[] $itemNames
 * @property Leaderboard[] $leaderboards
 * @property Participatesin[] $participatesins
 * @property Questions[] $questions
 * @property Rate[] $rates
 * @property Resources[] $resources
 * @property Surveytobadges[] $surveytobadges
 * @property Surveytocollections[] $surveytocollections
 * @property Surveytoquestions[] $surveytoquestions
 * @property Surveytoresources[] $surveytoresources
 * @property UserVisitLog[] $userVisitLogs
 * @property Usertobadges[] $usertobadges
 */
class UserTest extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['username', 'auth_key', 'password_hash', 'created_at', 'updated_at', 'name', 'surname', 'fields'], 'required'],
            [['status', 'superadmin', 'created_at', 'updated_at', 'email_confirmed', 'availability', 'consent_leaderboard', 'consent_details'], 'integer'],
            [['fields'], 'string'],
            [['username', 'password_hash', 'confirmation_token', 'bind_to_ip'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],
            [['registration_ip'], 'string', 'max' => 15],
            [['email'], 'string', 'max' => 128],
            [['name', 'surname'], 'string', 'max' => 50],
            [['orcidid'], 'string', 'max' => 19],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'auth_key' => 'Auth Key',
            'password_hash' => 'Password Hash',
            'confirmation_token' => 'Confirmation Token',
            'status' => 'Status',
            'superadmin' => 'Superadmin',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'registration_ip' => 'Registration Ip',
            'bind_to_ip' => 'Bind To Ip',
            'email' => 'Email',
            'email_confirmed' => 'Email Confirmed',
            'name' => 'Name',
            'surname' => 'Surname',
            'fields' => 'Fields',
            'orcidid' => 'Orcidid',
            'availability' => 'Availability',
            'consent_leaderboard' => 'Consent Leaderboard',
            'consent_details' => 'Consent Details',
        ];
    }

    /**
     * Gets query for [[AuthAssignments]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuthAssignments()
    {
        return $this->hasMany(AuthAssignment::className(), ['user_id' => 'id']);
    }

    /**
     * Gets query for [[Badges]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBadges()
    {
        return $this->hasMany(Badges::className(), ['ownerid' => 'id']);
    }

    /**
     * Gets query for [[Collections]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCollections()
    {
        return $this->hasMany(Collection::className(), ['userid' => 'id']);
    }

    /**
     * Gets query for [[ItemNames]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getItemNames()
    {
        return $this->hasMany(AuthItem::className(), ['name' => 'item_name'])->viaTable('auth_assignment', ['user_id' => 'id']);
    }

    /**
     * Gets query for [[Leaderboards]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLeaderboards()
    {
        return $this->hasMany(Leaderboard::className(), ['userid' => 'id']);
    }

    /**
     * Gets query for [[Participatesins]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getParticipatesins()
    {
        return $this->hasMany(Participatesin::className(), ['userid' => 'id']);
    }

    /**
     * Gets query for [[Questions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQuestions()
    {
        return $this->hasMany(Questions::className(), ['ownerid' => 'id']);
    }

    /**
     * Gets query for [[Rates]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRates()
    {
        return $this->hasMany(Rate::className(), ['userid' => 'id']);
    }

    /**
     * Gets query for [[Resources]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getResources()
    {
        return $this->hasMany(Resources::className(), ['ownerid' => 'id']);
    }

    /**
     * Gets query for [[Surveytobadges]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSurveytobadges()
    {
        return $this->hasMany(Surveytobadges::className(), ['ownerid' => 'id']);
    }

    /**
     * Gets query for [[Surveytocollections]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSurveytocollections()
    {
        return $this->hasMany(Surveytocollections::className(), ['ownerid' => 'id']);
    }

    /**
     * Gets query for [[Surveytoquestions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSurveytoquestions()
    {
        return $this->hasMany(Surveytoquestions::className(), ['ownerid' => 'id']);
    }

    /**
     * Gets query for [[Surveytoresources]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSurveytoresources()
    {
        return $this->hasMany(Surveytoresources::className(), ['ownerid' => 'id']);
    }

    /**
     * Gets query for [[UserVisitLogs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserVisitLogs()
    {
        return $this->hasMany(UserVisitLog::className(), ['user_id' => 'id']);
    }

    /**
     * Gets query for [[Usertobadges]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsertobadges()
    {
        return $this->hasMany(Usertobadges::className(), ['userid' => 'id']);
    }
}

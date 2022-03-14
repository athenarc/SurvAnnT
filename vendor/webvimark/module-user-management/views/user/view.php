<?php

use webvimark\modules\UserManagement\components\GhostHtml;
use webvimark\modules\UserManagement\models\rbacDB\Role;
use webvimark\modules\UserManagement\models\User;
use webvimark\modules\UserManagement\UserManagementModule;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\DetailView;

/**
 * @var yii\web\View $this
 * @var webvimark\modules\UserManagement\models\User $model
 */

// $this->title = $model->username;
// $this->params['breadcrumbs'][] = ['label' => UserManagementModule::t('back', 'Users'), 'url' => ['index']];
// $this->params['breadcrumbs'][] = $this->title;
?>

<div class="user-view outside-div medium-form">

	<h2 class="lte-hide-title"><?= $this->title ?></h2>

	<div class="panel panel-default ">
		<div class="panel-body">
			<?php if ( Yii::$app->user->identity->hasRole(['superadmin']) ): ?>
			    <p>

				<?= GhostHtml::a(UserManagementModule::t('back', 'Edit'), ['update', 'id' => $model->id], ['class' => 'btn btn-sm btn-primary']) ?>
				<?= GhostHtml::a(UserManagementModule::t('back', 'Create'), ['create'], ['class' => 'btn btn-sm btn-success']) ?>
				<?= GhostHtml::a(
					UserManagementModule::t('back', 'Roles and permissions'),
					['/user-management/user-permission/set', 'id'=>$model->id],
					['class' => 'btn btn-sm btn-default']
				) ?>

				<?= GhostHtml::a(UserManagementModule::t('back', 'Delete'), ['delete', 'id' => $model->id], [
				    'class' => 'btn btn-sm btn-danger pull-right',
				    'data' => [
					'confirm' => UserManagementModule::t('back', 'Are you sure you want to delete this user?'),
					'method' => 'post',
				    ],
				]) ?>
			    </p>
			<?php endif; ?>
			<?= DetailView::widget([
				'model'      => $model,
				'attributes' => [
					[

						'attribute' => 'id',
						'visible'=> User::hasRole('Superadmin'),
					],
					[
						'attribute'=>'status',
						'value'=>User::getStatusValue($model->status),
						'visible'=>User::hasRole('Superadmin'),
					],
					'username',
					[
						'attribute'=>'email',
						'value'=>$model->email,
						'format'=>'email',
						'visible'=>User::hasPermission('viewUserEmail'),
					],
					[
						'attribute'=>'email_confirmed',
						'value'=>$model->email_confirmed,
						'format'=>'boolean',
						'visible'=>User::hasRole('Superadmin'),
					],
					[
						'label'=>UserManagementModule::t('back', 'Roles'),
						'value'=>implode('<br>', ArrayHelper::map(Role::getUserRoles($model->id), 'name', 'description')),
						'visible'=>User::hasRole('Superadmin'),
						'format'=>'raw',
					],
					[
						'attribute'=>'fields',
						'format' => 'ntext',
						'value' => function ($model) {
							$str = '';
							foreach (explode("&&", $model->fields) as $key => $value) {
								$str .= $value."\n";
							}
							return $str;
						}
					],
					[
						'attribute'=>'participates in',
						'format' => 'raw',
						'value' => function ($model) {
							$str = '';
							foreach ($model->getParticipatesin()->joinWith(['survey'])->where(['owner' => 0])->all() as $key => $value) {
								if ( $value->owner == 1 ){
									$str.= '<i class="fa-solid fa-crown" title="Owner"></i>';
								}
								$str .= Html::a($value->survey->name, 'index.php?r=site%2Fsurveys-view&surveyid='.$value->survey->id)."<br>";
								
							}
							return $str;
						}
					],
					[
						'attribute'=>'Runs',
						'format' => 'raw',
						'value' => function ($model) {
							$str = '';
							foreach ($model->getParticipatesin()->joinWith(['survey'])->where(['owner' => 1])->all() as $key => $value) {
								
								$str .= Html::a($value->survey->name, 'index.php?r=site%2Fsurveys-view&surveyid='.$value->survey->id)."<br>";
								
							}
							return $str;
						}
					],
					[
						'attribute'=>'Total Annotations Provided',
						'format' => 'raw',
						'value' => $model->getRates()->groupBy('resourceid')->count()
					],
					[
						'attribute'=>'bind_to_ip',
						'visible'=>User::hasRole('Superadmin'),
					],
					array(
						'attribute'=>'registration_ip',
						'value'=>Html::a($model->registration_ip, "http://ipinfo.io/" . $model->registration_ip, ["target"=>"_blank"]),
						'format'=>'raw',
						'visible'=>User::hasRole('Superadmin'),
					),
					[
						'attribute'=>'created_at',
						'visible'=>User::hasRole('Superadmin'),
					],
					[
						'attribute'=>'updated_at',
						'visible'=>User::hasRole('Superadmin'),
					],
					// 'created_at:datetime',
					// 'updated_at:datetime',
				],
			]) ?>

		</div>
	</div>
</div>

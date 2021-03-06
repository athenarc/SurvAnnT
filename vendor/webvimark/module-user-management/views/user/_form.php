<?php

use webvimark\modules\UserManagement\models\User;
use webvimark\modules\UserManagement\UserManagementModule;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use webvimark\extensions\BootstrapSwitch\BootstrapSwitch;
use kartik\select2\Select2;
use app\models\Fields;
/**
 * @var yii\web\View $this
 * @var webvimark\modules\UserManagement\models\User $model
 * @var yii\bootstrap\ActiveForm $form
 */
?>

<div class="user-form">
	<?php 
	$fields = [];
	foreach (array_column ( Fields::find()->select(['name'])->asArray()->all(), 'name' ) as $f) {
		$fields[$f] = $f;
	}
	?>
	<?php $form = ActiveForm::begin([
		'id'=>'user',
		// 'layout'=>'horizontal',
		'validateOnBlur' => false,
	]); ?>
	
		
	<div class = "row">
		<div class = "col-md-8">
			<?= $form->field($model, 'username')->textInput(['maxlength' => 255, 'autocomplete'=>'off']) ?>
		</div>
		<?php if ( User::hasRole('Superadmin') ): ?>
			<div class = "col-md-4">
				<?= $form->field($model->loadDefaultValues(), 'status')
				->dropDownList(User::getStatusList()) ?>
			</div>
		<?php endif; ?>
	</div>

	<?php if ( $model->isNewRecord ): ?>
		<div class = "row">
			<div class = "col-md-12">
				<?= $form->field($model, 'password')->passwordInput(['maxlength' => 255, 'autocomplete'=>'off']) ?>
			</div>
		</div>
		<div class = "row">
			<div class = "col-md-12">
				<?= $form->field($model, 'repeat_password')->passwordInput(['maxlength' => 255, 'autocomplete'=>'off']) ?>
			</div>
		</div>
	<?php endif; ?>
	<div class = "row">
		<div class = "col-md-12">
			<?= $form->field($model, 'name')->textInput(['maxlength' => 50, 'autocomplete'=>'off', 'autofocus'=>true]) ?>
		</div>
	</div>
	<div class = "row">
		<div class = "col-md-12">
		<?= $form->field($model, 'surname')->textInput(['maxlength' => 50, 'autocomplete'=>'off', 'autofocus'=>true]) ?>
		</div>
	</div>
	<?php if ( User::hasPermission('editUserEmail') ): ?>
		<div class = "row d-flex align-items-end">
			<div class = "col-md-7">
				<?= $form->field($model, 'email')->textInput(['maxlength' => 255]) ?>
			</div>
			<?php if ( User::hasRole('Superadmin') ): ?>
				<div class = "col-md-5">
					<?= $form->field($model, 'email_confirmed')->checkbox() ?>
				</div>
			<?php endif; ?>
		</div>
	<?php endif; ?>

	<?php if ( User::hasRole('Superadmin') ): ?>
		<div class = "row">
			<div class = "col-md-12">
				<?= $form->field($model, 'bind_to_ip')->textInput(['maxlength' => 255])->hint(UserManagementModule::t('back','For example: 168.111.192.12')) ?>
			</div>
		</div>
	<?php endif; ?>

	
	<div class = "row">
		<div class = "col-md-12">
			<?= $form->field($model, 'orcidid')->textInput(['placeholder' => 'xxxx-xxxx-xxxx-xxxx']) ?>
		</div>
    </div>

	<div class = "row">
			<div class = "col-md-12">
				<?= $form->field($model, 'fields')->widget
		            (
		                Select2::className(), 
		                (
		                    [
		                    'name' => 'user-fields-selection',
		                    'data' => $fields,
		                    'theme' => \kartik\select2\Select2::THEME_BOOTSTRAP ,
		                    'options' => [ 'multiple' => true, ],
		                    'pluginOptions' => [ 'allowClear' => true, 'tags' => true ],
		                    ]
		                )
		            ) 
            	?>
        </div>
    </div>

    <div class = "row">
		<div class = "col-md-12">
			<?= $form->field($model, 'availability')->dropDownList([0 => 'No', 1 => 'Yes'], ['value' => 1, 'label' => 'Availability']) ?>
		</div>
	</div>
	<br>
	<div class="row">
		<div class = "col-md-12 text-center">
			<p class="label">I consent to my username, badges and scores being displayed SurvAnnT's leaderboards</p>
			<?= $form->field($model, 'consent_leaderboard')->checkbox(['label' => 'I consent to my username, scores and campaign participations being displayed in public'])->label(false) ?>
		</div>
	</div>

	<div class="row">
		<div class = "col-md-12 text-center">
			<p class="label">I consent to my research interests, campaing participations and annotation statistics being displayed in public</p>
			<?= $form->field($model, 'consent_details')->checkbox(['label' => 'I consent to my username, scores and campaign participations being displayed in public'])->label(false) ?>
		</div>
	</div>
			

	<div class="form-group">
		<div class="col-sm-offset-3 col-sm-12 text-right">
			<?php if ( $model->isNewRecord ): ?>
				<?= Html::submitButton(
					'<span class="glyphicon glyphicon-plus-sign"></span> ' . UserManagementModule::t('back', 'Create'),
					['class' => 'btn btn-success']
				) ?>
			<?php else: ?>
				<?= Html::submitButton(
					'<span class="glyphicon glyphicon-ok"></span> ' . UserManagementModule::t('back', 'Save'),
					['class' => 'btn btn-primary']
				) ?>
			<?php endif; ?>
		</div>
	</div>

	<?php ActiveForm::end(); ?>

</div>

<?php BootstrapSwitch::widget() ?>
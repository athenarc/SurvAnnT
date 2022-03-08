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
	
		
	
	<?php if ( User::hasRole('Admin', 'Superadmin') ): ?>
		<div class = "row">
			<?= $form->field($model->loadDefaultValues(), 'status')
				->dropDownList(User::getStatusList()) ?>
		</div>
	<?php endif; ?>
	
	<div class = "row">
		<?= $form->field($model, 'username')->textInput(['maxlength' => 255, 'autocomplete'=>'off']) ?>
	</div>

	<div class = "row">
		<?= $form->field($model, 'availability')->checkbox(['label' => 'Availability'])->label(true) ?>
	</div>

	<?php if ( $model->isNewRecord ): ?>
		<div class = "row">
		<?= $form->field($model, 'password')->passwordInput(['maxlength' => 255, 'autocomplete'=>'off']) ?>
		</div>
		<div class = "row">
		<?= $form->field($model, 'repeat_password')->passwordInput(['maxlength' => 255, 'autocomplete'=>'off']) ?>
		</div>
	<?php endif; ?>


	<?php if ( User::hasPermission('bindUserToIp') ): ?>
		<div class = "row">
		<?= $form->field($model, 'bind_to_ip')
			->textInput(['maxlength' => 255])
			->hint(UserManagementModule::t('back','For example: 123.34.56.78, 168.111.192.12')) ?>
		</div>
	<?php endif; ?>

	<?php if ( User::hasPermission('editUserEmail') ): ?>

		<?= $form->field($model, 'email')->textInput(['maxlength' => 255]) ?>
		<?= $form->field($model, 'email_confirmed')->checkbox() ?>

	<?php endif; ?>

	<?= $form->field($model, 'name')->textInput(['maxlength' => 50, 'autocomplete'=>'off', 'autofocus'=>true]) ?>

	<?= $form->field($model, 'surname')->textInput(['maxlength' => 50, 'autocomplete'=>'off', 'autofocus'=>true]) ?>

	<?= $form->field($model, 'orcidid')->textInput(['placeholder' => 'xxxx-xxxx-xxxx-xxxx']) ?>

	<?= $form->field($model, 'fields')->widget
            (
                Select2::className(), 
                (
                    [
                    'name' => 'user-fields-selection',
                    'data' => $fields,
                    'options' => [ 'multiple' => true ],
                    'pluginOptions' => [ 'allowClear' => true, 'tags' => true ],
                    ]
                )
            ) 
            ?>
			

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
<?php

use webvimark\modules\UserManagement\UserManagementModule;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;
use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var webvimark\modules\UserManagement\models\forms\PasswordRecoveryForm $model
 */

// $this->title = UserManagementModule::t('front', 'Password recovery');
// $this->params['breadcrumbs'][] = $this->title;
?>

<div class="password-recovery outside-div" style = "width: 40%; padding: 3%;">

	<h2 class="text-center"><?= $this->title ?></h2>

	<?php if ( Yii::$app->session->hasFlash('error') ): ?>
		<div class="alert-alert-warning text-center">
			<?= Yii::$app->session->getFlash('error') ?>
		</div>
	<?php endif; ?>

	<?php $form = ActiveForm::begin([
		'id'=>'user',
		'validateOnBlur'=>false,
	]); ?>
	<div class="row">
		<div class="col-md-12">
			<?= $form->field($model, 'email')->textInput(['maxlength' => 255, 'autofocus'=>true]) ?>
		</div>
	</div>
	

	<?= $form->field($model, 'captcha')->widget(Captcha::className(), [
		'template' => '<div class="row"><div class="col-md-5">{image}</div><div class="md-offset-2 col-md-7">{input}</div></div>',
		'captchaAction'=>['/user-management/auth/captcha']
	]) ?>

	<div class="row">
		<div class="form-group" style = "margin-right: 0% !important;">
			<div class="col-sm-offset-3 col-sm-9">
				<?= Html::submitButton(
					'<span class="glyphicon glyphicon-ok"></span> ' . UserManagementModule::t('front', 'Recover'),
					['class' => 'btn btn-primary']
				) ?>
			</div>
		</div>
	</div>
	<?php ActiveForm::end(); ?>

</div>

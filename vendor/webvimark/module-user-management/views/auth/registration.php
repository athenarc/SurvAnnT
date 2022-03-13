<?php

use webvimark\modules\UserManagement\UserManagementModule;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;
use yii\helpers\Html;
use kartik\select2\Select2;

/**
 * @var yii\web\View $this
 * @var webvimark\modules\UserManagement\models\forms\RegistrationForm $model
 */

// $this->title = UserManagementModule::t('front', 'Registration');
// $this->params['breadcrumbs'][] = $this->title;
?>

<div class="user-registration">

	
	<div class ="outside-div medium-form">
		<div class = "row header-row">
            <div class = "col-md-12">
                <h3>
                    <h2><?= $this->title ?></h2>       
                </h3>
            </div>
        </div>
		
		<?php $form = ActiveForm::begin(
			['id'=>'user', 
			'layout'=>'horizontal', 
			'validateOnBlur'=>false, 
			'fieldConfig' => 
				[ 
					'horizontalCssClasses' => 
						[
						 	'label' => 'col-md-2',
						 	'offset' => 'col-md-offset-2',
						 	'wrapper' => 'col-md-12',
						],
				],
			]); ?>

		<div class = "row d-flex align-items-end">
			<div class = "col-md-8">
				<?= $form->field($model, 'username')->textInput(['maxlength' => 50, 'autocomplete'=>'off', 'autofocus'=>true]) ?>
			</div>
			<div class = "col-md-4">
				<?= $form->field($model, 'availability')->checkbox(['value' => true]) ?>
			</div>
		</div>

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

		<div class = "row">
			<div class = "col-md-12">
				<?= $form->field($model, 'email')->textInput(['maxlength' => 50, 'autocomplete'=>'off', 'autofocus'=>true]) ?>
			</div>
		</div>

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
                                'options' => [ 'multiple' => true ],
                                'pluginOptions' => [ 'allowClear' => true, 'tags' => true ],
                                ]
                            )
                        ) 
                        ?>
			</div>
		</div>
		
		<?= $form->field($model, 'captcha')->widget(Captcha::className(), [
			'template' => '<div class="row d-flex align-items-end"><div class="col-md-4">{image}</div><div class="col-md-8">{input}</div></div>',
			'captchaAction'=>['/user-management/auth/captcha']
		]) ?>


		<!-- <div class="form-group"> -->
		<div class ="row button-row">
			<div class="col-md-12">
				<?= Html::submitButton( UserManagementModule::t('front', 'Register'), ['class' => 'btn btn-primary submit-button'] ) ?>
			</div>
		</div>
		<!-- </div> -->

		<?php ActiveForm::end(); ?>
	</div>
</div>

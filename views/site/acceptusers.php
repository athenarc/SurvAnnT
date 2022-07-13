<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model app\models\participatesin */
/* @var $form ActiveForm */
?>
<div class="participatesincreate survey-form">

    <div class="outside-div">
        <div class = "row header-row dataset-header-row">
        </div>
        <div class = "col-md-12 p-5">
            <?php $form = ActiveForm::begin(['options' => ['class' => 'survey-create']]); ?>


            <table class="table table-striped table-bordered participants-table">     
                <tr class = "dataset-table-header-row">
                    <td class = "dataset-header-column"> Survey </td>
                    <td class = "dataset-header-column"> Username </td>
                    <td class = "dataset-header-column"> Name </td>
                    <td class = "dataset-header-column"> Last Name </td>
                    <td class = "dataset-header-column"> Fields </td>
                    <td class = "dataset-header-column"> Actions 
                        <a class = "fas fa-info-circle link-icon white" title = "Accept or Deny user." style = "color: white !important;"></a>
                    </td>
                </tr>
                <?php foreach($participants as $key => $participant): ?>

                        <tr>
                            <td><?= Html::a($participant->getSurvey()->select(['name'])->asArray()->all()[0]['name'], 'index.php?r=site%2Fsurveys-view&surveyid='.$participant->getSurvey()->select(['id'])->asArray()->all()[0]['id']) ?></td>
                            <td><?= $participant->getUser()->select(['username'])->asArray()->all()[0]['username'] ?></td>
                            <td><?= $participant->getUser()->select(['name'])->asArray()->all()[0]['name'] ?></td>
                            <td><?= $participant->getUser()->select(['surname'])->asArray()->all()[0]['surname'] ?></td>
                            <td><?= str_replace( "&&", "<br>", $participant->getUser()->select(['fields'])->asArray()->all()[0]['fields'])  ?></td>
                            <td>
                                <a id = "participant-<?=$key?>" class="fas fa-user-check link-icon accept-user"></a> 
                                <a id = "participant-<?=$key?>" class="fas fa-user-slash link-icon reject-user"></a> 
                            </td>
                        </tr>
                        <?= $form->field($participant, "[$key]userid")->hiddenInput()->label(false) ?>
                        <?= $form->field($participant, "[$key]surveyid")->hiddenInput()->label(false) ?>
                <?php endforeach; ?>
            </table>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>


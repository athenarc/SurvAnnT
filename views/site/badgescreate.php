<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Badges */
/* @var $form ActiveForm */
?>
<div class="badgescreate">
    <div class = 'error-div' style = "display: none;"></div>
    <div class ="outside-div">
        <div class = "row header-row dataset-header-row">
            <?php include 'tabs.php'; ?>
        </div>
        <?php $form = ActiveForm::begin(['options' => ['id'=> 'badges-form',  'class' => 'resource-before-form', 'enctype' => 'multipart/form-data']]); ?>
            <div class = "row button-row-header">
                <div class = "col-md-10"></div>
                <div class = "col-md-1">
                    <?= Html::a( 'Previous', $tabs['Participants']['link'].$surveyid, ['class' => 'btn btn-primary submit-button ', 'name' => 'test-name']); ?>
                </div>
                <div class = "col-md-1">
                    <?= Html::submitButton('Next', ['class' => 'btn btn-primary submit-button', 'name' => 'next']) ?>
                </div>
            </div>
            <div class="survey-form-box">
                <div class = "row">
                    <div class = "col-md-12">
                        <div class = "header-label">
                                <?= Html::checkbox('badges-used', $survey->badgesused, ['id' => 'badges-used', 'label' => 'Enable Badges']) ?> &nbsp;
                        </div>
                         <!-- Html::a( 'Options', '', ['class' => 'btn submit-button edit-button', 'name' => 'test-name', 'style' => ($survey->badgesused) ? "display: block;" : "display: none;"])  -->
                    </div>
                </div>

                <div class = "row badges-hide" style = "display: <?= ($survey->badgesused) ? 'flex' : 'none' ?>">
                    <div class="col-lg-1 d-flex align-items-center">
                        <span class="control-label"> Source </span>
                    </div>
                    <div class = "col-md-6">
                         <?= Html::dropDownList('resources-function', $option, $options, ['class' => 'form-control badges-option-select']) ?>
                    </div>
                    <div class = "col-md-3">
                        
                    </div>
                </div>
                <br>
                <br>
                <div class = "header-label badges-hide" style = "display: <?= ($survey->badgesused) ? 'block' : 'none' ?>">    
                    Your Badges
                </div>
                <div class = "datasets-table " style = "display: <?= ($survey->badgesused) ? 'block' : 'none' ?>"> 
                    <?php foreach ($badges as $key => $badge): ?>
                        
                        <div class = "resource-object-<?=$key?>">
                            <?= $form->field($badge, "[$key]id")->hiddenInput()->label(false) ?>
                            <?= $form->field($badge, "[$key]ownerid")->hiddenInput()->label(false) ?>
                            <div class = "dataset-header-column dataset-table-header-row resource-header-row text-center" colspan = "3"> 
                                <span class = "float-left"> &nbsp;
                                    <?= Html::checkbox('agree-badge-'.$key, $badge->isNewRecord || in_array($surveyid, array_column($badge->surveytobadges, 'surveyid') ) ? true : false, ['id' => 'use-badge-'.$key, 'label' => 'Use']) ?> &nbsp;
                                    <?= ( $userid == $badge->ownerid || ( $badge->id == '') ) ? $form->field($badge, "[$key]allowusers")->checkbox(['label' => 'Public', 'id' => 'badge-allowusers-'.$key]) : '' ?>

                                </span>
                                <span class = "center">
                                    Badge <?= $badge->id ?>
                                </span>
                                <span class = "float-right"> 
                                    &nbsp;
                                    <a id = "dataset-<?=$key?>" class="fas fa-eye link-icon white hide-dataset"></a> 
                                </span> 
                            </div>
                            <div class = "text resource-types">
                                <table class="table table-striped table-bordered participants-table table-<?=$key?>">  
                                    <tr class = "dataset-table-header-row">
                                        <td class = "dataset-header-column"> Name </td>
                                        <?php if ( ( $userid == $badge->ownerid ) || ( $badge->id == '' ) ) : ?>
                                            <td class = "dataset-header-column"> Image </td>
                                        <?php endif; ?>
                                        <td class = "dataset-header-column"> Rating Condition 
                                            <a class="fas fa-info-circle tooltip-icon" title="Number of annotations to earn this badge"></a>
                                        </td>
                                        <td class = "dataset-header-column"> Badge Preview </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <?= $form->field($badge, "[$key]name")->textInput(['disabled'=> ( ( $userid == $badge->ownerid ) || ( $badge->id == '' ) ) ? false : true])->label(false) ?>
                                        </td>
                                        <?php if ( ( $userid == $badge->ownerid ) || ( $badge->id == '' ) ) : ?>
                                            <td>
                                                <?= $form->field($badge, "[$key]image")->fileInput(['id' => 'image-'.$key, 'multiple' => false ])->label(false) ?>
                                            </td>
                                        <?php endif; ?>
                                        <td>
                                            <?= $form->field($surveytobadges_arr[$key], "[$key]ratecondition")->textInput(['id' => 'rate-condition-'.$key])->label(false) ?>
                                        </td>
                                        <td>
                                            <?= isset($badges[$key]->image) ? '<img id = "image-preview-'.$key.'" src="data:image/png;base64,'.base64_encode($badges[$key]->image ).'"/>' : '' ?>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <div class = "row">
                        <div class = "col-md-11">
                        </div>
                        <div class = "col-md-1">
                            <?= Html::a( 'Add', '', ['class' => 'btn btn-primary submit-button add-badge', 'name' => 'test-name']) ?>
                        </div>
                    </div>
                </div>
            </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>

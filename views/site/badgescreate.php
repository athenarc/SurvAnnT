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
            <?php foreach ($tabs as $tab => $url): ?>
                <div class = "tab col-md" style = "border-bottom: <?= ( $tab == $message ) ? 'none !important;' : '' ?>">
                    <a class = "<?= ( ! $url['enabled'] ) ? 'url-disabled' : '' ?> " href = "<?= ($url['enabled']) ? $url['link'].$surveyid : null ?>" ><h5 title = "<?= $message ?>" style = "opacity: <?= ( $url['enabled'] ) ? '1' : '' ?>"> <?= $tab ?></h5></a>
                </div>
            <?php endforeach; ?>
        </div>
        <?php $form = ActiveForm::begin(['options' => ['class' => 'resource-before-form', 'enctype' => 'multipart/form-data']]); ?>
            <div class = "row" style = "margin:3%;">
                <div class = "col-md-12">
                    <?= Html::checkbox('badges-used', $survey->badgesused, ['id' => 'badges-used', 'label' => 'Use Badges on this Campaign']) ?> &nbsp;
                    <?=  Html::a( 'Options', '', ['class' => 'btn submit-button edit-button', 'name' => 'test-name', 'style' => ($survey->badgesused) ? "display: block;" : "display: none;"]) ?>
                </div>
            </div>
            <div class = "datasets-table edit-tools" style = "display: <?= ($survey->badgesused) ? 'block' : 'none' ?>"> 
                
                <div class = "row " >
                    <div class = "col-md-3">
                        
                    </div>
                    <div class = "col-md-6">
                        <?= Html::dropDownList('resources-function', $option, $options, ['class' => 'form-control user-resource-select']) ?> 
                    </div>
                    <div class = "col-md-3">
                        
                    </div>
                </div>
                <br>
                <div class = "row resources-number" >
                    <div class = "col-md-12">
                        <?= Html::a('Submit', '', ['class' => 'btn btn-primary submit-button submit-action-form', 'name' => 'submit']) ?>
                    </div>
                </div>

            </div>
            <div class = "datasets-table" style = "display: <?= ($survey->badgesused) ? 'block' : 'none' ?>"> 
                <?php foreach ($badges as $key => $badge): ?>
                    
                    <div class = "resource-object-<?=$key?>">
                        <?= $form->field($badge, "[$key]id")->hiddenInput()->label(false) ?>
                        <?= $form->field($badge, "[$key]ownerid")->hiddenInput()->label(false) ?>
                        <div class = "dataset-header-column dataset-table-header-row resource-header-row text-center" colspan = "3"> 
                            <span class = "float-left" style = "width: 30%; text-align: left !important;"> &nbsp;
                                <?= Html::checkbox('agree-badge-'.$key, $badge->isNewRecord || in_array($surveyid, array_column($badge->surveytobadges, 'surveyid') ) ? true : false, ['id' => 'use-badge-'.$key, 'label' => 'Use']) ?> &nbsp;
                                <?= ( $userid == $badge->ownerid || ( $badge->id == '') ) ? $form->field($badge, "[$key]allowusers")->checkbox(['label' => 'Public', 'id' => 'badge-allowusers-'.$key]) : '' ?>

                            </span>
                            <span class = "center" style = "width: 40%;">
                                Badge <?= $badge->id ?>
                            </span>
                            <span class = "float-right" style = "width: 30%; text-align: right;"> 
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
                                        <a class="fas fa-info-circle tooltip-icon" title="Number of surveys to earn this badge"></a>
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
            <div class = "row button-row">
                <div class = "col-md-10"></div>
                <div class = "col-md-1">
                    <?= Html::a( 'Previous', $tabs['Participants']['link'].$surveyid, ['class' => 'btn btn-primary submit-button ', 'name' => 'test-name']); ?>
                </div>
                <div class = "col-md-1">
                    <?= Html::submitButton('Next', ['class' => 'btn btn-primary submit-button', 'name' => 'next']) ?>
                </div>
            </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>

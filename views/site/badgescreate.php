<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\bootstrap4\LinkPager;
use yii\web\View;

$this->registerJsFile('@web/js/badgescreate.js', ['position' => View::POS_END, 'depends' => [\yii\web\JqueryAsset::className()]]);


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
        <div class = "row button-row ">
            <div class = "col-md-10 d-flex align-items-center">
                <i class="fa fa-info-circle helper-message" ></i>&nbsp;
                Create the collection of Badges to be used for Survey/Annotation purposes (users will be awarded these badges depending on the conditions you will set)
            </div>
            <div class = "col-md-2 text-right">
                <?= Html::a( 'Previous', $tabs['Participants']['link'].$survey->id, ['class' => 'btn btn-primary', 'name' => 'test-name']); ?>
                <?= Html::a( 'Next', $tabs['Overview']['link'].$survey->id, ['class' => 'btn btn-primary', 'name' => 'test-name']); ?>
            </div>
        </div>
        <?php $form = ActiveForm::begin(['options' => ['id'=> 'badges-form',  'class' => 'resource-before-form', 'enctype' => 'multipart/form-data']]); ?>
            
            <div class="survey-form-box">
                <div class = "row">
                    <div class = "col-md-12">
                        <div class = "header-label">
                            Selected Badges
                        </div>
                    </div>
                </div>
                <div class = "row">
                    <div class="col-md-12 text-right">
                        <?= ($survey->getSurveytobadges()->all()) ? Html::a( 'Delete All Badges', 'index.php?r=badges%2Fbadges-delete-all&surveyid='.$survey->id, ['class' => 'btn btn-primary delete-badges']) : '' ?> &nbsp;
                        <?= Html::button( 'Reuse Existing Badges', ['class' => 'btn btn-primary', 'data-toggle' => "modal", 'data-target' => ".db-badges"]) ?> &nbsp;
                        <?= Html::button( 'Create Badges', ['class' => 'btn btn-primary', 'data-toggle' => "modal", 'data-target' => ".user-badges"]) ?> &nbsp;
                       <input type="hidden" id="surveyId" value="<?=$survey->id?>" name="">
                    </div>
                    
                </div>
                <br>

                
                <!-- <div class = "datasets-table " >  -->
                    <div class="row">
                        <div class="col-md-12 d-flex justify-content-center">
                            <?= LinkPager::widget([
                                'pagination' => $paginationMyBadges,
                            ]) ?>
                        </div>
                    </div>
                    <table class="table table-striped table-bordered participants-table">  
                        <tr class = "dataset-table-header-row">
                            <th class = "dataset-header-column">
                                Name
                            </th>
                            <th class = "dataset-header-column">
                                Annotations Required to Earn
                            </th>
                            <th class = "dataset-header-column">
                                Badge Preview
                            </th>
                            <th class = "dataset-header-column">
                                Public
                            </th>
                            <th class = "dataset-header-column">
                                Actions
                            </th>
                        </tr>
                        <?php foreach ($myBadges as $key => $badge): ?>
                            <tr>
                                <td>
                                    <span class="edit-badge-name-<?=$badge->id?>" style ="display: none;">
                                        <?= $form->field($badge, "name")->textInput(['name' => 'badge-name-'.$badge->id, 'id' => 'badge-name-'.$badge->id])->label(false) ?>
                                    </span>
                                    <span class="edit-badge-name-<?=$badge->id?> badge-name-<?=$badge->id?>-text">
                                        <?= $badge->name ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="edit-badge-ratecondition-<?=$badge->id?>" style ="display: none;">
                                        <?= $form->field($surveytobadges_arr[$key], "[$key]ratecondition")->textInput(['name' => 'rate-condition-'.$badge->id, 'id' => 'badge-ratecondition-'.$badge->id])->label(false) ?>
                                    </span>
                                    <span class="edit-badge-ratecondition-<?=$badge->id?> badge-ratecondition-<?=$badge->id?>-text">
                                        <?= $surveytobadges_arr[$key]->ratecondition ?>
                                    </span>
                                    
                                </td>
                                <td>
                                    <?= isset($badge->image) ? '<img id = "image-preview-'.$key.'" class = "badge-image-preview" src="data:image/png;base64,'.base64_encode($badge->image ).'"/>' : '' ?>
                                </td>
                                <td>
                                    <span class="edit-badge-allowusers-<?=$badge->id?>" style ="display: none;">
                                        <?=  $form->field($badge, "[$key]allowusers")->checkbox(['name' => 'allowusers-'.$badge->id, 'id' => 'allowusers-'.$badge->id, 'label' => false, 'id' => 'badge-allowusers-'.$badge->id])
                                        ?>
                                    </span>
                                    <span class="edit-badge-allowusers-<?=$badge->id?>">
                                        <?php if($badge->allowusers): ?>
                                            Yes
                                        <?php else: ?>
                                            No
                                        <?php endif; ?> 
                                    </span>
                                </td> 
                                <td>
                                    <a id="badges-actions-<?=$badge->id?>" class="fas fa-pencil edit-badge link-icon"></a>
                                    <a id="badges-actions-<?=$badge->id?>" class="fas fa-trash-alt delete-badge link-icon"></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                <!-- </div> -->
            </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
<!-- 
<div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      ...
    </div>
  </div>
</div> -->

<div class="modal fade bd-example-modal-lg user-badges" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <?php $form = ActiveForm::begin(['options' => ['id'=> 'badges-import', 'enctype' => 'multipart/form-data']]); ?>
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Import Badges Form</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body badges-modal-body">
        <br>
        <table class="table table-striped table-bordered participants-table">
            <tr class = "dataset-table-header-row">
                <th class = "dataset-header-column">
                    File Upload
                </th>
            </tr>
            <tr>
                <td>
                    <div class="text-center">
                        <?= $form->field($badge, "imageFiles[]")->fileInput(['multiple' => true, 'id' => 'badges-file-input'])->label(false) ?>
                    </div>
                </td>
            </tr>
        </table>
        
        <br>
        <div class="badges-modal-body-table">

        </div>
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <?= Html::submitButton('Submit', ['class' => 'btn btn-primary submit-button', 'name' => 'new-badges', 'value' => 'new-badges', 'id' => 'new-badges']) ?>
      </div>
      <?php ActiveForm::end(); ?>
    </div>
  </div>
</div>


<div class="modal fade bd-example-modal-lg db-badges" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <?php $form = ActiveForm::begin(['options' => ['id'=> 'badges-reuse', 'enctype' => 'multipart/form-data']]); ?>
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Reuse Badges From SurvAnnT's Database</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body badges-modal-body">
        <div class="col-md-4 d-flex justify-content-center">
        </div>
        <table class="table table-striped table-bordered participants-table">  
            <tr class = "dataset-table-header-row">
                <th class = "dataset-header-column">
                    Name
                </th>
                <th class = "dataset-header-column">
                    Owner
                </th>
                <th class = "dataset-header-column">
                    Badge Preview
                </th>
                <th class = "dataset-header-column">
                    Use
                </th>
            </tr>
            <?php foreach ($badges as $key => $badge): ?>
                <tr>
                    <td>
                        <?= $badge->name ?>
                    </td>
                    <td>
                        <?= $badge->getOwner()->select(['username'])->asArray()->one()['username'] ?>
                    </td>
                    <td>
                        <?= isset($badge->image) ? '<img id = "image-preview-'.$key.'" class = "badge-image-preview" src="data:image/png;base64,'.base64_encode($badge->image ).'"/>' : '' ?>
                    </td>
                    <td>
                        <?= Html::checkbox('agree-badge-'.$badge->id, $badge->isNewRecord || in_array($survey->id, array_column($badge->surveytobadges, 'surveyid') ) ? true : false, ['id' => 'use-badge-'.$key, ]) ?> &nbsp; 
                        <!-- 'label' => 'Use' -->
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <?= Html::submitButton('Submit', ['class' => 'btn btn-primary submit-button', 'name' => 'new-badges', 'id' => 'new-badges']) ?>
      </div>
      <?php ActiveForm::end(); ?>
    </div>
  </div>
</div>
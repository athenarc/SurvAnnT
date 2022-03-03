<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Resources */
/* @var $form ActiveForm */
?>
<div class="resourcecreateformbefore">
    <div class = "outside-div">
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
                    <?=  Html::a( 'Options', '', ['class' => 'btn submit-button edit-button', 'name' => 'test-name']) ?>
                </div>
            </div>
            <div class = "datasets-table edit-tools" style = "display: <?= $tool ?>"> 
                
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
                <div class = "row resources-number">
                    <div class = "col-md-3">
                        
                    </div>
                    <div class = "col-md-6 user-resource-types" style = "display: <?= ( $option == 'user-form' ) ? 'block;' : 'none;' ?>">
                        <?= Html::dropDownList('unused-resource', $resource_types_option, $resource_types, [ 'id' => 'user-resource-types', 'class' => 'form-control', 'label' => 'Resource types']) ?> 
                    </div>
                    <div class = "col-md-6 db-resource-types" style = "display: <?= ( $option == 'db-load' ) ? 'block;' : 'none;' ?>">
                        <?= Html::dropDownList('unused-resource', $resource_types_option, $db_available_resources, [ 'id' => 'db-resource-types', 'class' => 'form-control', 'label' => 'Resource types']) ?> 
                    </div>
                    <div class = "col-md-6 dir-resource-types" style = "display: <?= ( $option == 'dir-load' ) ? 'block;' : 'none;' ?>">
                        <?= Html::dropDownList('unused-resource', $resource_types_option, $dir_available_resources, [ 'id' => 'dir-resource-types', 'class' => 'form-control', 'label' => 'Resource types']) ?> 
                    </div>
                    <div class = "col-md-3">
                        
                    </div>
                </div>
                <div class = "row resources-number" >
                    <div class = "col-md-12">
                        <?= Html::a('Submit', '', ['class' => 'btn btn-primary submit-button submit-action-form', 'name' => 'submit']) ?>
                    </div>
                </div>

            </div>

            <div class = "datasets-table"> 
                <div class = "row" >
                    <div class = "col-md-12 pull-right">
                        <?=  Html::a( 'Use all', '', ['class' => 'btn submit-button pull-left use-all-button', 'name' => 'use-all', 'style' => 'float: unset !important;']) ?>
                    </div>
                </div>
                <br>
                <?php foreach ($resources as $key => $resource): ?>
                    <div class = "resource-object-<?=$key?>">
                        <?= $form->field($resource, "[$key]type")->hiddenInput()->label(false) ?>
                        <?= $form->field($resource, "[$key]id")->hiddenInput()->label(false) ?>
                        <?= $form->field($resource, "[$key]ownerid")->hiddenInput()->label(false) ?>
                        
                        <div class = "dataset-header-column dataset-table-header-row resource-header-row text-center" colspan = "3"> 
                            <span class = "float-left" style = "width: 30%; text-align: left !important;"> &nbsp;
                                <?= Html::checkbox('agree-'.$resource->type.'-'.$key, in_array($surveyid, array_column($resource->surveytoresources, 'surveyid') ) ? true : false, ['id' => 'use-'.$resource->type.'-'.$key, 'label' => 'Use']) ?> &nbsp;
                                <?= ( $userid == $resource->ownerid || ( $resource->id == '') ) ? $form->field($resource, "[$key]allowusers")->checkbox(['label' => 'Allow', 'id' => $resource->type.'-allowusers-'.$key]) : '' ?>

                            </span>
                            <span class = "center" style = "width: 40%;">
                                <?= ucwords($resource->type) ?>
                            </span>
                            <span class = "float-right" style = "width: 30%; text-align: right;"> 
                                &nbsp;
                                <a id = "dataset-<?=$key?>" class="fas fa-eye link-icon white hide-dataset"></a> 
                            </span> 
                        </div>
                        <div class = "text resource-types" style = "display: <?= ( $resource_types_option == 'text' ) ? : 'none;' ?> ">
                            
                            <table class="table table-striped table-bordered participants-table table-<?=$key?>" style = "display: <?= ($option == 'db-load' && $resource_types_option == 'text') ? 'none' : '' ?> ;">  
                                <tr class = "dataset-table-header-row">
                                    <td class = "dataset-header-column" style = "width: 30%;"> Title </td>
                                    <td class = "dataset-header-column" style = "width: 70%;"> Text </td>
                                </tr>
                                <tr>
                                    <td><?=  ( $resource_types_option == 'text' ) ? $form->field($resource, "[$key]title")->textInput() : '' ?></td>
                                    <td>
                                        <?= $form->field($resource, "[$key]text")->textArea() ?>
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <div class = "article resource-types" style = "display: <?= ( $resource_types_option == 'article' ) ? : 'none;' ?> ">
                            <table class="table table-striped table-bordered participants-table table-<?=$key?>" style = "display: <?= ($option == 'db-load' && $resource_types_option == 'article') ? 'none' : '' ?> ;">  
                                <tr class = "dataset-table-header-row">
                                    <td  class = "dataset-header-column" colspan = "1"> Title </td>
                                    <td class = "dataset-header-column" colspan = "2"> Abstract </td>
                                </tr>
                                <tr>
                                    <td><?=  ( $resource_types_option == 'article' ) ? $form->field($resource, "[$key]title")->textInput(['disabled'=> ( ( $userid == $resource->ownerid ) || ( $resource->id == '' ) ) ? false : true ]) : '' ?></td>
                                    <td colspan="2"><?= $form->field($resource, "[$key]abstract")->textArea(['disabled'=> ( $userid == $resource->ownerid || $resource->id == '') ? false : true ])?></td>
                                </tr>
                                <tr class = "dataset-table-header-row">
                                    <td  class = "dataset-header-column" colspan = "1"> Pubmed id </td>
                                    <td class = "dataset-header-column" colspan = "1"> Year </td>
                                    <td class = "dataset-header-column"> Authors </td>
                                </tr>
                                <tr>
                                    <td><?= $form->field($resource, "[$key]pubmed_id")->textInput(['disabled'=> ( ( $userid == $resource->ownerid ) || ( $resource->id == '' ) ) ? false : true ]) ?></td>
                                    <td><?= $form->field($resource, "[$key]year")->textInput(['disabled'=> ( ( $userid == $resource->ownerid ) || ( $resource->id == '' ) ) ? false : true ]) ?></td>
                                    <td><?= $form->field($resource, "[$key]authors")->textInput(['disabled'=> ( ( $userid == $resource->ownerid ) || ( $resource->id == '' ) ) ? false : true ]) ?></td>
                                </tr>
                                <tr class = "dataset-table-header-row">
                                    <td  class = "dataset-header-column" colspan = "1"> Pmc id </td>
                                    <td class = "dataset-header-column" colspan = "1"> Doi </td>
                                    <td class = "dataset-header-column"> Journal </td>
                                </tr>
                                <tr>
                                    <td><?= $form->field($resource, "[$key]pmc")->textInput(['disabled'=> ( ( $userid == $resource->ownerid ) || ( $resource->id == '' ) ) ? false : true ]) ?></td>
                                    <td><?= $form->field($resource, "[$key]doi")->textInput(['disabled'=> ( ( $userid == $resource->ownerid ) || ( $resource->id == '' ) ) ? false : true ]) ?></td>
                                    <td><?= $form->field($resource, "[$key]journal")->textInput(['disabled'=> ( ( $userid == $resource->ownerid ) || ( $resource->id == '' ) ) ? false : true ]) ?></td>
                                </tr>

                            </table>
                           
                        </div>

                        <div class = "image resource-types" style = "display: <?= ( $resource_types_option == 'image' ) ? 'block;' : 'none;' ?> ">
                            <table class="table table-striped table-bordered participants-table table-<?=$key?>" style = "display: <?= ($option == 'db-load' && $resource_types_option == 'image') ? 'none' : '' ?> ;">     
                            
                                <tr class = "dataset-table-header-row">
                                    <td class = 'dataset-header-column' style="width: 70%;"> Image </td>
                                    <td class = "dataset-header-column" style="width: 30%;"> Preview </td>
                                </tr>
                                <tr>
                                    <?= ( $userid == $resource->ownerid || $resource->id == '' ) ? "<td class = 'image-input'>".$form->field($resource, "[$key]image")->fileInput(['id' => 'image-'.$key, 'multiple' => false ])."</td>" : "<td class = 'image-input'></td>" ?>   
                                    <td> 
                                        <?= isset($resources[$key]->image) ? '<img id = "image-preview-'.$key.'" src="data:image/png;base64,'.base64_encode($resources[$key]->image ).'"/>' : '' ?>
                                    </td>
                                </tr>
                            
                            </table>
                        </div>

                        <div class = "empty resource-types" style = "display: <?= ( $resource_types_option == 'questionaire' ) ? : 'none;' ?> ">
                            <table class="table table-striped table-bordered participants-table table-<?=$key?>" style = "display: <?= ($option == 'db-load' && $resource_types_option == 'questionaire') ? 'none' : '' ?> ;">  
                                <tr class = "dataset-table-header-row">
                                    <td class = "dataset-header-column" style = "width: 30%;"> Title </td>
                                </tr>
                                <tr>
                                    <td><?=  ( $resource_types_option == 'questionaire' ) ? $form->field($resource, "[$key]title")->textInput() : '' ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                <?php endforeach; ?>
                <div class = "row">
                    <div class = "col-md-11">
                    </div>
                    <div class = "col-md-1">
                        <?= ( $option != 'db-load' && $resource_types_option != 'questionaire') ? Html::a( 'Add', '', ['class' => 'btn btn-primary submit-button add-button', 'name' => 'test-name']) : '' ?>
                    </div>
                </div>
            </div>
            <div class = "row button-row">
                <div class = "col-md-10"></div>
                <div class = "col-md-1">
                    <?= Html::a( 'Previous', $tabs['Survey']['link'].$surveyid, ['class' => 'btn btn-primary submit-button ', 'name' => 'test-name']); ?>
                </div>
                <div class = "col-md-1">
                    <?= Html::submitButton('Next', ['class' => 'btn btn-primary submit-button', 'name' => 'next']) ?>
                </div>
            </div>
        <?php ActiveForm::end(); ?>
    </div>

</div><!-- resourcecreateform -->

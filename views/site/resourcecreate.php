<?php
 
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Resources */
/* @var $form ActiveForm */
?>
<div class="resourcecreateformbefore">
    <div class = 'error-div' style = "display: none;"></div>
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
                <!-- <div class = "col-md-12"> -->
                     <!-- Html::a( 'Options', '', ['class' => 'btn submit-button edit-button', 'name' => 'test-name', 'style' => "display: $tool"])  -->
                <!-- </div> -->
            </div>
            <div class = "datasets-table edit-tools" style = "display: <?= $tool ?>"> 
                
                <div class = "row " >
                    <div class = "col-md-3">
                        
                    </div>
                    <div class = "col-md-6">
                        Loading method: <?= Html::dropDownList('resources-function', $option, $options, ['class' => 'form-control user-resource-select']) ?> 
                    </div>
                    <div class = "col-md-3">
                        
                    </div>
                </div>
                <br>
                <div class = "row resources-number">
                    <div class = "col-md-3">
                        
                    </div>
                    <div class = "col-md-6 user-resource-types" style = "display: <?= ( $option == 'user-form' ) ? 'block;' : 'none;' ?>">
                        Type of resources: <?= Html::dropDownList('unused-resource', $resource_types_option, $resource_types, [ 'id' => 'user-resource-types', 'class' => 'form-control', 'label' => 'Resource types']) ?> 
                    </div>
                    <div class = "col-md-6 db-resource-types" style = "display: <?= ( $option == 'db-load' ) ? 'block;' : 'none;' ?>">
                        Type of resources: <?= Html::dropDownList('unused-resource', $resource_types_option, $db_available_resources, [ 'id' => 'db-resource-types', 'class' => 'form-control', 'label' => 'Resource types']) ?> 
                    </div>
                    <div class = "col-md-6 dir-resource-types" style = "display: <?= ( $option == 'dir-load' ) ? 'block;' : 'none;' ?>">
                        Type of resources: <?= Html::dropDownList('unused-resource', $resource_types_option, $dir_available_resources, [ 'id' => 'dir-resource-types', 'class' => 'form-control', 'label' => 'Resource types']) ?> 
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
            
            <div class = "datasets-table"> 
                <h3><i>Your Collection</i></h3>
                <hr style="background-color: white;">
                <div class = "row resources-number" >
                    <div class = "col-md-1">
                        Name
                    </div>
                    <div class="col-md-4">
                        <?= $form->field($collection, "name")->textInput()->label(false) ?>
                    </div>
                    <div class="col-md-1">
                        About
                    </div>
                    <div class = "col-md-6 col-md-offset-6">
                        <?= $form->field($collection, "about")->textArea()->label(false) ?>
                    </div>
                </div>
                <br>
                <br>
                <div class = "row" > 
                    <div class="col-md-8">
                        <?php if( $collection->isNewRecord ): ?>
                            <h3> 
                                <i><?= ($option == 'db-load') ? "Chose from the following publicly available collections" : 'Choose resources from selected directory' ?></i>
                            </h3>
                        <?php endif; ?>
                    </div>
                    <?php if( $collection->isNewRecord && $resource_types_option != 'questionaire' && $option != 'dir-load' ): ?>
                        <div class = "col-md-4 text-right">
                            <?=  Html::a( 'Select all', '', ['class' => 'btn submit-button pull-left select-all-button', 'name' => 'use-all', 'style' => 'float: unset !important;']) ?>
                        </div>
                    <?php elseif( ! $collection->isNewRecord ): ?>
                        <div class = "col-md-4 text-right">
                            <?= Html::submitButton( 'Discard Collection', ['class' => 'btn submit-button pull-left', 'name' => 'discard-collection', 'id' => 'discard-collection']) ?>                       
                        </div>
                    <?php endif; ?>  
                </div>  
           
                <?php if( $collection->isNewRecord ): ?>
                    <hr style="background-color: white;">
                <?php else: ?>
                    <br>
                <?php endif; ?>
                <?php foreach ($collections as $collection_key => $collection): ?>
                    <div class = "collection-<?=$collection_key?>" style = "">
                        <div class = "dataset-header-column dataset-table-header-row resource-header-row text-center" colspan = "3"> 
                            <span class = "float-left" style = "width: 30%; text-align: left !important;"> &nbsp;
                                <?= ($option == 'db-load') ? Html::checkbox('agree-collection-'.$collection_key, ! isset( $collection->id ) || in_array($surveyid, array_column($collection->surveytocollections, 'surveyid') ) ? true : false, ['id' => 'use-collection-'.$collection_key, 'label' => 'Use']) : '' ?> &nbsp;
                                <?= ( $userid == $collection->userid || ( $collection->id == '') ) ? $form->field($collection, "[$collection_key]allowusers")->checkbox(['label' => 'Public']) : '' ?>
                            </span>
                            <span class = "center" style = "width: 40%;">
                                <?=  $collection->name.' <i>('.$collection->getUser()->select(['username'])->asArray()->all()[0]['username'].')</i>' ?>
                                
                            </span>
                            <span class = "float-right" style = "width: 30%; text-align: right;"> 
                                
                                &nbsp;
                                <a id = "dataset-<?=$collection_key?>" class="fas fa-eye link-icon white hide-dataset collections"></a> 
                            </span> 
                        </div>
                        <br>
                        <div id = "collection-resources-<?=$collection_key?>" class="" style = "width: 80%; margin: auto; display: none;">
                            <?php $resources = ( isset( $collection->id ) ) ? $collection->getResources()->where(['allowusers' => 1])->orWhere(['ownerid' => $userid])->all() : $resources ?>

                            <?php foreach ( $resources as $key => $resource): ?>
                            
                                <div class = "dataset-header-column dataset-table-header-row resource-header-row text-center" colspan = "3"> 
                                    <span class = "float-left" style = "width: 30%; text-align: left !important;"> &nbsp;
                                        <?=  ($option == 'db-load') ? Html::checkbox('agree-'.$collection_key.'-'.$resource->type.'-'.$key, $resource->collectionid == $collection->id ? true : false, ['id' => 'use-'.$resource->type.'-'.$key, 'label' => 'Use']) : '' ?> &nbsp;
                                        <?= ( $userid == $resource->ownerid || ( $resource->id == '') ) ? $form->field($resource, "[$key]allowusers")->checkbox(['label' => 'Public', 'id' => $resource->type.'-allowusers-'.$key]) : '' ?>
                                    </span>
                                    <span class = "center" style = "width: 40%;">
                                        <?= $resource->type ?>
                                    </span>
                                    <span class = "float-right" style = "width: 30%; text-align: right;"> 
                                        &nbsp;
                                        <a id = "dataset-<?=$collection_key?>-<?=$key?>" class="fas fa-eye link-icon white hide-dataset resources"></a> 
                                    </span> 
                                </div>

                                <div id = "resource-<?=$collection_key?>-<?=$key?>">
                                    
                                    <div class = "text resource-types" style = "display: <?= ( $resource->type == 'text' ) ? : 'none;' ?> ">
                                        <table class="table table-striped table-bordered participants-table table-<?=$collection_key?>-<?=$key?>" style = "display: <?= ($option == 'db-load' && $resource->type == 'text') ? 'none' : '' ?> ;">  
                                            <tr class = "dataset-table-header-row">
                                                <td class = "dataset-header-column" style = "width: 30%;"> Title </td>
                                                <td class = "dataset-header-column" style = "width: 70%;"> Text </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <?=  ( $resource->type == 'text' ) ? $form->field($resource, "[$key]title")->textInput() : '' ?>
                                                </td>
                                                <td>
                                                    <?= $form->field($resource, "[$key]text")->textArea() ?>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>

                                    <div class = "article resource-types" style = "display: <?= ( $resource->type == 'article' ) ? : 'none;' ?> ">
                                        <table class="table table-striped table-bordered participants-table table-<?=$collection_key?>-<?=$key?>" style = "display: <?= ($option == 'db-load' && $resource->type == 'article') ? 'none' : '' ?> ;">  
                                            <tr class = "dataset-table-header-row">
                                                <td  class = "dataset-header-column" colspan = "1"> Title </td>
                                                <td class = "dataset-header-column" colspan = "2"> Abstract </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <?=  ( $resource->type == 'article' ) ? $form->field($resource, "[$key]title")->textInput(['disabled'=> ( ( $userid == $resource->ownerid ) || ( $resource->id == '' ) ) ? false : true ]) : '' ?>
                                                </td>
                                                <td colspan="2">
                                                    <?= $form->field($resource, "[$key]abstract")->textArea(['disabled'=> ( $userid == $resource->ownerid || $resource->id == '') ? false : true ])?>
                                                </td>
                                            </tr>
                                            <tr class = "dataset-table-header-row">
                                                <td  class = "dataset-header-column" colspan = "1"> Pubmed id </td>
                                                <td class = "dataset-header-column" colspan = "1"> Year </td>
                                                <td class = "dataset-header-column"> Authors </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <?= $form->field($resource, "[$key]pubmed_id")->textInput(['disabled'=> ( ( $userid == $resource->ownerid ) || ( $resource->id == '' ) ) ? false : true ]) ?>
                                                </td>
                                                <td>
                                                    <?= $form->field($resource, "[$key]year")->textInput(['disabled'=> ( ( $userid == $resource->ownerid ) || ( $resource->id == '' ) ) ? false : true ]) ?>
                                                </td>
                                                <td>
                                                    <?= $form->field($resource, "[$key]authors")->textInput(['disabled'=> ( ( $userid == $resource->ownerid ) || ( $resource->id == '' ) ) ? false : true ]) ?>
                                                </td>
                                            </tr>
                                            <tr class = "dataset-table-header-row">
                                                <td  class = "dataset-header-column" colspan = "1"> Pmc id </td>
                                                <td class = "dataset-header-column" colspan = "1"> Doi </td>
                                                <td class = "dataset-header-column"> Journal </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <?= $form->field($resource, "[$key]pmc")->textInput(['disabled'=> ( ( $userid == $resource->ownerid ) || ( $resource->id == '' ) ) ? false : true ]) ?>
                                                </td>
                                                <td>
                                                    <?= $form->field($resource, "[$key]doi")->textInput(['disabled'=> ( ( $userid == $resource->ownerid ) || ( $resource->id == '' ) ) ? false : true ]) ?>
                                                </td>
                                                <td>
                                                    <?= $form->field($resource, "[$key]journal")->textInput(['disabled'=> ( ( $userid == $resource->ownerid ) || ( $resource->id == '' ) ) ? false : true ]) ?>
                                                </td>
                                            </tr>

                                        </table>
                                       
                                    </div>

                                    <div class = "image resource-types" style = "display: <?= ( $resource->type == 'image' ) ? 'block;' : 'none;' ?> ">
                                        <table class="table table-striped table-bordered participants-table table-<?=$collection_key?>-<?=$key?>" style = "display: <?= ($option == 'db-load' && $resource->type == 'image') ? 'none' : '' ?> ;">     
                                        
                                            <tr class = "dataset-table-header-row">
                                                <!-- <td class = 'dataset-header-column' style="width: 70%;"> Image </td> -->
                                                <td class = "dataset-header-column" style="width: 30%;"> Preview </td>
                                            </tr>
                                            <tr>
                                                <!-- ( $userid == $resource->ownerid || $resource->id == '' ) ? "<td class = 'image-input'>".$form->field($resource, "[$key]image")->fileInput(['id' => 'image-'.$key, 'multiple' => false ])."</td>" : "<td class = 'image-input'></td>"  -->
                                                <td> 
                                                    <?= isset($resource->image) 
                                                        ? isset($resource->id ) 
                                                            ? '<img id = "image-preview-'.$key.'" src="data:image/png;base64,'.base64_encode($resource->image ).'"/>' 
                                                            : '<img id = "image-preview-'.$key.'" src="'.$resource->image.'"/>'
                                                        : '' 
                                                    ?>
                                                </td>
                                            </tr>
                                        
                                        </table>
                                    </div>

                                    <div class = "empty resource-types" style = "display: <?= ( $resource->type == 'questionaire' ) ? : 'none;' ?> ">
                                        <table class="table table-striped table-bordered participants-table table-<?=$collection_key?>-<?=$key?>" style = "display: <?= ($option == 'db-load' && $resource->type == 'questionaire') ? 'none' : '' ?> ;">  
                                            <tr class = "dataset-table-header-row">
                                                <td class = "dataset-header-column" style = "width: 30%;"> Title </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <?=  ( $resource->type == 'questionaire' ) ? $form->field($resource, "[$key]title")->textInput() : '' ?>        
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                <br>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>

            </div>
            <div class = "row button-row">
                <div class = "col-md-10"></div>
                <div class = "col-md-1">
                    <?= Html::a( 'Previous', $tabs['General Settings']['link'].$surveyid, ['class' => 'btn btn-primary submit-button ', 'name' => 'test-name']); ?>
                </div>
                <div class = "col-md-1">
                    <?= Html::submitButton('Next', ['class' => 'btn btn-primary submit-button', 'name' => 'next']) ?>
                </div>
            </div>
        <?php ActiveForm::end(); ?>
    </div>

</div><!-- resourcecreateform -->

<style type="text/css">
    .collection-name-label{
        margin: 0%;
    }

    .collection-name{
        color: black !important;
        height: 24px;
    }
</style>
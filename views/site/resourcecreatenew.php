<?php
 
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use \kartik\datetime\DateTimePicker;
use kartik\select2\Select2;

date_default_timezone_set("Europe/Athens"); 
$date = date('Y-m-d hh:mm', time());
use yii\web\View;

$this->registerJsFile('@web/js/resourcecreatenew.js', ['position' => View::POS_END, 'depends' => [\yii\web\JqueryAsset::className()]]);
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
            <div class="survey-form-box">
                <div class = "header-label">
                    Options
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <?= Html::dropDownList('resources-function', $option, $options, ['class' => 'form-control user-resource-select']) ?>
                    </div>
                    <div class="col-md-4">
                        <?= Html::dropDownList('resources-type', $resource_types_option, $resource_types, [ 'id' => 'user-resource-types', 'class' => 'form-control', 'label' => 'Resource types']) ?>
                    </div>
                </div>
                <div class="loaded-collections">
                    <?php foreach($collections as $collection_key => $collection): ?>
                        <?php if( ! $collection->isNewRecord ): ?>
                            <div class = "dataset-header-column dataset-table-header-row resource-header-row text-center" colspan = "3"> 
                                <span class = "float-left" style = "width: 30%; text-align: left !important;"> &nbsp;
                                    <?= ($option == 'db-load') ? Html::checkbox('agree-collection-'.$collection_key, ! isset( $collection->id ) || in_array($surveyid, array_column($collection->surveytocollections, 'surveyid') ) ? true : false, ['id' => 'use-collection-'.$collection_key, 'label' => 'Use']) : '' ?> &nbsp;
                                    <?= ( $userid == $collection->userid || ( $collection->id == '') ) ? $form->field($collection, "[$collection_key]allowusers")->checkbox(['label' => 'Public']) : '' ?>
                                </span>
                                <span class = "center" style = "width: 40%;">
                                    <?=  (!$collection->isNewRecord) ? $collection->name.' <i>('.$collection->getUser()->select(['username'])->asArray()->all()[0]['username'].')</i>' : 'Your Collection' ?>
                                    
                                </span>
                                <span class = "float-right" style = "width: 30%; text-align: right;"> 
                                    
                                    &nbsp;
                                    <a id = "dataset-<?=$collection_key?>" class="fas fa-eye link-icon white hide-dataset collections"></a> 
                                </span> 
                            </div>
                        <?php endif; ?>
                        <br>
                        <div id = "collection-resources-<?=$collection_key?>" class="" style = "width: 80%; margin: auto; ">
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
                    <?php endforeach; ?>
                    
                </div>
                <div class = "header-label">
                    Your Collection
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <?= $form->field($user_collection, 'name')->textInput([])->label() ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($user_collection, 'about')->textArea()->label() ?>
                    </div>
                    <div class="col-md-2">
                        <?= $form->field($user_collection, 'allowusers')->checkbox([])->label(false) ?>
                    </div>
                </div>
                <div class = "header-label">
                    Your Resources
                </div>
                <div class="row">
                    <?php if ($user_collection): ?>
                        <?php foreach($user_collection->getResources()->all() as $resource): ?>

                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                </div>
                <div class = "row button-row">
                    <div class = "col-md-10"></div>
                    <div class = "col-md-1">
                        <!-- Html::a( 'Back', Yii::$app->request->referrer, ['class' => 'btn btn-primary submit-button ', 'name' => 'test-name']); -->
                    </div>
                    <div class = "col-md-1">
                        <?= Html::submitButton('Next', ['class' => 'btn btn-primary submit-button ']) ?>
                    </div>
                </div>
            </div>
        <?php ActiveForm::end(); ?>
    </div>

</div><!-- resourcecreateform -->

<style type="text/css">
    .survey-form-box{
        margin: 2em !important;
        padding: 3em;
        /*background-color: white;*/
        border-radius: 25px;
        /*border: 2px solid black;*/
    }

    .control-label{
        /*color: black !important;*/
        text-decoration: none;
        padding-bottom: 0.2em;
        font-style: italic;
        padding-left: 0.2em;

    }

    .header-label{
        /*color: black;*/
        /*font-weight: bold;*/
        margin-bottom: 1em;
        padding-bottom: 0.5em;
        border-bottom: 1px solid lightgrey;
        font-style: italic;
        font-size: 24px;
    }

    .row{
        margin-bottom: 2em;
    }

    .dataset-header-row{
        margin-bottom: 0% !important;
    }

    .loaded-collections{
        max-height: 300px;
        overflow: auto;
    }
</style>

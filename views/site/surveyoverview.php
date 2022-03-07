<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use \kartik\datetime\DateTimePicker;
use kartik\select2\Select2;

date_default_timezone_set("Europe/Athens"); 
$date = date('Y-m-d hh:mm', time());

?>
<div class="survey-form">

    <div class ="outside-div">

        <div class = "row header-row dataset-header-row">
            <?php foreach ($tabs as $tab => $url): ?>
                <div class = "tab col-md" style = "border-bottom: <?= ( $tab == $message ) ? 'none !important;' : '' ?>">
                    <a class = "<?= ( ! $url['enabled'] ) ? 'url-disabled' : '' ?> " href = "<?= ($url['enabled']) ? $url['link'].$surveyid : null ?>" ><h5 title = "<?= $message ?>" style = "opacity: <?= ( $url['enabled'] ) ? '1' : '' ?>"> <?= $tab ?></h5></a>
                </div>
            <?php endforeach; ?>
        </div>
        <?php $form = ActiveForm::begin(['options' => ['class' => 'survey-create']]); ?>
            <div class = "datasets-table"> 
                <h3 style = "margin: 0%;"><i>Overview</i></h3>
                <hr style="background-color: white;">
                <br>
                <?php foreach ($survey_sections as $section_name => $section): ?>
                    <!-- SURVEY / RESOURCES / QUESTIONS / PARTICIPANTS / BADGES -->
                    <div class="survey-section" style = "margin: 2.5%;">
                        
                        <?php if($section_name == 'resources'): ?>
                            <div class = "row" >
                                <div class="col-md-8">
                                    <h3 style = "margin: 0%;"><i><?= ucfirst( 'collection' ) ?></i></h3>
                                </div>
                                <div class = "col-md-4 text-right">
                                    <?=  Html::a( '<i class="fas fa-angle-down"></i>', '', ['class' => 'btn submit-button pull-left display-section', 'id' => 'show-'.$section_name, 'style' => 'float: unset !important;']) ?>
                                </div>
                            </div>
                            <hr style="background-color: white;">
                            <table class="table table-striped table-bordered participants-table">     
                                <tr class = "dataset-table-header-row">
                                    <td class = "dataset-header-column"> <?= 'Collection' ?> </td>
                                </tr>
                            </table>

                        <?php else: ?>
                            <div class = "row" >
                                <div class="col-md-8">
                                    <h3 style = "margin: 0%;"><i><?= ucfirst( $section_name ) ?></i></h3>
                                </div>
                                <div class = "col-md-4 text-right">
                                    <?=  Html::a( '<i class="fas fa-angle-down"></i>', '', ['class' => 'btn submit-button pull-left display-section', 'id' => 'show-'.$section_name, 'style' => 'float: unset !important;']) ?>
                                </div>
                            </div>
                            <hr style="background-color: white;">
                        <?php endif; ?>
                        <div class = "section-<?=$section_name?>" style = "margin: 2%;">
                            <?php foreach ($section as $section_key => $section_value): ?>
                            <!-- SECTION FIELDS (E.G. SURVEY NAME, SURVEY ID) -->
                            
                                <?php if( is_array($section_value) ): ?>
                                    <!-- MANY TO MANY (E.G. RESOURCES => RESOURCE 1, RESOURCE 2) -->
                                    <table class="table table-striped table-bordered participants-table">     
                                        <tr class = "dataset-table-header-row">
                                            <?php if(!is_array($section_value)){ print_r(array_keys($section_value)); exit(0); }?>
                                            <?php foreach (array_keys($section_value) as $entity_column): ?>
                                                <td class = "dataset-header-column"> <?=ucfirst( $entity_column )?> </td>
                                            <?php endforeach; ?>
                                        </tr>
                                        <tr>
                                            <?php foreach (array_keys($section_value) as $entity_column): ?>
                                                <td> 
                                                    <?php if($entity_column == 'image'): ?>
                                                        <img id = "image-preview" src="data:image/png;base64,<?=base64_encode( $section_value[$entity_column] )?>"/>
                                                    <?php elseif(is_array($section_value[$entity_column])): ?>
                                                        <?= $entity_column ?>
                                                    <?php else: ?>
                                                        <?= $section_value[$entity_column] ?>
                                                    <?php endif; ?>
                                                </td>
                                            <?php endforeach; ?>
                                        </tr>
                                    </table>
                                <?php endif; ?>
                            <!-- END SECTION FIELDS (E.G. SURVEY NAME, SURVEY ID) -->
                            <?php endforeach; ?>
                        </div>
                        <div class = "row button-row">
                            <div class = "col-md-10"></div>
                            <div class = "col-md-12">
                                <?= Html::a('Edit '.$section_name, $tabs[ucfirst($section_name)]['link'].$surveyid, ['class' => 'btn btn-primary submit-button', 'name' => 'next']) ?>
                            </div>
                        </div>

                    </div>
                    <!-- END SURVEY / RESOURCES / QUESTIONS / PARTICIPANTS / BADGES -->
                <?php endforeach; ?>
            </div>
            <div class = "row button-row">
                <div class = "col-md-10"></div>
                <div class = "col-md-1">
                    <?= Html::a( 'Previous', $tabs['Participants']['link'].$surveyid, ['class' => 'btn btn-primary submit-button ', 'name' => 'test-name']); ?>
                </div>
                <div class = "col-md-1">
                    <?= Html::submitButton('Finish', ['class' => 'btn btn-primary submit-button', 'name' => 'finalize']) ?>
                </div>
            </div>
        <?php ActiveForm::end(); ?>
    </div>

</div>



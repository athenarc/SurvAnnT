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
            <div class = "survey-form-box">
                <div class = "header-label">
                    Basic Information
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <?= $form->field($survey, 'name')->textInput()->label() ?>
                    </div>
                    <div class="col-md-4">
                        <?= $form->field($survey, 'starts')->widget
                            (
                                DateTimePicker::className(), 
                                (
                                    [
                                    'type' => DateTimePicker::TYPE_COMPONENT_PREPEND,
                                    'name' => 'start_time',
                                    'size' => 'md',
                                    'pluginOptions' => 
                                        [
                                            'autoclose' => true,
                                            'format' => 'yyyy/mm/dd hh:ii'
                                        ]
                                    ]
                                ) 
                            )->label()  
                        ?>
                    </div>
                    <div class="col-md-4">
                        <?= $form->field($survey, 'ends')->widget
                        (
                                DateTimePicker::className(), 
                                (
                                    [
                                    'type' => DateTimePicker::TYPE_COMPONENT_PREPEND,
                                    'name' => 'end_time',
                                    'size' => 'md',
                                    'pluginOptions' => 
                                        [
                                            'autoclose' => true,
                                            'format' => 'yyyy/mm/dd hh:ii'
                                        ]
                                    ]
                                ) 
                            )->label()  
                        ?>
                    </div>
                </div>
                <div class = "header-label">
                    Additional Information
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($survey, 'about')->textarea()->label() ?>
                    </div>
                    <div class="col-md-4">
                        <?= $form->field($survey, 'fields')->widget
                            (
                                Select2::className(), 
                                (
                                    [
                                    'name' => 'survey-fields-selection',
                                    'data' => $fields,
                                    'maintainOrder' => true,
                                    'options' => [ 'multiple' => true ],
                                    'pluginOptions' => [ 'allowClear' => true, 'tags' => true ],
                                    ]
                                )
                            )->label()  
                            ?>
                    </div>
                    <div class="col-md-2">
                        <?= $form->field($survey, 'locked')->dropDownList([ 0 => 'Open', 1 => 'Locked'])->label() ?>
                    </div>
                </div>
                <div class = "header-label">
                    Campaign Goals
                </div>
                
                <div class="row">
                    <div class="col-md-3">
                        <?= $form->field($survey, 'minRespPerRes')->textInput()->label() ?>
                    </div>
                    <div class="col-md-3">
                        <?= $form->field($survey, 'maxRespPerRes')->textInput()->label() ?>
                    </div>
                    <div class="col-md-3">
                        <?= $form->field($survey, 'minResEv')->textInput()->label() ?>
                    </div>
                    <div class="col-md-3">
                        <?= $form->field($survey, 'maxResEv')->textInput()->label() ?>
                    </div>
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
        <?php ActiveForm::end(); ?>
    </div>

</div>


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
</style>
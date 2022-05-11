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
            <?php include 'tabs.php'; ?>
        </div>

        <?php $form = ActiveForm::begin(['options' => ['class' => 'survey-create']]); ?>

            <div class = "row button-row">
                <div class = "col-md-10"></div>
                <div class = "col-md-1">
                    <!-- Html::a( 'Back', Yii::$app->request->referrer, ['class' => 'btn btn-primary submit-button ', 'name' => 'test-name']); -->
                </div>
                <div class = "col-md-1">
                    <?= Html::submitButton('Next', ['class' => 'btn btn-primary submit-button ']) ?>
                </div>
            </div>
            <div class = "col-md-12 dataset-form">
            <table class="table table-striped table-bordered participants-table">     
                <tr class = "dataset-table-header-row">
                    <td class = "dataset-header-column"> Campaign id </td>
                    <td class = "dataset-header-column"> Starts </td>
                    <td class = "dataset-header-column"> Ends </td>
                    <td class = "dataset-header-column"> Availability <a class = "fas fa-info-circle link-icon white" title = "Select Open to make this survey available to all platform users, or Locked to invite those that you want." style = "color: white !important;"></a></td>
                </tr>
                <tr>
                    <td><?= $form->field($survey, 'name')->textInput()->label(false) ?></td>
                    <td>
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
                            )->label(false)  
                        ?>
                    </td>
                    <td>
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
                            )->label(false)  
                        ?>
                    </td>
                    <td> <?= $form->field($survey, 'locked')->dropDownList([ 0 => 'Open', 1 => 'Locked'])->label(false) ?> </td>
                </tr>
                <tr class = "dataset-table-header-row">
                    <td colspan = "3" class = "dataset-header-column"> Description </td>
                    <td class = "dataset-header-column">Field</td>
                </tr>
                <tr>
                    <td colspan = "3">
                        <?= $form->field($survey, 'about')->textarea()->label(false) ?>
                    </td>
                    <td style = "width: 30%;"> 

                        <?= $form->field($survey, 'fields')->widget
                        (
                            Select2::className(), 
                            (
                                [
                                'name' => 'survey-fields-selection',
                                'data' => $fields,
                                'maintainOrder' => true,
                                'theme' => \kartik\select2\Select2::THEME_BOOTSTRAP ,
                                'options' => [ 'multiple' => true ],
                                'pluginOptions' => [ 'allowClear' => true, 'tags' => true ],
                                ]
                            )
                        )->label(false)  
                        ?>

                    </td>
                </tr>
                <tr class = "dataset-table-header-row">
                    <td colspan = "2" class = "dataset-header-column"> Evaluations per Resource </td>
                    <td colspan = "2" class = "dataset-header-column"> Resources Evaluated </td>
                </tr>
                <tr class = "dataset-table-header-row">
                    <td class = "dataset-header-column"> Min </td>
                    <td class = "dataset-header-column"> Max </td>
                    <td class = "dataset-header-column"> Min </td>
                    <td class = "dataset-header-column"> Max </td>
                </tr>
                <tr>
                    <td><?= $form->field($survey, 'minRespPerRes')->textInput()->label(false) ?></td>
                    <td><?= $form->field($survey, 'maxRespPerRes')->textInput()->label(false) ?></td>
                    <td><?= $form->field($survey, 'minResEv')->textInput()->label(false) ?></td>
                    <td><?= $form->field($survey, 'maxResEv')->textInput()->label(false) ?></td>
                </tr>
                <tr class = "dataset-table-header-row">
                    <td colspan = "2" class = "dataset-header-column"> Capture Response Times <a class = "fas fa-info-circle link-icon white" title = "Capture the time needed for a participant to provide an annotation for a resource. Participants are notified when this option is on." style = "color: white !important;"></a></td>
                    <td colspan = "2" class = "dataset-header-column"> Resource Selection Methodology <a class = "fas fa-info-circle link-icon white" title = "Determine how the resources will be retrieved in annotation time. " style = "color: white !important;"></a></td>
                </tr>
                <tr>
                    <td colspan = "2" >
                        <?= $form->field($survey, 'time')->checkbox([], false)->label(false) ?>
                    </td>
                    <td colspan = "2" >
                        <!-- $form->field($survey, 'randomness')->checkbox([], false)->label(false) -->
                         <?= $form->field($survey, 'randomness')->dropDownList([ 0 => 'Relevance', 1 => 'Random'])->label(false) ?> 
                    </td>
                </tr>
            </table>
            </div>

        <?php ActiveForm::end(); ?>
    </div>

</div>



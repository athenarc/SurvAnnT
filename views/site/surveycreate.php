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
            <div class = "col-md-12 dataset-form">
            <table class="table table-striped table-bordered participants-table">     
                <tr class = "dataset-table-header-row">
                    <td class = "dataset-header-column"> Name </td>
                    <td class = "dataset-header-column"> Starts </td>
                    <td class = "dataset-header-column"> Ends </td>
                    <td class = "dataset-header-column"> Permissions <a class = "fas fa-info-circle link-icon white" title = "Select Open to make this survey available to all raters, or Locked to invite those that you want." style = "color: white !important;"></a></td>
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
                    <td> 

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
            </table>
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



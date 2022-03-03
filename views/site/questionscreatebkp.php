<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Questions */
/* @var $form ActiveForm */
?>
<div class="questionscreate survey-form">

    <div class ="outside-div">

        <div class = "row header-row dataset-header-row">
            <?php foreach ($tabs as $tab => $url): ?>
                <div class = "tab col-md" style = "border-bottom: <?= ( $tab == $message ) ? 'none !important;' : '' ?>">
                    <a class = "<?= ( ! $url['enabled'] ) ? 'url-disabled' : '' ?> " href = "<?= ($url['enabled']) ? $url['link'].$surveyid : null ?>" ><h5 title = "<?= $message ?>" style = "opacity: <?= ( $url['enabled'] ) ? '1' : '' ?>"> <?= $tab ?></h5></a>
                </div>
            <?php endforeach; ?>
        </div>
        <?php $form = ActiveForm::begin(); ?>
            <div class = "datasets-table"> 
                <?php foreach ($questions as $key => $question): ?>
                    <div class = "dataset-tools">
                        <div class = "dataset-header-column dataset-table-header-row text-center" colspan = "<?= $colspan ?>" > 
                            Question <?=$key + 1?> 
                            <span class = "float-right"> 
                                <a id = "dataset-<?=$key?>" class="fas fa-eye link-icon white hide-dataset"></a> 
                                <a id = "dataset-<?=$key?>" class="fas fa-times link-icon white delete-dataset"></a> 
                            </span> 
                        </div>
                    </div>
                    <div class = "col-md-12 dataset-form">
                        
                        <table class="table table-striped table-bordered participants-table table-<?=$key?>">     
                            
                            <tr class = "dataset-table-header-row">
                                <td class = "dataset-header-column" colspan = "1"> Question </td>
                                <td class = "dataset-header-column" colspan = "1"> Tooltip </td>
                                
                            </tr>
                            <tr>
                                
                                <td colspan = "1"> <?= $form->field($question, "[$key]question")->textarea()->label(false) ?> </td>    
                                <td> <?= $form->field($question, "[$key]tooltip")->label(false) ?> </td>
                                
                            </tr>
                            <tr class = "dataset-table-header-row">
                                <td class = "dataset-header-column" colspan = "1"> Answer Type </td>
                                <td class = "dataset-header-column" colspan = "<?= $colspan ?>"> <?= $question->answertype == 'textInput' ? 'Answer' : 'Answer values <a data-toggle="modal" data-target=".help" class="fas fa-info-circle tooltip-icon" title="" aria-hidden="true"></a>' ?></td>
                                
                            </tr>
                            <tr>
                                <td> <?= $form->field($question, "[$key]answertype")->dropDownList($answertypes)->label(false) ?> </td>
                                <td colspan = "<?= $colspan ?>" style = "display: <?= $question->answertype == 'textInput' ? 'block;' : 'none;' ?> "> <?= $form->field($question, "[$key]answer")->textarea([ 'placeholder' => 'This is a question answer...'])->label(false) ?> </td>
                                <td colspan = "<?= $colspan ?>" style = "display: <?= $question->answertype == 'textInput' ? 'none;' : 'block;' ?> "> <?= $form->field($question, "[$key]answervalues")->textarea([ 'placeholder' => "{\n\t\"1\" : \"value\"\n}"])->label(false) ?> </td>
                                
                            </tr>
                            
                        </table>
                    </div>
                    <?= $form->field($question, "[$key]ownerid")->hiddenInput()->label(false) ?>
                    <?= $form->field($question, "[$key]destroy")->hiddenInput(['id' => 'destroy-'.$key])->label(false) ?>
                <?php endforeach; ?>
                <div class = "row button-row-2">
                    <div class = "col-md-10"></div>
                    <div class = "col-md-2">
                        <?= Html::a('Add question', ['site/survey-create'], ['class' => 'btn btn-primary submit-button add-dataset', 'name' => 'add']) ?>
                    </div>
                </div>
            </div>
            <div class = "row button-row">
                <div class = "col-md-10"></div>
                <div class = "col-md-1">
                    <?= Html::a( 'Previous', $tabs['Resources']['link'].$surveyid, ['class' => 'btn btn-primary submit-button ', 'name' => 'test-name']); ?>
                </div>
                <div class = "col-md-1">
                    <?= Html::submitButton('Next', ['class' => 'btn btn-primary submit-button ']) ?>
                </div>
            </div>
        <?php ActiveForm::end(); ?>
    </div>

</div><!-- questionscreate -->

<div class="modal fade help" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4>JSON format</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>If your survey question needs a <b>Radio List</b> format answer, then the values of your question's input answer must be json formated.</p>
        <p>The following code demonstrates a valid JSON format.</p>
        <div class = "code-block-background">
        <div style = "color: white;"> {</div>
        &nbsp;&nbsp;&nbsp;&nbsp;<span class = "teal">"</span><span class = "green">1</span><span class = "teal">"</span><span class = "white">: </span><span class = "teal">"</span><span class = "green">Agree</span><span class = "teal">"</span><span class = "white">,</span><br>
        &nbsp;&nbsp;&nbsp;&nbsp;<span class = "teal">"</span><span class = "green">2</span><span class = "teal">"</span><span class = "white">: </span><span class = "teal">"</span><span class = "green">Neither agree nor disagree</span><span class = "teal">"</span><span class = "white">,</span><br>
        &nbsp;&nbsp;&nbsp;&nbsp;<span class = "teal">"</span><span class = "green">3</span><span class = "teal">"</span><span class = "white">: </span><span class = "teal">"</span><span class = "green">Agree</span><span class = "teal">"</span>
        <p class = "white" >}</p>

        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary submit-button" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

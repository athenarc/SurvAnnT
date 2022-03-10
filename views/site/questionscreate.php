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
                    <div id = "dataset-tools-<?=$key?>" class = "dataset-tools">
                        <div class = "dataset-header-column dataset-table-header-row resource-header-row text-center" colspan = "<?= $colspan ?>" > 
                            <span class = "float-left" style = "width: 30%; text-align: left !important;"> &nbsp;
                                 <!-- Html::checkbox('agree-question-'.$key, true, ['id' => 'use-question-'.$key, 'label' => 'Use']) &nbsp; -->
                            </span>
                            <span class = "center" style = "width: 40%;">
                                Question
                            </span>
                            <span class = "float-right" style = "width: 30%; text-align: right;"> 
                                <a id = "dataset-<?=$key?>" class="fas fa-eye link-icon white hide-dataset"></a> 
                                <a id = "dataset-<?=$key?>" class="fas fa-times link-icon white delete-question"></a> 
                            </span> 
                        </div>
                    </div>


                    <div id = "dataset-form-<?=$key?>"  class = "col-md-12 dataset-form">
                        
                        <table class="table table-striped table-bordered participants-table table-<?=$key?>">     
                            
                            <tr class = "dataset-table-header-row">
                                <td class = "dataset-header-column" colspan = "1"> Question </td>
                                <td class = "dataset-header-column" colspan = "2"> Tooltip </td>
                                
                            </tr>
                            <tr>
                                
                                <td colspan = "1"> <?= $form->field($question, "[$key]question")->textarea()->label(false) ?> </td>    
                                <td colspan = "2"> <?= $form->field($question, "[$key]tooltip")->label(false) ?> </td>
                                
                            </tr>
                            <tr class = "dataset-table-header-row">
                                <td class = "dataset-header-column" colspan = "1" style = "width:50%;"> Answer Type </td>
                                <td id =  "answer-header-text-input-<?=$key?>" class = "dataset-header-column" style = ""> Answer </td>                                
                            </tr>
                            <tr>
                                <td colspan = "1" style = "width: 45%;"> 
                                    <?= $form->field($question, "[$key]answertype")->dropDownList($answertypes)->label(false) ?> 
                                </td>
                                <td id = "textInput-<?=$key?>" colspan = "2" style = "display: <?= $question->answertype == 'textInput' ? 'table-cell;' : 'none;' ?> "> 
                                    <?= $form->field($question, "[$key]answer")->textarea([ 'placeholder' => 'This is a question answer...'])->label(false) ?> 
                                </td>
                                <td id = "radioList-<?=$key?>" colspan = "<?= 2 ?>" style = "display: <?= $question->answertype == 'radioList' ? 'table-cell;' : 'none;' ?> "> 
                                    <?php 
                                    $counter = 0;
                                    $question->answervalues = ($question->answervalues == '') ? $likert_5 : json_decode($question->answervalues) ;
                                    ?>
                                    
                                    <table class = "table table-striped table-bordered">
                                        <tr class = "dataset-table-header-row">
                                            <td class = "dataset-header-column" colspan = "1" style = "width: 60%;"> User Answer </td>
                                            <td class = "dataset-header-column" colspan = "1" style = "width: 30%;"> Stored Value </td>
                                            <td class = "dataset-header-column" colspan = "1" style = "width: 10%;">  </td>
                                        </tr>
                                        <?php foreach ($question->answervalues as $ans_key => $ans_val): ?>
                                            <?php 
                                            $answer = (array)$ans_val;
                                            $answer_key = key($answer);
                                            $answer_val = end($answer);
                                            ?>
                                            <tr>
                                                <td>
                                                    <input type="text" value="<?=$answer_val?>" name="question-<?=$key?>-radioList-<<?=$counter?>>-answer" class = "form-control" style = "">
                                                </td>
                                                <td>
                                                    <input type="text" value="<?=$answer_key?>" name="question-<?=$key?>-radioList-<<?=$counter?>>-value" class = "form-control" style = "">
                                                </td>
                                                <td>
                                                    <a id = "delete-radioList-<?=$key?>-<?=$answer_key?>" class="fas fa-trash-alt link-icon delete-radioList-key"></a>
                                                </td>
                                            </tr>
                                            <?php $counter ++; ?>
                                        <?php endforeach; ?>
                                    </table>
                                </td>
                                <td id = "Likert-5-<?=$key?>" colspan = "<?= 2 ?>" style = "display: <?= $question->answertype == 'Likert-5' ? 'table-cell;' : 'none;' ?> "> 
                                    <?php 
                                    $counter = 0;
                                    $radiolist = ( ! $question->isNewRecord )  ? $question->answervalues :  $likert_5;
                                    ?>
                                    <table class = "table table-striped table-bordered">
                                        <tr class = "dataset-table-header-row">
                                            <td class = "dataset-header-column" colspan = "1" style = "width: 60%;"> User Answer </td>
                                            <td class = "dataset-header-column" colspan = "1" style = "width: 30%;"> Stored Value </td>
                                            <td class = "dataset-header-column" colspan = "1" style = "width: 10%;">  </td>
                                        </tr>
                                        <?php foreach ($radiolist as $ans_key => $ans_val): ?>
                                            <?php 
                                            $answer = (array)$ans_val;
                                            $answer_key = key($answer);
                                            $answer_val = end($answer);
                                            ?>
                                            <tr>
                                                <!-- FOR EACH LIKERT VAL -->
                                                <td>
                                                    <input type="text" value="<?=$answer_val?>" name="question-<?=$key?>-Likert-5-<<?=$counter?>>-answer" class = "form-control">
                                                </td>
                                                <td>
                                                    <input type="text" value="<?=$answer_key?>" name="question-<?=$key?>-Likert-5-<<?=$counter?>>-value" class = "form-control">
                                                </td>
                                                <!-- FOR EACH LIKERT VAL -->
                                                <td>
                                                    <a id = "delete-radioList-<?=$key?>-<?=$answer_key?>" class="fas fa-trash-alt link-icon delete-radioList-key"></a>
                                                </td>
                                            </tr>
                                            <?php $counter ++; ?>
                                        <?php endforeach; ?>
                                    </table>
                                </td>
                                <td id = "Likert-7-<?=$key?>" colspan = "<?= 2 ?>" style = "display: <?= $question->answertype == 'Likert-7' ? 'table-cell;' : 'none;' ?> "> 
                                    <?php 
                                    $counter = 0;
                                    $radiolist = ( ! $question->isNewRecord ) ? $question->answervalues : $likert_7;
                                    ?>
                                    <table class = "table table-striped table-bordered">
                                        <tr class = "dataset-table-header-row">
                                            <td class = "dataset-header-column" colspan = "1" style = "width: 60%;"> User Answer </td>
                                            <td class = "dataset-header-column" colspan = "1" style = "width: 30%;"> Stored Value </td>
                                            <td class = "dataset-header-column" colspan = "1" style = "width: 10%;">  </td>
                                        </tr>
                                        <?php foreach ($radiolist as $ans_key => $ans_val): ?>
                                            <?php 
                                            $answer = (array)$ans_val;
                                            $answer_key = key($answer);
                                            $answer_val = end($answer);
                                            ?>
                                            <tr>
                                                <td>
                                                    <input type="text" value="<?=$answer_val?>" name="question-<?=$key?>-Likert-7-<<?=$counter?>>-answer" class = "form-control">
                                                </td>
                                                <td>
                                                    <input type="text" value="<?=$answer_key?>" name="question-<?=$key?>-Likert-7-<<?=$counter?>>-value" class = "form-control">
                                                </td>
                                                <td>
                                                    <a id = "delete-radioList-<?=$key?>-<?=$answer_key?>" class="fas fa-trash-alt link-icon delete-radioList-key"></a> 
                                                </td>
                                            </tr>
                                            <?php $counter ++; ?>
                                        <?php endforeach; ?>
                                    </table>
                                </td>
                            </tr>
                            
                        </table>
                    </div>
                    <?= $form->field($question, "[$key]ownerid")->hiddenInput()->label(false) ?>
                    <?= $form->field($question, "[$key]destroy")->hiddenInput(['id' => 'destroy-'.$key])->label(false) ?>
                    <input type="hidden" id ='Likert-5' name="" value="<?= Html::encode( ( json_encode($likert_5) ) ) ?>">
                    <input type="hidden" id ='Likert-7' name="" value="<?= Html::encode( ( json_encode($likert_7) ) )?>">
                <?php endforeach; ?>
                <div class = "row button-row-2">
                    <div class = "col-md-10"></div>
                    <div class = "col-md-2">
                        <?= Html::a('Add question', ['site/survey-create'], ['class' => 'btn btn-primary submit-button add-question', 'name' => 'add']) ?>
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

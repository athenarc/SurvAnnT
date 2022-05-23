<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\web\View;
use yii\widgets\Pjax;

$this->registerJsFile('@web/js/questionscreate.js', ['position' => View::POS_END, 'depends' => [\yii\web\JqueryAsset::className()]]);

/* @var $this yii\web\View */
/* @var $model app\models\Questions */
/* @var $form ActiveForm */
?>
<div class="questionscreate">

    <div class ="outside-div">
        <div class = 'error-div' style = "display: none;"></div>
        <div class = "row header-row dataset-header-row">
            <?php include 'tabs.php'; ?>
        </div>
        <div class = "row button-row">
            <div class = "col-md-10 d-flex align-items-center">
                <i class="fa fa-info-circle helper-message" ></i>&nbsp;
                Create the Questions to be used for annotation/survey purposes
            </div>
            <div class = "col-md-2  text-right">
                <?= Html::a( 'Previous', $tabs['Resources']['link'].$survey->id, ['class' => 'btn btn-primary', 'name' => 'test-name']); ?>
                <?= Html::a( 'Next', $tabs['Participants']['link'].$survey->id, ['class' => 'btn btn-primary', 'name' => 'test-name']); ?>
                 <!-- Html::submitButton('Next', ['class' => 'btn btn-primary submit-button ']) ?> -->
            </div>
        </div>

            

            <div class="survey-form-box" style="margin-bottom: 0% !important; padding-bottom: 0% !important ;">
                <div class = "row">
                    <div class = "col-md-12">
                        <div class = "header-label">
                            Selected Questions 
                        </div>
                    </div>
                </div>
                <div class = "row">
                    <div class="col-md-12 text-right">
                        <?= ($survey->getSurveytoquestions()->all()) ? Html::a( 'Delete All Questions', ['questions/questions-delete-all', 'surveyid' => $survey->id], ['class' => 'btn btn-primary  db-question-select', 'name' => 'delete-all']) : '' ?>
                         <?= Html::button( 'Reuse Existing Questions', ['class' => 'btn btn-primary', 'data-toggle' => "modal", 'data-target' => "#db-questions"]) ?>
                         <?= Html::button( 'Create Questions', ['class' => 'btn btn-primary', 'data-toggle' => "modal", 'data-target' => "#user-questions"]) ?>
                         <input type="hidden" id="surveyId" value="<?=$survey->id?>" name="">
                    </div>                
                </div>
                <br>
                <?php $form = ActiveForm::begin(['options' => ['id'=> 'survey-questions-form',  'class' => 'resource-before-form']]); ?>
                <table class="table table-striped table-bordered participants-table">     
                    <tr class = "dataset-table-header-row">
                        <td class = "dataset-header-column" colspan = "1"> Question </td>
                        <td class = "dataset-header-column" colspan = "1"> Tooltip </td>
                        <td class = "dataset-header-column" colspan = "1"> Answer Format </td>
                        <td class = "dataset-header-column" colspan = "1"> Answer </td>                        
                        <td class = "dataset-header-column" colspan = "1"> Public </td>
                        <td class = "dataset-header-column" colspan = "1"> Actions </td>
                    </tr>
                    <?php foreach ($SurveyQuestions as $k => $SurveyQuestion): ?>
                        <tr>
                            <td>
                                <span class="edit-question-question-<?=$SurveyQuestion->id?>" style ="display: none;">
                                    <?= $form->field($SurveyQuestion, "question")->textArea(['name' => 'question-question-'.$SurveyQuestion->id, 'id' => 'question-question-'.$SurveyQuestion->id])->label(false) ?>
                                </span>
                                <span class="edit-question-question-<?=$SurveyQuestion->id?> question-question-<?=$SurveyQuestion->id?>-text">
                                    <?= $SurveyQuestion->question ?>
                                </span>    
                            </td>
                            <td>
                                <span class="edit-question-tooltip-<?=$SurveyQuestion->id?>" style ="display: none;">
                                    <?= $form->field($SurveyQuestion, "tooltip")->textInput(['name' => 'question-tooltip-'.$SurveyQuestion->id, 'id' => 'question-tooltip-'.$SurveyQuestion->id])->label(false) ?>
                                </span>
                                <span class="edit-question-tooltip-<?=$SurveyQuestion->id?> question-tooltip-<?=$SurveyQuestion->id?>-text">
                                    <?= $SurveyQuestion->tooltip ?>
                                </span>   
                                    
                            </td>
                            <td>
                                <span class="edit-question-answertype-<?=$SurveyQuestion->id?>" style ="display: none;">
                                    <?= $form->field($SurveyQuestion, "answertype")->dropDownList($answertypes, ['name' => 'question-answertype-'.$SurveyQuestion->id, 'id' => "questions-$SurveyQuestion->id-answertype"])->label(false) ?>
                                </span>
                                <span class="edit-question-answertype-<?=$SurveyQuestion->id?> question-answertype-<?=$SurveyQuestion->id?>-text">
                                    <?= ucwords(strtolower($SurveyQuestion->answertype)) ?>
                                </span>  
                                
                            </td>
                            
                            <td id = "textInput-<?=$SurveyQuestion->id?>" colspan = "1" style = "display: <?= $SurveyQuestion->answertype == 'textInput' || $SurveyQuestion->isNewRecord ? 'table-cell;' : 'none;' ?> "> 
                                            
                                <span class="edit-question-answer-<?=$SurveyQuestion->id?>" style ="display: none;">
                                        <?= $form->field($SurveyQuestion, "[$k]answer")->textarea([ 'placeholder' => 'This is a question answer...', 'id' => "questions-$SurveyQuestion->id-answer"])->label(false) ?> 
                                </span>
                                <span class="edit-question-answer-<?=$SurveyQuestion->id?> question-answertype-<?=$SurveyQuestion->id?>-text">
                                    <?=$SurveyQuestion->answer?>
                                </span>
                            </td>
                            <td id = "radioList-<?=$SurveyQuestion->id?>" colspan = "<?= 1 ?>" style = "display: <?= $SurveyQuestion->answertype == 'radioList' ? 'table-cell;' : 'none;' ?> "> 
                                <?php 
                                $counter = 0;
                                $SurveyQuestion->answervalues = ($SurveyQuestion->answervalues == '') ? $likert_5 : json_decode($SurveyQuestion->answervalues) ;
                                ?> 
                                Table
                                <a id ="link-show-radioList-5-<?=$SurveyQuestion->id?>" class="fa-solid fa-caret-down link-icon"></a>
                                <table id = "table-show-radioList-5-<?=$SurveyQuestion->id?>" class = "table table-striped table-bordered mb-0" style = "display: none;">
                                    <tr class = "dataset-table-header-row">
                                        <td class = "dataset-header-column" colspan = "1" style = "width: 60%;"> User Answer </td>
                                        <td class = "dataset-header-column" colspan = "1" style = "width: 30%;"> Stored Value </td>
                                        <td class = "dataset-header-column class="edit-question-answer-<?=$SurveyQuestion->id?>" colspan = "1" style = "width: 10%;">  </td>
                                    </tr>
                                    <?php foreach ($SurveyQuestion->answervalues as $ans_key => $ans_val): ?>
                                        <?php 
                                        $answer = (array)$ans_val;
                                        $answer_key = key($answer);
                                        $answer_val = end($answer);
                                        ?>
                                        <tr>
                                            <td>
                                                
                                                <span class="edit-question-answer-<?=$SurveyQuestion->id?>" style ="display: none;">
                                                    <input type="text" value="<?=$answer_val?>" name="question-<?=$k?>-radioList-<<?=$counter?>>-answer" class = "form-control" style = "">
                                                </span>
                                                <span class="edit-question-answer-<?=$SurveyQuestion->id?> question-answertype-<?=$SurveyQuestion->id?>-text-answer-<?=$counter?>">
                                                    <?=$answer_val?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="edit-question-answer-<?=$SurveyQuestion->id?>" style ="display: none;">
                                                    <input type="text" value="<?=$answer_key?>" name="question-<?=$k?>-radioList-<<?=$counter?>>-value" class = "form-control" style = "">
                                                </span>
                                                <span class="edit-question-answer-<?=$SurveyQuestion->id?> question-answertype-<?=$SurveyQuestion->id?>-text-key-<?=$counter?>">
                                                    <?=$answer_key?>
                                                </span>
                                                
                                            </td>
                                            <td class="edit-question-answer-<?=$SurveyQuestion->id?>" style ="display: none;">
                                                <a id = "delete-radioList-<?=$k?>-<?=$answer_key?>" class="fas fa-trash-alt link-icon delete-radioList-key"></a>
                                            </td>
                                        </tr>
                                        <?php $counter ++; ?>
                                    <?php endforeach; ?>
                                </table>
                            </td>
                            <td id = "Likert-5-<?=$SurveyQuestion->id?>" colspan = "<?= 1 ?>" style = "display: <?= $SurveyQuestion->answertype == 'Likert-5' ? 'table-cell;' : 'none;' ?> "> 
                                <?php 
                                $counter = 0;
                                $radiolist = ( ! $SurveyQuestion->isNewRecord )  ? $SurveyQuestion->answervalues :  $likert_5;
                                ?>
                                Table
                                <a id ="link-show-Likert-5-<?=$SurveyQuestion->id?>" class="fa-solid fa-caret-down link-icon"></a>
                                <table id = "table-show-Likert-5-<?=$SurveyQuestion->id?>" class = "table table-striped table-bordered mb-0" style = "display: none;">
                                    <tr class = "dataset-table-header-row">
                                        <td class = "dataset-header-column" colspan = "1" style = "width: 60%;"> User Answer </td>
                                        <td class = "dataset-header-column" colspan = "1" style = "width: 30%;"> Stored Value </td>
                                        <td class = "dataset-header-column edit-question-answer-<?=$SurveyQuestion->id?>" colspan = "1" style = "display: none;width: 10%;">  </td>
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
                                                <span class="edit-question-answer-<?=$SurveyQuestion->id?>" style ="display: none;">
                                                    <input type="text" value="<?=$answer_val?>" name="question-<?=$k?>-Likert-5-<<?=$counter?>>-answer" class = "form-control">
                                                </span>
                                                <span class="edit-question-answer-<?=$SurveyQuestion->id?> question-answertype-<?=$SurveyQuestion->id?>-text-answer-<?=$counter?>">
                                                    <?=$answer_val?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="edit-question-answer-<?=$SurveyQuestion->id?>" style ="display: none;">
                                                    <input type="text" value="<?=$answer_key?>" name="question-<?=$k?>-Likert-5-<<?=$counter?>>-value" class = "form-control">
                                                </span>
                                                <span class="edit-question-answer-<?=$SurveyQuestion->id?> question-answertype-<?=$SurveyQuestion->id?>-text-key-<?=$counter?>">
                                                    <?=$answer_key?>
                                                </span>
                                            </td>
                                            <!-- FOR EACH LIKERT VAL -->
                                            <td class="edit-question-answer-<?=$SurveyQuestion->id?>" style ="display: none;">
                                                <a id = "delete-radioList-<?=$k?>-<?=$answer_key?>" class="fas fa-trash-alt link-icon delete-radioList-key"></a>
                                            </td>
                                        </tr>
                                        <?php $counter ++; ?>
                                    <?php endforeach; ?>
                                </table>
                            </td>
                            <td id = "Likert-7-<?=$SurveyQuestion->id?>" colspan = "<?= 1 ?>" style = "display: <?= $SurveyQuestion->answertype == 'Likert-7' ? 'table-cell;' : 'none;' ?> "> 
                                <?php 
                                $counter = 0;
                                $radiolist = ( ! $SurveyQuestion->isNewRecord ) ? $SurveyQuestion->answervalues : $likert_7;
                                ?>
                                Table
                                <a id ="link-show-Likert-7-<?=$SurveyQuestion->id?>" class="fa-solid fa-caret-down link-icon"></a>
                                <table id = "table-show-Likert-7-<?=$SurveyQuestion->id?>" class = "table table-striped table-bordered mb-0" style = "display: none;">
                                    <tr class = "dataset-table-header-row">
                                        <td class = "dataset-header-column" colspan = "1" style = "width: 60%;"> User Answer </td>
                                        <td class = "dataset-header-column" colspan = "1" style = "width: 30%;"> Stored Value </td>
                                        <td class = "dataset-header-column edit-question-answer-<?=$SurveyQuestion->id?>" colspan = "1" style = "display: none;width: 10%;">  </td>
                                    </tr>
                                    <?php foreach ($radiolist as $ans_key => $ans_val): ?>
                                        <?php 
                                        $answer = (array)$ans_val;
                                        $answer_key = key($answer);
                                        $answer_val = end($answer);
                                        ?>
                                        <tr>
                                            <td>
                                                <span class="edit-question-answer-<?=$SurveyQuestion->id?>" style ="display: none;">
                                                    <input type="text" value="<?=$answer_val?>" name="question-<?=$k?>-Likert-7-<<?=$counter?>>-answer" class = "form-control">
                                                </span>
                                                <span class="edit-question-answer-<?=$SurveyQuestion->id?> question-answertype-<?=$SurveyQuestion->id?>-text-answer-<?=$counter?>">
                                                    <?=$answer_val?>
                                                </span>
                                                
                                            </td>
                                            <td>
                                                <span class="edit-question-answer-<?=$SurveyQuestion->id?>" style ="display: none;">
                                                    <input type="text" value="<?=$answer_key?>" name="question-<?=$k?>-Likert-7-<<?=$counter?>>-value" class = "form-control">
                                                </span>
                                                <span class="edit-question-answer-<?=$SurveyQuestion->id?> question-answertype-<?=$SurveyQuestion->id?>-text-key-<?=$counter?>">
                                                    <?=$answer_key?>
                                                </span>
                                            </td>
                                            <td class="edit-question-answer-<?=$SurveyQuestion->id?>" style ="display: none;">
                                                <a id = "delete-radioList-<?=$k?>-<?=$answer_key?>" class="fas fa-trash-alt link-icon delete-radioList-key"></a> 
                                            </td>
                                        </tr>
                                        <?php $counter ++; ?>
                                    <?php endforeach; ?>
                                </table>
                            </td>
                            <td>
                                <span class="edit-question-allowusers-<?=$SurveyQuestion->id?>" style ="display: none;">
                                    <?=  $form->field($SurveyQuestion, "[$k]allowusers")->checkbox(['name' => 'allowusers-'.$SurveyQuestion->id, 'id' => 'allowusers-'.$SurveyQuestion->id, 'label' => false, 'id' => 'question-allowusers-'.$SurveyQuestion->id])
                                    ?>
                                </span>
                                <span class="edit-question-allowusers-<?=$SurveyQuestion->id?>">
                                    <?php if($SurveyQuestion->allowusers): ?>
                                        Yes
                                    <?php else: ?>
                                        No
                                    <?php endif; ?> 
                                </span>        
                            </td>
                            <td>
                                <a id="questions-actions-<?=$SurveyQuestion->id?>" class="fas fa-pencil edit-question link-icon"></a>
                                <a id="questions-actions-<?=$SurveyQuestion->id?>" class="fas fa-trash-alt delete-question link-icon"></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
                <?php ActiveForm::end(); ?>
                <br>
            </div>
                
        </div>

    </div>

</div><!-- questionscreate -->

<div class="modal fade bd-example-modal-lg" id="user-questions" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div  class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        
      
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Questions Form</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body questions-modal-body">
                <!-- MODAL BODY START -->
                <div class="datasets-table">
                    <?php $form = ActiveForm::begin(['options' => ['id' => 'questions-form']]); ?> 
                        <?php foreach ($questions as $key => $question): ?>
                            <div id = "dataset-tools-<?=$key?>" class = "dataset-tools">
                                <div class = "dataset-header-column dataset-table-header-row resource-header-row text-center" colspan = "<?= $colspan ?>" > 
                                    <span class = "float-left" style = ""> &nbsp;
                                         <!-- Html::checkbox('agree-question-'.$key, true, ['id' => 'use-question-'.$key, 'label' => 'Use']) &nbsp; -->
                                    </span>
                                    <span class = "center" style = "">
                                        Question
                                    </span>
                                    <span class = "float-right" style = ""> 
                                        <a id = "dataset-<?=$key?>" class="fas fa-caret-down link-icon white hide-dataset"></a> 
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
                                        <td class = "dataset-header-column" colspan = "1" style = "width:50%;"> Answer Format </td>
                                        <td id =  "answer-header-text-input-<?=$key?>" class = "dataset-header-column" style = ""> Answer </td>                                
                                    </tr>
                                    <tr>
                                        <td colspan = "1" style = "width: 45%;"> 
                                            <?= $form->field($question, "[$key]answertype")->dropDownList($answertypes, ['id' => "questions-modal-$key-answertype"])->label(false) ?> 
                                        </td>
                                        <td id = "textInput-modal-<?=$key?>" colspan = "2" style = "display: <?= $question->answertype == 'textInput' || $question->isNewRecord ? 'table-cell;' : 'none;' ?> "> 
                                            <?= $form->field($question, "[$key]answer")->textarea([ 'placeholder' => 'This is a question answer...'])->label(false) ?> 
                                        </td>
                                        <td id = "radioList-modal-<?=$key?>" colspan = "<?= 2 ?>" style = "display: <?= $question->answertype == 'radioList' ? 'table-cell;' : 'none;' ?> "> 
                                            <?php 
                                            $counter = 0;
                                            $question->answervalues = ($question->answervalues == '') ? $likert_5 : json_decode($question->answervalues) ;
                                            ?> 
                                            Table
                                            <a id ="link-show-radioList-<?=$key?>" class="fa-solid fa-caret-down link-icon"></a>
                                            <table id = "table-show-radioList-<?=$key?>" class = "table table-striped table-bordered mb-0" style ="display: none;">
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
                                        <td id = "Likert-5-modal-<?=$key?>" colspan = "<?= 2 ?>" style = "display: <?= $question->answertype == 'Likert-5' ? 'table-cell;' : 'none;' ?> "> 
                                            <?php 
                                            $counter = 0;
                                            $radiolist = ( ! $question->isNewRecord )  ? $question->answervalues :  $likert_5;
                                            ?>
                                            Table
                                            <a id ="link-show-Likert-5-<?=$key?>" class="fa-solid fa-caret-down link-icon"></a>
                                            <table id = "table-show-Likert-5-<?=$key?>" class = "table table-striped table-bordered mb-0" style="display: none;">
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
                                        <td id = "Likert-7-modal-<?=$key?>" colspan = "<?= 2 ?>" style = "display: <?= $question->answertype == 'Likert-7' ? 'table-cell;' : 'none;' ?> "> 
                                            <?php 
                                            $counter = 0;
                                            $radiolist = ( ! $question->isNewRecord ) ? $question->answervalues : $likert_7;
                                            ?>
                                            Table
                                            <a id ="link-show-Likert-7-<?=$key?>" class="fa-solid fa-caret-down link-icon"></a>
                                            <table id = "table-show-Likert-7-<?=$key?>" class = "table table-striped table-bordered mb-0" style="display: none;">
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
                            <div class = "col-md-9"></div>
                            <div class = "col-md-3">
                                 
                                <?= Html::a('Add', 'javascript:void(0)', ['class' => 'btn btn-primary submit-button user-question-select add-question', 'value'=>'create_add', 'name' => 'add-question', 'id' => 'add-question']) ?>
                            </div>
                        </div>
                    
                </div> 
                <!-- MODAL BODY END -->
            </div>    
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>    
                <?= Html::submitButton('Submit', ['class' => 'btn btn-primary submit-button']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<div class="modal fade bd-example-modal-lg" id="db-questions" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div  class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        
      
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Upload Questions Form</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body questions-modal-body">
                <!-- MODAL BODY START -->
                <div class="datasets-table">
                    <?php $form = ActiveForm::begin(['action' => ['questions/questions-use'], 'options' => ['id' => 'questions-form']]); ?> 
                    <table class="table table-striped table-bordered participants-table table-<?=$key?>">
                        <tr class = "dataset-table-header-row">
                            <th class = "dataset-header-column">Question</th>
                            <th class = "dataset-header-column">Owner</th>
                            <th class = "dataset-header-column">Tooltip</th>
                            <th class = "dataset-header-column">Answer Format</th>
                            <th class = "dataset-header-column">Answer Values</th>
                            <th class = "dataset-header-column">Use</th>
                        </tr>
                        <?php foreach ($dbQuestions as $key => $question): ?>
                            <tr>
                                <td><?= $question->question ?></td>
                                <td><?= $question->getOwner()->select(['username'])->one()['username']?></td>
                                <td><?= $question->tooltip ?></td>
                                <td><?= ucwords(strtolower($question->answertype)) ?></td>
                                <td>
                                    <?php if($question->answertype == 'textInput'): ?>
                                    
                                        <?= $question->answer ?> 
                                 
                                    <?php else: ?>

                                        <?php 
                                            $counter = 0;
                                            $radiolist =  json_decode($question->answervalues); 
                                        ?>
                                        Table
                                        <a id ="link-show-Likert-7-<?=$key?>" class="fa-solid fa-caret-down link-icon"></a>
                                        <table id = "table-show-Likert-7-<?=$key?>" class = "table table-striped table-bordered mb-0" style = "display: none;">
                                            <tr class = "dataset-table-header-row">
                                                <td class = "dataset-header-column" colspan = "1" style = "width: 60%;"> User Answer </td>
                                                <td class = "dataset-header-column" colspan = "1" style = "width: 30%;"> Stored Value </td>
                                            </tr>
                                            <?php foreach ( $radiolist as $ans_key => $ans_val): ?>
                                                <?php 
                                                    $answer = (array)$ans_val;
                                                    $answer_key = key($answer);
                                                    $answer_val = end($answer);
                                                ?>
                                                <tr id="table-<?=$key?>">
                                                    <td>
                                                        <?=$answer_val?>
                                                    </td>
                                                    <td>
                                                        <?=$answer_key?>
                                                    </td>
                                                </tr>
                                                <?php $counter ++; ?>
                                            <?php endforeach; ?>
                                        </table>

                                    <?php endif; ?>
                                </td>
                                <td><?= Html::checkbox('agree-question-'.$question->id, $question->isNewRecord || in_array($survey->id, array_column($question->surveytoquestions, 'surveyid') ) ? true : false, ['id' => 'use-question-'.$question->id, ]) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <input type="hidden" id="surveyId" name="surveyId" value="<?=$survey->id?>">
                    </table>
                </div> 
                <!-- MODAL BODY END -->
            </div>    
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>    
                <?= Html::submitButton('Submit', ['class' => 'btn btn-primary submit-button']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
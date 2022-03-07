<?php

/* @var $this yii\web\View */
use yii\bootstrap4\Progress;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

use yii\widgets\DetailView;
$this->title = 'My Yii Application';
?>
<div class="site-index">

	<div class="outside-div">
		
		<?php $form = ActiveForm::begin(['options' => ['class' => 'rate-form']]); ?>

		<h3> Please provide us with your feedback!</h3>
		<br>
		<h4> <?= $survey->name ?> </h4>
		<br>
		<?php 	if ( $resource['type'] == 'image'): ?>

			<img src="data:image/png;base64,<?= base64_encode($resource['image']) ?>"/>

		<?php 	elseif ( $resource['type'] == 'questionaire' ): ?>

			<b><?= $resource['title'] ?></b>

		<?php 	else: ?>

			<b><?= $resource['title'] ?></b><br><br>
			<b><?= $resource['abstract'] ?></b>

		<?php 	endif; ?>

		<br><br>
		<?= "Feedback provided by user in Total: ".$user_feedback_provided_general ?> <br><br>
		<?= "Feedback provided by user: ".$user_feedback_provided ?> <br><br>

		<?= "Feedback needed: ".$survey->minResEv ?> <br><br>

		<?= "Minimum Responses Per Resource needed: ".$survey->minRespPerRes ?> <br><br>
		<?= "Maximum Responses Per Resource needed: ".$survey->maxRespPerRes ?> <br><br>

		<?php foreach ($rates as $key => $rate): ?>
            <b><span id = 'Question' > <?= $questions[$key]['question'] ?> </span></b><br>
           
                <?php if( $questions[$key]['answertype'] == 'textInput' ): ?>
                	<?= $form->field($rate, "[$key]answer")->textInput(['id' => 'rate-answer-'.$key])->label(false) ?><br>
                <?php else: ?>
                	<?= $form->field($rate, "[$key]answer")->radioList($questions[$key]['answervalues'], ['id' => 'rate-answer-'.$key])->label(false) ?><br>
                <?php endif; ?>

                <?php isset( $questions[$key]['tooltip'] ) ? '<i class="fas fa-question-circle" title = "'.$questions[$key]['tooltip'] .'"></i>' : '' ?><br>
            
        <?php endforeach; ?>  
        
		<div class = "row button-row">
			<div class = "col-md-10"></div>
			<div class = "col-md-1">
				<?= Html::resetButton('Reset', ['class' => 'btn btn-primary submit-button']); ?>
			</div>
            <div class = "col-md-1">
            	
		    	<?= Html::submitButton('Next', ['class' => 'btn btn-primary submit-button']); ?>
			</div>
		</div>

		<?php ActiveForm::end(); ?>
	</div>

</div>

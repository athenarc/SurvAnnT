<?php

/* @var $this yii\web\View */
use yii\bootstrap4\Progress;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'My Yii Application';
?>
<div class="site-index">

	<div class="outside-div">
		
		<?php $form = ActiveForm::begin(['options' => ['class' => 'rate-form']]); ?>

		<div class = "rate-box" >
			<div class = "resource-row marg-left-right">
				<div class = "text-left">
				<?php 	if ( $resource['type'] == 'image'): ?>

					<img src="data:image/png;base64,<?= base64_encode($resource['image']) ?>"/>

				<?php 	elseif ( $resource['type'] == 'questionaire' ): ?>

					<h3><?= $resource['title'] ?></h3>

				<?php 	else: ?>

					<h3><?= $resource['title'] ?></h3>
					<?= $resource['abstract'] ?>

				<?php 	endif; ?>
				</div>
			</div>

			<div class = "progress-bar-widget row">
	        	<div class = "col-md-4">
	        		<h4> Annotation Goal </h4>
					<?= Progress::widget([
		            'percent' => substr( ( $user_feedback_provided / $minimum_resources_goal ) * 100, 0, 4 ),
		            'barOptions' => ['class' => 'progress-bar-success'],
		            'options' => ['class' => 'active progress-striped']
		            ]), substr( ( $user_feedback_provided / $minimum_resources_goal ) * 100, 0, 4 ), "% (", $minimum_resources_goal - $user_feedback_provided, " at least more Resources need to be annotated) " ?>
		        </div>
		        <div class = "col-md-4">
	        		<h4> Next Badge Goal </h4>
					<?= Progress::widget([
		            'percent' => substr( ( $user_feedback_provided / $minimum_resources_goal ) * 100, 0, 4 ),
		            'barOptions' => ['class' => 'progress-bar-success'],
		            'options' => ['class' => 'active progress-striped']
		            ]), substr( ( $user_feedback_provided / $minimum_resources_goal ) * 100, 0, 4 ), "% ( Annotate ", $minimum_resources_goal - $user_feedback_provided, " more resources for the next badge! ) " ?>
		        </div>
        	</div>

			<div class = "question-box marg-left-right">
				<?php foreach ($rates as $key => $rate): ?>
					<div class = "row question">
	            		<!-- <span id = 'Question' >  -->
	            			<div class = "col-md-12">
		            			<?= $questions[$key]['question'] ?> 
		            			<?php isset( $questions[$key]['tooltip'] ) ? '<i class="fas fa-question-circle" title = "'.$questions[$key]['tooltip'] .'"></i>' : '' ?>
		            		</div>
	            		<!-- </span> -->
		           	</div>
		           	<div class = "row question-values">
		           		<div class = "col-md-12">
			                <?php if( $questions[$key]['answertype'] == 'textInput' ): ?>
			                	<?= $form->field($rate, "[$key]answer")->textInput(['class' => 'question-radiolist', 'id' => 'rate-answer-'.$key])->label(false) ?><br>
			                <?php else: ?>
			                	<?= $form->field($rate, "[$key]answer")->radioList($questions[$key]['answervalues'], [ 'class' => 'question-radiolist', 'id' => 'rate-answer-'.$key])->label(false) ?><br>
			                <?php endif; ?>
			            </div>
		           	</div>
		        <?php endforeach; ?>  
	        </div>

			<div class = "row button-row">
				<div class = "col-md-10"></div>
				<div class = "col-md-1">
					<?= Html::resetButton('Reset', ['class' => 'btn btn-primary submit-button']); ?>
				</div>
	            <div class = "col-md-1">
	            	
			    	<?= Html::submitButton('Next', ['class' => 'btn btn-primary submit-button']); ?>
				</div>
			</div>
		</div>
		<?php ActiveForm::end(); ?>
	</div>

</div>

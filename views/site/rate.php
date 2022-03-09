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
				<?php 	if ( $resource['type'] == 'image'): ?>
					<div class = "text-center">
						<img src="data:image/png;base64,<?= base64_encode($resource['image']) ?>"/>
					</div>
				<?php 	elseif ( $resource['type'] == 'questionaire' ): ?>
					<div class = "text-left">
						<h3><?= $resource['title'] ?></h3>	
					</div>

				<?php 	else: ?>
					<div class = "text-left">
						<h3><?= $resource['title'] ?></h3>
						<?= $resource['abstract'] ?>
					</div>
				<?php 	endif; ?>
			</div>
			
			<div class = "progress-bar-widget row">
				<?php if(( $minimum_resources_goal > 0 )): ?>
		        	<div class = "col-md-4">
		        		<h4> Annotation Goal </h4>
		        		
						<?= Progress::widget([
			            'percent' => substr( ( $user_feedback_provided / $minimum_resources_goal ) * 100, 0, 4 ),
			            'barOptions' => ['class' => 'progress-bar-success'],
			            'options' => ['class' => 'active progress-striped']
			            ]), substr( ( $user_feedback_provided / $minimum_resources_goal ) * 100, 0, 4 ), "% (", $minimum_resources_goal - $user_feedback_provided, " at least more Resources need to be annotated) " ?>
			       
			        </div>
		    	<?php endif; ?>
		        <div class = "col-md-4">
	        		<h4> Next Badge Goal </h4>
					<?= Progress::widget([
		            'percent' => substr( ( $user_feedback_provided /  ( $user_feedback_provided + $next_badge_goal ) ) * 100, 0, 4 ),
		            'barOptions' => ['class' => 'progress-bar-success'],
		            'options' => ['class' => 'active progress-striped']
		            ]), substr( ( $user_feedback_provided /  ( $user_feedback_provided + $next_badge_goal ) ) * 100, 0, 4 ), "% ( Annotate ", $next_badge_goal, " more resources for the next badge! ) " ?>
		        </div>
        	</div>

			<div class = "question-box marg-left-right">
				<?php foreach ($rates as $key => $rate): ?>
					<div class = "row question">
	            		<!-- <span id = 'Question' >  -->
	            			<div class = "col-md-12">
		            			<?= $questions[$key]['question'] ?> 
		            			<?= isset( $questions[$key]['tooltip'] ) ? '<a class="fas fa-question-circle link-icon" title = "'.$questions[$key]['tooltip'] .'"></a>' : '' ?>
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

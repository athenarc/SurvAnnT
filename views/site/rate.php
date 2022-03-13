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
				<h5 class="text-center"> <i>Campaign:  <?= $survey->name ?></i></h5>
				<?php 	if ( $resource['type'] == 'image'): ?>
					<div class = "text-center">
						<img src="data:image/png;base64,<?= base64_encode($resource['image']) ?>"/>
					</div>
				<?php 	elseif ( $resource['type'] == 'questionaire' ): ?>
					<div class = "text-center">
						<h2><?= $resource['title'] ?></h2>	
					</div>

				<?php 	elseif ( $resource['type'] == 'article' ): ?>
					<div class = "text-left">
						<h2><?= $resource['title'] ?></h2>
						<?= $resource['abstract'] ?>
					</div>
				<?php 	else: ?>
					<div class = "text-left">
						<h2><?= $resource['title'] ?></h2>
						<?= $resource['text'] ?>
					</div>
				<?php 	endif; ?>
				<br>
				<h3>Please provide us with your annotations!</h3>
				<br>
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
			                	<?= $form->field($rate, "[$key]answer")->textInput(['id' => 'rate-answer-'.$key])->label(false) ?><br>
			                <?php else: ?>
			                	<?= $form->field($rate, "[$key]answer")->radioList($questions[$key]['answervalues'], [ 'class' => 'question-radiolist', 'id' => 'rate-answer-'.$key])->label(false) ?><br>
			                <?php endif; ?>
			            </div>
		           	</div>
		        <?php endforeach; ?>  
	        

		        <div class = "progress-bar-widget row">
		        	<div class="row">
						<?php if(( $minimum_resources_goal > 0 )): ?>
				        	<div class = "col-md-4">
				        		<h5 class = "goals-title"> Annotation Goal </h5>
				        		
								<?= Progress::widget([
					            'percent' => substr( ( $user_feedback_provided / $minimum_resources_goal ) * 100, 0, 4 ),
					            'barOptions' => ['class' => 'progress-bar-success'],
					            'options' => ['class' => 'active progress-striped']
					            ]), substr( ( $user_feedback_provided / $minimum_resources_goal ) * 100, 0, 4 ), "% (", $minimum_resources_goal - $user_feedback_provided, " more Resources need to be annotated) " ?>
					       
					        </div>
				    	<?php endif; ?>
				    	<?php if(( $next_badge_goal > 0 )): ?>
					        <div class = "col-md-4">
				        		<h5 class = "goals-title"> Next Badge Goal </h5>

								<?= Progress::widget([
					            'percent' => substr( ( $user_feedback_provided /  ( $user_feedback_provided + $next_badge_goal ) ) * 100, 0, 4 ),
					            'barOptions' => ['class' => 'progress-bar-success'],
					            'options' => ['class' => 'active progress-striped']
					            ]), substr( ( $user_feedback_provided /  ( $user_feedback_provided + $next_badge_goal ) ) * 100, 0, 4 ), "% (", $next_badge_goal, " more resources for the next badge! ) " ?>
					        </div>
				        <?php endif; ?>
			        	<div class = "col-md-4">
			        		<h5 class = "goals-title"> Acquired Badges (This Campaign)</h5>
			        		<?php foreach($acquired_badges as $badge): ?>
								<img class = "acquired-badges" src="data:image/png;base64,<?= base64_encode($badge) ?>"/>
							<?php endforeach; ?>
				        </div>
		        	</div>
				</div>
				<div class = "row button-row">
					<div class = "col-md-10"></div>
					<div class = "col-md-1">
						<?= Html::resetButton('Reset', ['class' => 'btn btn-primary submit-button']); ?>
					</div>
		            <div class = "col-md-1">
		            	
				    	<?= Html::submitButton(($resource['type'] != 'questionaire') ? 'Next' : 'Submit', ['class' => 'btn btn-primary submit-button']); ?>
					</div>
				</div>
			</div>
		</div>
		<?php ActiveForm::end(); ?>
	</div>

</div>

<?php

/* @var $this yii\web\View */
use yii\bootstrap4\Progress;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\web\View;
if($survey->time){
	$this->registerJsFile('@web/js/time.js', ['position' => View::POS_END, 'depends' => [\yii\web\JqueryAsset::className()]]);
}


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
				<h4>Please provide us with your feedback!</h4>
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
		           	<?= $form->field($rate, "[$key]time") -> hiddenInput(['id' => 'time', 'class' => 'time'])->label(false) ?>
		        <?php endforeach; ?>  
	        

		        <div class = "progress-bar-widget">
		        	<div class="row">
						<?php if( $minimum_resources_goal > 0  ): ?>
				        	<div class = "col-md-3">
				        		<h5 class = "goals-title"> <?= $progresses['annotation_goal']['title'] ?> </h5>
				        		
								<?= Progress::widget(
									[
					            		'percent' => $progresses['annotation_goal']['progress'],
					            		'barOptions' => ['class' => 'progress-bar-success'],
					            		'options' => ['class' => 'active progress-striped']
					            ]), $progresses['annotation_goal']['progress'], $progresses['annotation_goal']['message']  ?>
					       
					        </div>
					    <?php endif; ?>
				    	<?php if ( $user_feedback_provided >= $minimum_resources_goal && $maximum_resources_goal > $minimum_resources_goal ): ?>
				    		<div class = "col-md-3">
				        		<h5 class = "goals-title"> <?= $progresses['additional_annotation_goal']['title'] ?> </h5>
				        		
								<?= Progress::widget(
									[
					            		'percent' => $progresses['additional_annotation_goal']['progress'],
					            		'barOptions' => ['class' => 'progress-bar-success'],
					            		'options' => ['class' => 'active progress-striped']
					            	]), $progresses['additional_annotation_goal']['progress'].$progresses['additional_annotation_goal']['message']  ?>
					       
					        </div>
					    <?php endif; ?>
				    	<?php if( $next_badge_goal > 0 ): ?>
					        <div class = "col-md-3">
				        		<h5 class = "goals-title"> <?= $progresses['next_badge_goal']['title'] ?> </h5>

								<?= Progress::widget(
									[
					            		'percent' => $progresses['next_badge_goal']['progress'],
					            		'barOptions' => ['class' => 'progress-bar-success'],
					            		'options' => ['class' => 'active progress-striped']
					            	]), $progresses['next_badge_goal']['progress'], $progresses['next_badge_goal']['message']
					            	 ?>
					        </div>
				        <?php endif; ?>
				        <?php if( sizeof( $acquired_badges ) > 0): ?>
				        	<div class = "col-md-3">
				        		<h5 class = "goals-title"> Acquired Badges (This Campaign)</h5>
				        		<?php foreach($acquired_badges as $badge): ?>
									<img class = "acquired-badges" src="data:image/png;base64,<?= base64_encode($badge) ?>"/>
								<?php endforeach; ?>
					        </div>
				        <?php endif; ?>

				        <?php if($survey->time): ?>

					        <label id="seconds_active" style = "display: none;">0</label>
			                <label id="seconds_inactive" style = "display: none;">0</label>
			                <span class="notice-message"><i>Response times are recorded</i>&nbsp;</span><i class = "fas fa-info-circle notice-tooltip" title = "The response time is acquired in order to discover possible correlations with a text's reading time as measured by various metrics."></i>
		            	<?php endif; ?>
		        	</div>
				</div>
				<div class = "row button-row">
					<div class = "col-md-10"></div>
					<div class = "col-md-1">
						<?= Html::resetButton('Reset', ['class' => 'btn btn-primary submit-button']); ?>
					</div>
		            <div class = "col-md-1">
		            	
				    	<?= Html::submitButton(($resource['type'] != 'questionaire') ? 'Next' : 'Submit', ['class' => 'btn btn-primary submit-button', 'id' => 'save']); ?>
					</div>
				</div>
			</div>
		</div>
		<?php ActiveForm::end(); ?>
	</div>

</div>

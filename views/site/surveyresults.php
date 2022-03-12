<?php

/* @var $this yii\web\View */
use yii\bootstrap4\Progress;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'My Yii Application';
?>
<div class="site-index">

	<div class="outside-div about-div">
		<div class ="about-text">
            <div class = "row about-row">
                <h2>Campaign "<?= $survey->name ?>" Resutls</h2>
            </div>
            <br>
			<table class="table table-striped table-bordered participants-table">  
		        <tr class = "dataset-table-header-row">
		            <th class = "dataset-header-column">
		               	Resource Id
		            </th>
		            <th class = "dataset-header-column">
		                Resource
		            </th>
		            <th class = "dataset-header-column">
		                # of Annotations
		            </th>
		            <th class = "dataset-header-column">
		                Users Evaluated
		            </th>
		        </tr>
			<?php foreach ($resources as $resource): ?>
				<tr>
					<td> <?= $resource->id ?></td>
					<?php if($resource->type == 'image'): ?>    
						<td> 
							<img src="data:image/png;base64,<?=base64_encode($resource->image)?>" style = "max-height: 50px; max-width: 50px;"/>
						</td>
					<?php else: ?>
						<td> 
							<?= $resource->title ?>
						</td>
					<?php endif; ?>
					<td>
						<?= $resource->getRates()->select(['count(*)'])->groupBy('resourceid')->all()['count(*)'] ?>
					</td>
					<td>
						<?= implode("<br>", $rates['resources'][$resource->id]) ?>
					</td>
				</tr>
			<?php endforeach; ?> 
			</table>

			<br>
			<table class="table table-striped table-bordered participants-table">  
		        <tr class = "dataset-table-header-row">
		            <th class = "dataset-header-column">
		               	Question Id
		            </th>
		            <th class = "dataset-header-column">
		                Question
		            </th>
		            <th class = "dataset-header-column">
		                # of Responses
		            </th>
		            <th class = "dataset-header-column">
		                Users Evaluated
		            </th>
		        </tr>
			<?php foreach ($questions as $question): ?>
				<tr>
					<td> <?= $question->id ?></td>
					<td> <?= $question->question ?></td>
					<td>
						<?= sizeof($question->getRates()->groupBy('questionid', 'userid')->all()) ?>
					</td>
					<td>
						<?= implode("<br>", $rates['questions'][$question->id]) ?>
					</td>
				</tr>
			<?php endforeach; ?> 
			</table>
		</div>
	</div>

</div>

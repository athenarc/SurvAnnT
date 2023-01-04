<?php
 
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use app\models\Resources;
use yii\bootstrap4\LinkPager;

date_default_timezone_set("Europe/Athens"); 
$date = date('Y-m-d hh:mm', time());
use yii\web\View;

$this->registerJsFile('@web/js/resourcecreatenew.js', ['position' => View::POS_END, 'depends' => [\yii\web\JqueryAsset::className()]]);
?>

<div class="resourcecreateformbefore">
	<div class = "outside-div">
        <div class = "row header-row dataset-header-row">
            <?php include 'tabs.php'; ?>
        </div>
        <div class = "row button-row ">
            <div class = "col-md-10 d-flex align-items-center">
                <i class="fa fa-info-circle helper-message" ></i>&nbsp;
                Create the collection of Resources to be used for Survey/Annotation purposes (users will answer
                questions related to each of these resources)
            </div>
            <div class = "col-md-2  text-right">
                <?= Html::a( 'Previous', $tabs['General Settings']['link'].$survey->id, ['class' => 'btn btn-primary', 'name' => 'test-name']); ?>
                <?= Html::a( 'Next', $tabs['Questions']['link'].$survey->id, ['class' => 'btn btn-primary', 'name' => 'test-name']); ?>
            </div>
            
        </div>
        <div class="survey-form-box ">

        	<div class = "header-label">
                Collection Metadata
            </div>
            <?php $form = ActiveForm::begin(['options' => ['class' => 'resource-before-form', 'enctype' => 'multipart/form-data']]); ?>
            	<?php if( !$SurveyCollection->isNewRecord ): ?>
	            	<div class="row">  
	            		<div class="col-md-12 text-right">
	            			<?= Html::submitButton('Update Collection', ['class' => 'btn btn-primary']) ?>
	            			<?= Html::a( 'Delete Collection', ['resources/collection-delete', 'surveyid' => $survey->id], ['class' => 'btn btn-primary']) ?>
	            		</div>
	            	</div>
            	<?php endif; ?>
                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($SurveyCollection, 'name')->textInput([])->label() ?>
                    </div>
                    
                    <div class="col-md-6">
                        <?= $form->field($SurveyCollection, 'allowusers')->dropDownList([ 1 => 'Yes', 0 => 'No']) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <?= $form->field($SurveyCollection, 'about')->textArea()->label() ?>
                    </div>
                </div>
           		<br>

        	
        	<!-- SURVEY RESOURCES TABLE -->
        	<?php if( !$SurveyCollection->isNewRecord ): ?>
        		<div class = "row">
                <div class = "col-md-12">
                    <div class = "header-label">
                        Add Resources to Collection
                    </div>
                </div>
	            </div>
	            <div class = "row">
	                <div class="col-md-12 text-right">
	                	<?php if( !$SurveyCollection->isNewRecord && $survey->getCollection()->one()->getResources()->all()): ?>
	                		<?= Html::a( 'Delete All Resources',['resources/resources-delete-all', 'surveyid' => $survey->id],  ['class' => 'btn btn-primary resources-delete-all']) ?>
	                	<?php endif; ?>
	                	<?= Html::button( 'Import Resources (articles only)', ['id' => 'import-resources-button', 'disabled' => sizeof($SurveyResources) > 0 && ( $SurveyResources[0]['type'] != 'article' ) ? true : false, 'class' => 'btn btn-primary', 'data-toggle' => "modal", 'data-target' => "#import-resources"]) ?>
	                   	<?= Html::button( 'Reuse Existing Resources', ['id' => 'db-resources-button', 'disabled' => sizeof($SurveyResources) > 0 && $SurveyResources[0]['type'] == 'questionnaire' ? true : false, 'class' => 'btn btn-primary', 'data-toggle' => "modal", 'data-target' => "#db-resources"]) ?>
	                    <?= Html::button( 'Create Resources', ['id' => 'user-resources-button', 'disabled' => sizeof($SurveyResources) > 0 && $SurveyResources[0]['type'] == 'questionnaire' ? true : false, 'class' => 'btn btn-primary', 'data-toggle' => "modal", 'data-target' => "#user-resources"]) ?>
	                     <input type="hidden" id="surveyId" value="<?=$survey->id?>" name="">
	                </div>                
	            </div>
	            <br>
	            <div class="row">
                    <div class="col-md-12 d-flex justify-content-center">
                    	<?= LinkPager::widget(['pagination' => $paginationResources]) ?>
                	</div>
                </div>

	            <br>
	            <div class="row">
	            	<div class="col-md-12">
			        	<table class="table table-striped table-bordered participants-table"> 
			        		<tr class = "dataset-table-header-row">
			        			<tr class = "dataset-table-header-row">
			        			<th class = "dataset-header-column">Title</th>
			        			<th class = "dataset-header-column" style = "display: <?=$type == 'article' ? 'table-cell' : 'none'?>;">Abstract</th>
			        			<th class = "dataset-header-column" style = "display: <?=$type == 'text' ? 'table-cell' : 'none'?>;">Text</th>
			        			<th class = "dataset-header-column" style = "display: <?=$type == 'image' ? 'table-cell' : 'none'?>;">Image</th>
			        			<th class = "dataset-header-column" style = "display: <?=$type == 'article' ? 'table-cell' : 'none'?>;">PMC</th>
								<th class = "dataset-header-column" style = "display: <?=$type == 'article' ? 'table-cell' : 'none'?>;">DOI</th>
								<th class = "dataset-header-column" style = "display: <?=$type == 'article' ? 'table-cell' : 'none'?>;">PUBMED</th>
								<th class = "dataset-header-column" style = "display: <?=$type == 'article' ? 'table-cell' : 'none'?>;">Authors</th>
								<th class = "dataset-header-column" style = "display: <?=$type == 'article' ? 'table-cell' : 'none'?>;">Journal</th>
								<th class = "dataset-header-column" style = "display: <?=$type == 'article' ? 'table-cell' : 'none'?>;">Year</th>
								<th class = "dataset-header-column">Public</th>
								<th class = "dataset-header-column">Actions</th>
			        		</tr>
			        		<?php foreach ($SurveyResources as $resource): ?>
			        			<tr class="resource-table-row">
			        				<td class ="text-overflow-ellipsis"> 
			        					<span class="edit-resource-<?=$resource->id?>" style ="display: none;">
											<input type="text" value="<?=$resource->title?>" name="resource-title-<?=$resource['id']?>" class = "form-control">
										</span>
		                                <span class="edit-resource-<?=$resource->id?> resource-title-<?=$resource->id?>" title = "<?=$resource->title?>">
		                                	<?= $resource->title ?>
		                                </span>
			        				</td>
			        				<td class ="text-overflow-ellipsis" style = "display: <?=$type == 'article' ? 'table-cell' : 'none'?>;"> 
			        					<span class="edit-resource-<?=$resource->id?>" style ="display: none;">
											<textarea class="form-control" name="resource-abstract-<?=$resource['id']?>" rows="4" cols="50"><?= $resource->abstract?></textarea>
										</span>
										<span class="edit-resource-<?=$resource->id?> resource-abstract-<?=$resource->id?>" title = "<?=$resource->abstract?>">
			        						<?= $resource->abstract ?> 
			        					</span>
			        				</td>
			        				<td class ="text-overflow-ellipsis" style = "display: <?=$type == 'text' ? 'table-cell' : 'none'?>;"> 
			        					<span class="edit-resource-<?=$resource->id?>" style ="display: none;">
											<textarea class="form-control" name="resource-text-<?=$resource['id']?>" rows="4" cols="50"><?=$resource['text']?></textarea>
										</span>
										<span class="edit-resource-<?=$resource->id?> resource-text-<?=$resource->id?>" title = "<?=$resource->text?>">
			        						<?= $resource->text ?> 
			        					</span>
			        				</td>
			        				<td style = "display: <?=$type == 'image' ? 'table-cell' : 'none'?>;"> 
			        					<span class="edit-resource-<?=$resource->id?>" style ="display: none;">
			        						<img src="data:image/png;base64,<?= base64_encode($resource->image) ?>" style = "max-height: 35px; max-width: 35px;"/>
			        					</span>
			        					<span class="edit-resource-<?=$resource->id?>">
			        						<img src="data:image/png;base64,<?= base64_encode($resource->image) ?>" style = "max-height: 35px; max-width: 35px;"/>
			        					</span>
			        				</td>
			        				<td class ="text-overflow-ellipsis" style = "display: <?=$type == 'article' ? 'table-cell' : 'none'?>;"> 
			        					<span class="edit-resource-<?=$resource->id?>" style ="display: none;">
			        						<input type="text" value="<?=$resource['pmc']?>" name="resource-pmc-<?=$resource['id']?>" class = "form-control">
			        					</span>
			        					<span class="edit-resource-<?=$resource->id?> resource-pmc-<?=$resource->id?>">
			        						<?= $resource['pmc'] ?> 
			        					</span>
			        				</td>
			        				<td class ="text-overflow-ellipsis" style = "display: <?=$type == 'article' ? 'table-cell' : 'none'?>;"> 
			        					<span class="edit-resource-<?=$resource->id?>" style ="display: none;">
			        						<input type="text" value="<?=$resource['doi']?>" name="resource-doi-<?=$resource['id']?>" class = "form-control">
			        					</span>
			        					<span class="edit-resource-<?=$resource->id?> resource-doi-<?=$resource->id?>">
			        						<?= $resource['doi'] ?> 
			        					</span>
			        				</td>
			        				<td class ="text-overflow-ellipsis" style = "display: <?=$type == 'article' ? 'table-cell' : 'none'?>;"> 
			        					<span class="edit-resource-<?=$resource->id?>" style ="display: none;">
			        						<input type="text" value="<?=$resource['pubmed_id']?>" name="resource-pubmed_id-<?=$resource['id']?>" class = "form-control">
			        					</span>
			        					<span class="edit-resource-<?=$resource->id?> resource-pubmed_id-<?=$resource->id?>">
			        						<?= $resource['pubmed_id'] ?> 
			        					</span>
			        				</td>
			        				<td class ="text-overflow-ellipsis" style = "display: <?=$type == 'article' ? 'table-cell' : 'none'?>;"> 
			        					<span class="edit-resource-<?=$resource->id?>" style ="display: none;">
			        						<input type="text" value="<?=$resource['authors']?>" name="resource-authors-<?=$resource['id']?>" class = "form-control">
			        					</span>
			        					<span class="edit-resource-<?=$resource->id?> resource-authors-<?=$resource->id?>">
			        						<?= $resource['authors'] ?> 
			        					</span>
			        				</td>
			        				<td class ="text-overflow-ellipsis" style = "display: <?=$type == 'article' ? 'table-cell' : 'none'?>;"> 
			        					<span class="edit-resource-<?=$resource->id?>" style ="display: none;">
			        						<input type="text" value="<?=$resource['journal']?>" name="resource-journal-<?=$resource['id']?>" class = "form-control">
			        					</span>
			        					<span class="edit-resource-<?=$resource->id?>  resource-journal-<?=$resource->id?>">
			        						<?= $resource['journal'] ?> 
			        					</span>
			        				</td>
			        				<td class ="text-overflow-ellipsis" style = "display: <?=$type == 'article' ? 'table-cell' : 'none'?>;"> 
			        					<span class="edit-resource-<?=$resource->id?>" style ="display: none;">
			        						<input type="text" value="<?=$resource['year']?>" name="resource-year-<?=$resource['id']?>" class = "form-control">
			        					</span>
			        					<span class="edit-resource-<?=$resource->id?> resource-year-<?=$resource->id?>">
			        						<?= $resource['year'] ?> 
			        					</span>
			        				</td>
			        				<td class ="text-overflow-ellipsis" > 
			        					<span class="edit-resource-<?=$resource->id?>" style ="display: none;">
			        						<select class="form-control" selected="0" value = "0" name="resource-allowusers-<?=$resource['id']?>">
												<option <?= $resource->allowusers ? 'selected' : '' ?> value="1">Yes</option>
												<option <?= ! $resource->allowusers ? 'selected' : '' ?> value="0">No</option>
											</select>
			        					</span>
			        					<span class="edit-resource-<?=$resource->id?> resource-public-<?=$resource->id?>">
			        						<?= $resource['allowusers'] == 1 ? 'Yes' : 'No' ?> 
			        					</span>
			        				</td>
			        				<td>
			        					<a id="resources-actions-<?=$resource->id?>" class="fas fa-pencil edit-resource link-icon"></a>
		                                <a id="resources-actions-<?=$resource->id?>" class="fas fa-trash-alt delete-resource link-icon"></a>
			        				</td>
			        			</tr>
			        		<?php endforeach; ?>
			        	</table>
			        </div>
		        </div>
	        	<br>

        	<?php else: ?>
            	<?= Html::submitButton('Next step (1/2)', ['class' => 'btn btn-primary submit-button']) ?>
            	<br>
            <?php endif; ?>
            
        	<?php ActiveForm::end(); ?>
        </div>
    </div>
</div>


<div class="modal fade bd-example-modal-lg" id="db-resources" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div  class="modal-dialog modal-dialog-db-resources modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Resources Form</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body resources-modal-body " >
            	<div class="table-responsive p-3 ">
            		<?php Pjax::begin(['id' => 'resources', 'enablePushState' => false]) ?>
				    <?= GridView::widget([
				        'dataProvider' => $dbResources,
				        'filterModel' => $resourcesSearch,
				        'pager' => [ 
	                		'class' => '\yii\bootstrap4\LinkPager',
	                		'options' => ['class' => 'col-md-8']
	                	],
				        'columns' => 
				        	[
				        		[
								    'attribute' => 'id',
								    'value' => 'id',
								    'visible' => false,
								],
				        		[
								    'attribute' => 'type',
								    'value' => 'type',
								    'filter' => Html::activeDropDownList($resourcesSearch, 'type', $resourceTypeOptions, ['value' => $type, 'class'=>'form-control']),
								],
				        		[
					                'label' => 'Image',
					                'format' => 'raw',
					                'attribute'=>'image',
					                'visible' =>  in_array('image', $columns),
					                'value' => function($model) {
					                    return '<img id = "image-preview-'.$model->id.'" class="badge-image-preview" src="data:image/png;base64,'.base64_encode($model->image ).'"/>';
					                },
				        		],
				        		
				        		[
				        			'contentOptions' => ['style' => 'text-overflow: ellipsis; white-space: nowrap; max-width: 3vw; overflow: hidden;'],
					                'label' => 'Title',
					                'format' => 'raw',
					                'attribute'=>'title',
					                'visible' =>  in_array('title', $columns),
					                'value' => function($model) {
					                    return '<span title = "'.$model->title.'">'.$model->title.'</span>';
					                },
				        		],

				        		[
				        			'contentOptions' => ['style' => 'text-overflow: ellipsis; white-space: nowrap; max-width: 3vw; overflow: hidden;'],
					                'label' => 'Abstract',
					                'format' => 'raw',
					                'attribute'=>'abstract',
					                'visible' =>  in_array('abstract', $columns),
					                'value' => function($model) {
					                    return '<span title = "'.$model->abstract.'">'.$model->abstract.'</span>';
					                },
				        		],

				        		[
				        			'contentOptions' => ['style' => 'text-overflow: ellipsis; white-space: nowrap; max-width: 3vw; overflow: hidden;'],
					                'label' => 'Text',
					                'format' => 'raw',
					                'attribute'=>'text',
					                'visible' =>  in_array('text', $columns),
					                'value' => function($model) {
					                    return '<span title = "'.$model->text.'">'.$model->text.'</span>';
					                },
				        		],

				        		[
				        			'contentOptions' => ['style' => 'text-overflow: ellipsis; white-space: nowrap; max-width: 3vw; overflow: hidden;'],
					                'label' => 'PMC',
					                'format' => 'raw',
					                'attribute'=>'pmc',
					                'visible' =>  in_array('pmc', $columns),
					                'value' => function($model) {
					                    return '<span title = "'.$model->pmc.'">'.$model->pmc.'</span>';
					                },
				        		],

				        		[
				        			'contentOptions' => ['style' => 'text-overflow: ellipsis; white-space: nowrap; max-width: 3vw; overflow: hidden;'],
					                'label' => 'DOI',
					                'format' => 'raw',
					                'attribute'=>'doi',
					                'visible' => in_array('doi', $columns),
					                'value' => function($model) {
					                    return '<span title = "'.$model->doi.'">'.$model->doi.'</span>';
					                },
				        		],
				        		[
				        			'contentOptions' => ['style' => 'text-overflow: ellipsis; white-space: nowrap; max-width: 3vw; overflow: hidden;'],
					                'label' => 'Pubmed',
					                'format' => 'raw',
					                'attribute'=>'pubmed_id',
					                'visible' =>  in_array('pubmed_id', $columns),
					                'value' => function($model) {
					                    return '<span title = "'.$model->pubmed_id.'">'.$model->pubmed_id.'</span>';
					                },
				        		],
				        		[
				        			'contentOptions' => ['style' => 'text-overflow: ellipsis; white-space: nowrap; max-width: 3vw; overflow: hidden;'],
					                'label' => 'Authors',
					                'format' => 'raw',
					                'attribute'=>'authors',
					                'visible' =>  in_array('authors', $columns),
					                'value' => function($model) {
					                    return '<span title = "'.$model->authors.'">'.$model->authors.'</span>';
					                },
				        		],
				        		[
				        			'contentOptions' => ['style' => 'text-overflow: ellipsis; white-space: nowrap; max-width: 3vw; overflow: hidden;'],
					                'label' => 'Journal',
					                'format' => 'raw',
					                'attribute'=>'journal',
					                'visible' =>  in_array('authors', $columns),
					                'value' => function($model) {
					                    return '<span title = "'.$model->journal.'">'.$model->journal.'</span>';
					                },
				        		],
				        		[
					                'label' => 'Year',
					                'format' => 'raw',
					                'attribute'=>'year',
					                'visible' =>  in_array('year', $columns),

				        		],
				        		[
					                'label' => 'Use',
					                'format' => 'raw',
					                'value' => function($model, $SurveyResources) {
					                    return Html::checkbox('resource-use-'.$model->id, false, ['class' => 'resource-use', 'id'=>'resource-use-'.$model->id]);
					                },


				        		],
				        		

				        	],
				    ]); ?>
				<?php Pjax::end() ?>
				</div>

            </div>
            <?php $form = ActiveForm::begin(['action' => ['resources/resources-use'], 'options' => ['id'=>'db-resources-form', 'data-pjax' => false ]]); ?>
            <div class="modal-footer">

            	
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>    
                <?= Html::submitButton('Submit', ['class' => 'btn btn-primary submit-button']) ?>
                
            </div>
            <input type="hidden" id="surveyId" name="surveyId" value="<?=$survey->id?>">  
            <?php ActiveForm::end(); ?>
                     
        </div>
    </div>
</div>

<div class="modal fade bd-example-modal-lg" id="user-resources" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div  class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        	<?php $form = ActiveForm::begin(['options' => ['id' => 'resources-user-form', 'class' => 'resource-before-form', 'enctype' => 'multipart/form-data']]); ?>
	            <div class="modal-header">
	                <h5 class="modal-title" id="exampleModalLabel">Upload Resources Form</h5>
	                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                    <span aria-hidden="true">&times;</span>
	                </button>
	            </div>
	            <div class="modal-body resources-modal-body p-4 resources-user-form-modal">
	            	<table class="table table-striped table-bordered participants-table">
	            		<tr class = "dataset-table-header-row">
	            			<th class = "dataset-header-column">
	            				Resource Type
	            			</th>
	            		</tr>
	            		<tr>
	            			<td>
	            				<?= Html::activeDropDownList($resourcesSearch, 'type', $resourceTypeOptions, ['id'=>'user-form-resource-type', 'value' => $type, 'class'=>'form-control']) ?>
	            			</td>
	            		</tr>
	            	</table>
	            	<br>
	            	<?php foreach ($resources as $key => $resource): ?>
	            		<table class="table table-striped table-bordered participants-table resource-form-table-<?=$key?> dataset-tools">
		            		<tr class = "dataset-table-header-row">
			        			<th colspan = "2" class = "dataset-header-column user-form-field user-article user-text user-questionnaire user-image ">Title</th>
			        			<th class = "dataset-header-column user-form-field user-image" style = "display: <?=$type == 'image' ? 'table-cell' : 'none'?>;">Image</th>
			        			<th class = "dataset-header-column">Public</th>		
			        			<th class = "dataset-header-column" style = "display: none;" >Type</th>			
			        			<th class = "dataset-header-column" style = "display: none;" > Survey ID</th>		
		            		</tr>
		            		<tr>
		            			<td colspan = "2" class="user-form-field user-image  user-article user-text user-questionnaire">
		            				<?= $form->field($resource, "[$key]title")->textInput()->label(false)?>
		            			</td>	
		            			<td class="user-form-field user-image user-image" style = "display: <?=$type == 'image' ? 'table-cell' : 'none'?>;">
		            				<?= $form->field($resource, "[$key]image")->fileInput(['multiple' => false])->label(false) ?>
		            			</td>
		            			
		            			<td>
		            				<?= $form->field($resource, "[$key]allowusers")->dropDownList([1 => 'Yes', 0 => 'No'])->label(false)?>
		            			</td>
		            			<td style = "display: none;" >
		            				<?= $form->field($resource, "[$key]type")->dropDownList($resourceTypeOptions, ['value' => $type, 'id' => 'resource-type-'.$key])->label(false)?>
		            			</td>
		            			<td style = "display: none;" >
		            				<input type="hidden" id="surveyId" value="<?=$survey->id?>" name="">
		            			</td>
		            		</tr>
		            		<tr>
		            			<th colspan = "3" class = "dataset-header-column user-form-field user-article" style = "display: <?=$type == 'article' ? 'table-cell' : 'none'?>;">Abstract</th>
			        			<th colspan = "3" class = "dataset-header-column user-form-field user-text" style = "display: <?=$type == 'text' ? 'table-cell' : 'none'?>;">Text</th>
		            		</tr>
		            		<tr>
		            			<td colspan = "3" class="user-form-field user-article" style = "display: <?=$type == 'article' ? 'table-cell' : 'none'?>;">
		            				<?= $form->field($resource, "[$key]abstract")->textArea()->label(false)?>
		            			</td>
		            			<td colspan = "3" class="user-form-field user-text" style = "display: <?=$type == 'text' ? 'table-cell' : 'none'?>;">
		            				<?= $form->field($resource, "[$key]text")->textArea()->label(false)?>
		            			</td>
		            		</tr>
		            		<tr>
		            			<th class = "dataset-header-column user-form-field user-article" style = "display: <?=$type == 'article' ? 'table-cell' : 'none'?>;">PMC</th>
								<th class = "dataset-header-column user-form-field user-article" style = "display: <?=$type == 'article' ? 'table-cell' : 'none'?>;">DOI</th>
								<th class = "dataset-header-column user-form-field user-article" style = "display: <?=$type == 'article' ? 'table-cell' : 'none'?>;">PUBMED</th>
		            		</tr>
		            		<tr>
		            			<td class="user-form-field user-article" style = "display: <?=$type == 'article' ? 'table-cell' : 'none'?>;">
		            				<?= $form->field($resource, "[$key]pmc")->textInput()->label(false)?>
		            			</td>
		            			<td class="user-form-field user-article" style = "display: <?=$type == 'article' ? 'table-cell' : 'none'?>;">
		            				<?= $form->field($resource, "[$key]doi")->textInput()->label(false)?>
		            			</td>
		            			<td class="user-form-field user-article" style = "display: <?=$type == 'article' ? 'table-cell' : 'none'?>;">
		            				<?= $form->field($resource, "[$key]pubmed_id")->textInput()->label(false)?>
		            			</td>
		            		</tr>
		            		<tr>
		            			<th class = "dataset-header-column user-form-field user-article" style = "display: <?=$type == 'article' ? 'table-cell' : 'none'?>;">Authors</th>
								<th class = "dataset-header-column user-form-field user-article" style = "display: <?=$type == 'article' ? 'table-cell' : 'none'?>;">Journal</th>
								<th class = "dataset-header-column user-form-field user-article" style = "display: <?=$type == 'article' ? 'table-cell' : 'none'?>;">Year</th>
		            		</tr>
		            		<tr>
		            			<td class="user-form-field user-article" style = "display: <?=$type == 'article' ? 'table-cell' : 'none'?>;">
		            				<?= $form->field($resource, "[$key]authors")->textInput()->label(false)?>
		            			</td>
		            			<td class="user-form-field user-article" style = "display: <?=$type == 'article' ? 'table-cell' : 'none'?>">
		            				<?= $form->field($resource, "[$key]journal")->textInput()->label(false)?>
		            			</td>
		            			<td class="user-form-field user-article" style = "display: <?=$type == 'article' ? 'table-cell' : 'none'?>;">
		            				<?= $form->field($resource, "[$key]year")->textInput()->label(false)?>
		            			</td>
		            		</tr>
		            	</table>
	            	<?php endforeach; ?>
	            	<div class = "row button-row-2">
                        <div class = "col-md-12">
                        	<?= Html::a('Add', 'javascript:void(0)', ['class' => 'btn btn-primary submit-button user-question-select add-resource', 'value'=>'create_add', 'name' => 'add-resource', 'id' => 'add-resource']) ?>
                        </div>
                    </div>
	            </div>
	            <div class="modal-footer">
	                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>    
	                <?= Html::submitButton('Submit', ['class' => 'btn btn-primary submit-button']) ?>
	            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<div class="modal fade bd-example-modal-lg import-resources" id="import-resources" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <?php $form = ActiveForm::begin(['action' => ['resources/resources-import']],['options' => ['id'=> 'resources-import', 'enctype' => 'multipart/form-data']]); ?>
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Import Resources</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body resource-modal-body">
        <?php foreach ($resourceZip as $kk => $zip): ?>
	        <table class="table table-striped table-bordered participants-table">
	            <tr class = "dataset-table-header-row">
	                <th colspan = "2" class = "dataset-header-column">
	                    File Upload (Currently supported for articles only!)
	                </th>
	            </tr>
	            <tr>
	                <td  colspan = "2" >
	                    <div class="text-center">
	                        <?= $form->field($zip, "[$kk]zipFile")->fileInput(['multiple' => false, 'id' => 'resource-file-input'])->label(false) ?>
	                        <input type="hidden" id="surveyId" value="<?=$survey->id?>" name="surveyId">
	                        <?= $form->field($zip, "[$kk]method")->hiddenInput(['id' => 'resource-method-0'])->label(false) ?>
	                        <input type="hidden" id="collectionId" value="<?= ($survey->getCollection()->one()) ? $survey->getCollection()->one()['id'] : '' ?>" name="collectionId">
	                    </div>
	                </td>
	            </tr>
	            <tr class = "dataset-table-header-row">
	                <th class = "dataset-header-column">
	                    Number of Abstracts <a class="link-icon fa fa-info-circle white" title ="If left blank, all the abstracts found will be used"></a>
	                </th>
	                <th class = "dataset-header-column">
	                    Selection Method <a class="link-icon fa fa-info-circle white" title ="If random is selected as the selection method, then abstracts will be selected randomly if a number is set in the Number of Abstracts field"></a>
	                </th>
	            </tr>
	            <tr>
	            	<td>
	            		<input type="text" name="numAbstracts" class="form-control">
	            	</td>
	            	<td>
	            		<select name="selectionOption" class="form-control">
	            			<option value="relevance">Relevance</option>
	            			<option value="random">Random</option>
	            		</select>
	            	</td>
	            </tr>
	        </table>  
        <?php endforeach; ?>   
			<div class = "col-md-10 d-flex align-items-center">
                <i class="fa fa-info-circle helper-message" ></i>&nbsp;
                Import a compressed (rar, zip, tar) file containing a csv file with the articles.
            </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <?= Html::submitButton('Submit', ['class' => 'btn btn-primary submit-button', 'name' => 'new-resource-file', 'value' => 'new-resource-file', 'id' => 'new-resource-file']) ?>
      </div>
      <?php ActiveForm::end(); ?>
    </div>
  </div>
</div>

<style type="text/css">

	body  .modal-dialog-db-resources  { /* Width */
	    width: 90%;
	    max-width:1400px;
	}
</style>

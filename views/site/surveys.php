<?php

/* @var $this yii\web\View */

use yii\widgets\ActiveForm;
use yii\bootstrap4\Progress;
use yii\helpers\Html;
use \yii\jui\DatePicker;
use yii\grid\GridView;
use yii\widgets\LinkPager;
use webvimark\extensions\GridPageSize\GridPageSize;

date_default_timezone_set("Europe/Athens"); 
$date = date('Y-m-d', time());
$superadmin = isset( Yii::$app->user->identity->superadmin ) && Yii::$app->user->identity->superadmin == 1;

$this->title = 'My Yii Application';
?>
<div class="site-index">
	
	<div class="outside-div">
		<div class = "row header-row dataset-header-row">
            <?php foreach ($tabs as $tab => $url): ?>
                <div class = "tab col-md-<?= 12 / sizeof($tabs) ?>" style = "border-bottom: <?= ( $tab == $message ) ? 'none !important;' : '' ?>">
                    <a class = "<?= ( ! $url['enabled'] ) ? 'url-disabled' : '' ?> " href = "<?= ($url['enabled']) ? $url['link'] : null ?>" ><h5 title = "<?= $message ?>" style = "opacity: <?= ( $url['enabled'] ) ? '1' : '' ?>"> <?= $tab ?></h5></a>
                </div>
            <?php endforeach; ?>
        </div>
       <div class = "row survey-fields">
       		<div class = "col-md-12">
       			
				<?= GridView::widget([
				    'dataProvider' => $surveys,
				    'layout'=>"{pager}\n{items}",
                	'summary' => "Showing {begin} - {end} of {totalCount} items",
                	'pager' => [ 
                		'class' => '\yii\bootstrap4\LinkPager',
                		'options' => ['class' => 'col-md-12']
                	],
				    'columns' => [

						[
							'headerOptions' => ['style'=>'text-align: center;'],
				            'contentOptions' => ['style'=>'text-align: center; vertical-align: middle;'],
			            	'label' => 'Owner',
			            	'format' => 'raw',
			            	'attribute' => 'owner_username',
			            	'value' => function($model, $key) {
						                	$groupNames = [];
						                	foreach($model->participatesin as $participants) {
						                		if ( $participants->owner == 1 ){
							                		$key = array_search($participants->userid, array_column((array) $model->user, 'id'));
							                		$groupNames[] = "<a href = 'index.php?r=user-management%2Fuser%2Fview&id=".$model->user[$key]['id']."'> ".$model->user[$key]['username']." </a><br>";
							                	}
						                	}
						                	return implode("\n", $groupNames);
						                }
			            ],

				        [
				        	'headerOptions' => ['style'=>'text-align: center;'],
				            'contentOptions' => ['style'=>'text-align: center; vertical-align: middle;'],
							'attribute'=>'name',
							'format' => 'raw',
							'value' => function($model, $key) 
			                	{
			                       	return "&nbsp;<a href = 'index.php?r=site%2Fsurveys-view&surveyid=".$model['id']."'>".$model['name']."</a>";
			                    }  	
						],
						// [
						// 	'headerOptions' => ['style'=>'text-align: center;'],
				  //           'contentOptions' => ['style'=>'text-align: center; vertical-align: middle;'],
						// 	'attribute'=>'created',
						// 	'visible' => Yii::$app->user->identity->hasRole(['Admin', 'Superadmin'])
						// ],
						[
							'headerOptions' => ['style'=>'text-align: center;'],
				            'contentOptions' => ['class' => 'start-time','style'=>'text-align: center; vertical-align: middle;'],
							'attribute' => 'starts',
							'label' => 'Starts in',
							'visible' => true
						],
						[	
							'headerOptions' => ['style'=>'text-align: center;'],
				            'contentOptions' => ['class' => 'end-time', 'style'=>' text-align: center; vertical-align: middle;'],
							'attribute' => 'ends',
							'label' => 'Expires in'
						],

			            [
			            	'headerOptions' => ['style'=>'text-align: center;'],
				            'contentOptions' => ['style'=>'text-align: center; vertical-align: middle;'],
			                'label' => 'Participants',
			                'format' => 'raw',
			                'attribute'=>'participants_count',
			                'value' => function($model) {
			                	$completed = 0;
			                	foreach ($model->participatesin as $key => $value) {
			                		if ( $value['finished'] == 1 ){
			                			$completed ++;
			                		}
			                	}
			                	$info = "<a class = 'fas fa-info-circle link-icon' title = 'Completed: ".$completed."'> </a>";
			                    return sizeof($model->participatesin)." ".$info;
			                },
			            ],

			            [
			            	'headerOptions' => ['style'=>'text-align: center;'],
				            'contentOptions' => ['style'=>'text-align: center; vertical-align: middle;'],
			                'label' => 'Active',
			                'attribute'=>'active',
			                'value' => function($model) {
			                    return ($model->active == '1') ? 'True' : 'False';
			                },
			            ],

			            [
			            	'headerOptions' => ['style'=>'text-align: center;'],
				            'contentOptions' => ['style'=>'text-align: center; vertical-align: middle;'],
			                'label' => 'Questions',
			                'format' => 'ntext',
			                'attribute'=>'questions_count',
			                'value' => function($model) {
			                    return sizeof($model->questions);
			                },
			            ],

			            [
			            	'headerOptions' => ['style'=>'text-align: center;'],
				            'contentOptions' => ['style'=>'text-align: center; vertical-align: middle;'],
			                'label' => 'Resources',
			                'format' => 'ntext',
			                'attribute'=>'resources_count',
			                'value' => function($model) {
			                    $test = "";
		                    	foreach ($model->collection as $col) {
		                    		$test .= sizeof($col->getResources()->all());
		                    	}
		                    	if ( $test == '' ){
		                    		return 0;
		                    	}
		                    	return $test;
			                },
			            ],

			            [
			            	'headerOptions' => ['style'=>'text-align: center;'],
				            'contentOptions' => ['style'=>'text-align: center; vertical-align: middle;'],
			                'label' => 'Annotations',
			                'attribute'=>'rates_count',
			                'visible' => Yii::$app->user->identity->hasRole(['Admin', 'Superadmin']),
			                'value' => function($model) {
			                    return ( isset( $model->rates_count) ) ? $model->rates_count : 0; 
			                },

			            ],
						
				        [

				            'class' => 'yii\grid\ActionColumn',
				            'controller' => 'site',
				            'header' => 'actions',
				            'headerOptions' => ['style'=>'text-align: center;'],
				            'contentOptions' => ['style'=>'text-align: center; vertical-align: middle;'],
				            'template' => '{view} {update} {delete} {rate} {open} {requests} {results}',
				            'buttons' =>
				            [
				            	'view' => function ($url, $model, $key) {
							    	
							        return Html::a('<i class="fas fa-eye" ></i>', 'index.php?r=site%2Fsurveys-view&surveyid='.$key);
							    },

							    'update' => function ($url, $model, $key) {
							    	$user_id = -1;
							    	foreach($model['participatesin'] as $participants) {
							    		if ( isset ( $participants['owner'] ) ){
					                    	if ( $participants['owner'] == 1 ){
					                    		$user_id = $participants['userid'];
					                    	}
					                    }
				                    }
							        return 
							        ( Yii::$app->user->identity->id == $user_id || Yii::$app->user->identity->hasRole("Superadmin") ) && ( $model->active != 1 )
							        ? Html::a('<i class="fas fa-edit link-icon"></i>', 'index.php?r=site%2Fsurvey-create&surveyid='.$key.'&edit=1') 
							        : '';
							    },
							    'delete' => function ($url, $model, $key) {
							    	$user_id = -1;
							    	foreach($model['participatesin'] as $participants) {
							    		if ( isset ( $participants['owner'] ) ){
					                    	if ( $participants['owner'] == 1 ){
					                    		$user_id = $participants['userid'];
					                    	}
					                    }
				                    }
							        return 
							        Yii::$app->user->identity->id == $user_id || Yii::$app->user->identity->hasRole("Superadmin")
							        ? Html::a('<i class="fas fa-trash-alt link-icon" ></i>', 'index.php?r=site%2Fsurvey-delete&surveyid='.$key) 
							        : '';
							    },
							    'rate' => function ($url, $model, $key) {
							        if ( ( Yii::$app->user->identity->hasRole(["Admin"]) && $model['locked'] == 0 ) || in_array(Yii::$app->user->identity->id, array_column($model->participatesin, 'userid') ) ){
							        	foreach ($model->participatesin as $participant) {
							    			if ( $participant->userid ==  Yii::$app->user->identity->id ){
							    				if ( $participant->finished == 1 ){
							    					return Html::a('<i class="fas fa-check link-icon" ></i>', 'javascript:void(0);', ['title' => 'Completed!']);
							    				}
							    				if ( $participant->request == 1 && $model->active == 1){
							    					return Html::a('<i class="fas fa-star link-icon" ></i>', 'index.php?r=site%2Fsurvey-rate&surveyid='.$key, []); // 'class' => 'btn btn-primary btn-sm'
							    				}
							    			}
								    	}
							        }
							    },

							    'open' => function ($url, $model, $key){
							    	$date = date('Y-m-d h:m:s', time());

							    	if ( ( $model['ends'] < strval( $date ) && $model['ends'] != '' ) || $model['locked'] == 1 && !Yii::$app->user->identity->hasRole(['Admin', 'Superadmin']) && !in_array(Yii::$app->user->identity->id, array_column($model->participatesin, 'userid') )){
							    		return Html::a('<i class="fas fa-lock" style = "color: #dd7777"></i>');
							    	}else{
							    		
								    	if ( in_array(Yii::$app->user->identity->id, array_column($model->participatesin, 'userid') ) ){
								    		foreach ($model->participatesin as $participant) {
								    			if ( $participant->userid ==  Yii::$app->user->identity->id ){
								    				if ( $participant->request == 0 ){
								    					return Html::a('<i class="fas fa-hourglass-half link-icon" title = "Pending acceptance" style ="color: orange;"></i>');
								    				}
								    			}
								    		}
								    	}else{
								    		
								    		return Html::a('<i class="fas fa-unlock link-icon" title = "Request to participate" "></i>', 'index.php?r=site%2Frequest-participation&surveyid='.$key);
								    	}
								    }
							    },

							    'requests' => function ($url, $model, $key){
							    	
							    	if ( in_array( $model->id, array_column ($this->params['requests'], 'surveyid') ) ){
							    		return Html::a('<i class="fas fa-inbox" style = "color: green"></i>', 'index.php?r=site%2Fuser-requests&surveyid='.$key);
							    	}
							    	
							    },

							    'results' => function ($url, $model, $key){
							    	
							    	if( Yii::$app->user->identity->getParticipatesin()->where(['owner' => 1, 'surveyid' => $model->id ])->all()){

							    		return Html::a('<i class="fa-solid fa-square-poll-horizontal"></i>', 'index.php?r=site%2Fsurveys-statistics&surveyid='.$key);
							    	}
							    	
							    },
							],
				        ],
				    ],
				]) ?>

			</div>
		</div>
		<div class = "row button-row">
			<div class = "col-md-9"></div>
            <div class = "col-md-3">
				<?= ( Yii::$app->user->identity->hasRole("Admin") ) ? Html::a('Create Campaign', ['site/survey-create'], ['class'=>'btn btn-primary submit-button']) : '' ?>
			</div>
		</div>
	</div>

    

</div>

<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use \kartik\datetime\DateTimePicker;
use kartik\select2\Select2;
use yii\bootstrap4\LinkPager;

date_default_timezone_set("Europe/Athens"); 
$date = date('Y-m-d hh:mm', time());

?>
<div class="survey-form">

    <div class ="outside-div">
        <?php if($message != ''): ?>
            <div class = "row header-row dataset-header-row"> 
                <?php include 'tabs.php'; ?>
            </div>
        <?php endif; ?>
        <div class ="about-text">
            <?php if($message == ''): ?>
                <div class = "row about-row">
                    <h2>Campaign Overview</h2>
                </div>
            <?php endif; ?>
            <?php if( in_array(Yii::$app->user->identity->id, $survey->getOwner() ) && ! $survey->active ): ?>
                <?php $form = ActiveForm::begin(['options' => ['class' => 'survey-create']]); ?>    
                    <div class = "row button-row">
                        <div class = "col-md-12 text-right">
                            <?= Html::a('Previous', 'index.php?r=site/badges-create&surveyid='.$survey->id, ['class' => 'btn btn-primary', 'name' => 'next']) ?>
                            <?php if( $survey->getCollection()->one() && sizeof($survey->getCollection()->one()->getResources()->all()) > 0 && sizeof($survey->getQuestions()->all()) > 0 && sizeof($survey->getCollection()->one()->getResources()->all()) >= $survey->minResEv): ?>
                                <!-- <div class = "col-md-1"> -->
                                    <?= Html::submitButton('Finish', ['class' => 'btn btn-primary', 'name' => 'finalize']) ?>
                                <!-- </div> -->
                            <?php else: ?>
                                <!-- <div class = "col-md-1"> -->
                                    <?= Html::submitButton('Finish', ['class' => 'btn btn-primary', 'disabled' => true, 'name' => 'finalize']) ?>
                                <!-- </div> -->
                            <?php endif; ?>
                        </div>
                        
                    </div>
                <?php ActiveForm::end(); ?>
            <?php endif; ?>
            <div class = "header-label">
                <h3 class = "surveys-view-header"> General Settings </h3>
                <table class="table table-striped table-bordered participants-table">  
                    <tr class = "dataset-table-header-row">
                        <th class = "dataset-header-column">
                            Name
                        </th>
                        <th class = "dataset-header-column">
                            Research Fields
                        </th>
                        <th class = "dataset-header-column">
                            Start Date
                        </th>
                        <th class = "dataset-header-column">
                            End Date
                        </th>
                        <th class = "dataset-header-column">
                            Availability
                        </th>
                        <th class = "dataset-header-column">
                            Minimum # of Resources Annotated
                        </th>
                        <th class = "dataset-header-column">
                            Maximum # of Resources Annotated
                        </th>
                        <th class = "dataset-header-column">
                            Minimum # of Evaluations per Resource
                        </th>
                        <th class = "dataset-header-column">
                            Maximum # of Evaluations per Resource
                        </th>
                    </tr>
                    <tr>
                        <td> <?= $survey->name ?></td>
                        <td> <?= str_replace("&&", ", ", $survey->fields) ?> </td>
                        <td> <?= isset( $survey->starts ) ? $survey->starts : '<i>Not determined yet</i>' ?> </td>
                        <td> <?= isset( $survey->ends ) ? $survey->ends : '<i>Not determined yet</i>' ?> </td>
                        <td> <?= ( $survey->locked ) ? 'Restricted' : 'Available' ?> </td>
                        <td> <?= ( $survey->minResEv > 0 ) ? $survey->minResEv : '<i>Not set</i>' ?> </td>
                        <td> <?= ( $survey->maxResEv > 0 ) ? $survey->maxResEv : '<i>Not set</i>' ?> </td>
                        <td> <?= ( $survey->minRespPerRes > 0 ) ? $survey->minRespPerRes : '<i>Not set</i>' ?> </td>
                        <td> <?= ( $survey->maxRespPerRes > 0 ) ? $survey->maxRespPerRes : '<i>Not set</i>' ?> </td>
                    </tr>
                </table>
                <table class="table table-striped table-bordered participants-table">  
                    <tr class = "dataset-table-header-row">
                        <th class = "dataset-header-column">
                            # of Resources
                        </th>
                        <th class = "dataset-header-column">
                            # of Questions
                        </th>
                        <th class = "dataset-header-column">
                            Resources Type
                        </th>
                    </tr>
                    <tr>
                        <td> <?= ( ! is_null ( $survey->getCollection()->one() ) ) ? sizeof( $survey->getCollection()->one()->getResources()->all() ) : '<i> No resources selected yet </i>' ?>
                        </td>
                        <td> <?= sizeof( $survey->getQuestions()->all() ) > 0 ? sizeof( $survey->getQuestions()->all() ) : '<i>No questions selected yet</i>' ?> </td>
                        <td> <?= ( ! is_null ( $survey->getCollection()->one() ) && isset( $survey->getCollection()->one()->getResources()->one()['type'] ) ) ? ucwords( $survey->getCollection()->one()->getResources()->one()['type']) : '<i> Not determined yet </i>' ?>
                        </td>
                    </tr>
                </table>
                <?php if( in_array(Yii::$app->user->identity->id, $survey->getOwner() ) && ! $survey->completed && ! $survey->active ): ?>
                    <?= Html::a('Edit', 'index.php?r=site/survey-create&surveyid='.$survey->id, ['class' => 'btn btn-primary submit-button', 'name' => 'next']) ?>
                <?php endif; ?>
                <br>
            </div>
            <br>
            <div class="header-label">
                <h3 class="surveys-view-header"> 
                    Resources 
                    <?php if(!$resources): ?>
                        <i class="fa fa-circle-xmark" title ="No resources set yet"> </i>
                    <?php endif; ?>
                    <?php if(sizeof($resources) < $survey->minResEv): ?>
                        <i class="fa fa-circle-exclamation" title ="Number of minimum resources evaluated set goal set is greater than the number of actual resources imported. Either lower the goal or import more resources."> </i>
                    <?php endif; ?>
                </h3>
                    <?= LinkPager::widget(['pagination' => $paginations[0]]) ?>    
                <div class="table-responsive">
                    <table class="table table-fixed table-striped table-bordered participants-table">
                        <thead>
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
                        </thead>  
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
                                <?= $resource->getRates()->groupBy(['resourceid', 'userid'])->count() ?>
                            </td>
                            <td>
                                <?= implode("<br>", $rates['resources'][$resource->id]['users']) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?> 
                    <?php if(!$resources): ?>
                        <tr>
                            <td colspan="100%"> No resources </td>
                        </tr>
                    <?php endif; ?>
                    </table>
                </div>
                <?php if( in_array(Yii::$app->user->identity->id, $survey->getOwner() ) && ! $survey->active ): ?>
                    <?= Html::a('Edit', 'index.php?r=site/resource-create&surveyid='.$survey->id, ['class' => 'btn btn-primary submit-button', 'name' => 'next']) ?>
                <?php endif; ?>
                <br>
            </div>
            <br>
            <div class="header-label">
                <h3 class="surveys-view-header"> 
                    Questions 
                    <?php if(!$questions): ?>
                        <i class="fa fa-circle-xmark" title ="No questions set yet"> </i>
                    <?php endif; ?>
                </h3>
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
                            <?= $question->getRates()->groupBy(['questionid', 'userid'])->count() ?>
                        </td>
                        
                        <td>
                            <?= implode("<br>", $rates['questions'][$question->id]['users']) ?>
                        </td>
                    </tr>
                <?php endforeach; ?> 
                <?php if(!$questions): ?>
                    <tr>
                        <td colspan="100%"> No questions </td>
                    </tr>
                <?php endif; ?>
                </table>
                <?php if( in_array(Yii::$app->user->identity->id, $survey->getOwner() ) && ! $survey->active ): ?>
                    <?= Html::a('Edit', 'index.php?r=site/questions-create&surveyid='.$survey->id, ['class' => 'btn btn-primary submit-button', 'name' => 'next']) ?>
                <?php endif; ?>
                <br>
            </div>
            <br>
            <div class = "header-label">
                <h3 class = "surveys-view-header">Participants</h3>
                <table class="table table-striped table-bordered participants-table">  
                    <tr class = "dataset-table-header-row">
                        <th class = "dataset-header-column">
                            Username
                        </th>
                        <th class = "dataset-header-column">
                            Research Interests
                        </th>
                        <th class = "dataset-header-column">
                            # of Surveys Participating
                        </th>
                    </tr>
                    <?php foreach ($survey->getParticipatesin()->all() as $participant): ?>
                        <tr>
                            <td>
                                <?= ( $participant->owner ) ? ' <i class="fa-solid fa-crown" title = "Owner"></i>' : '' ?>
                                <?=  "<a href = 'index.php?r=user-management%2Fuser%2Fview&id=".$participant->getUser()->select('id')->one()['id']."'> ".$participant->getUser()->select('username')->one()['username']." </a><br>";  ?>
                            </td>
                            <td>
                                <?= str_replace("&&", ", ", $participant->getUser()->select('fields')->one()['fields']) ?>
                            </td>
                            <td>
                                <?= sizeof( $participant->getUser()->one()->getParticipatesin()->all() ) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if( sizeof( $survey->getParticipatesin()->all() ) == 0 ): ?>
                        <tr>
                            <td colspan = "100%" style = "text-align: center;"> <i>No participants yet</i></td>
                        </tr>
                    <?php endif; ?>
                </table>
                <?php if( in_array(Yii::$app->user->identity->id, $survey->getOwner() ) && ! $survey->active ): ?>
                    <?= Html::a('Edit', 'index.php?r=site/participants-invite&surveyid='.$survey->id, ['class' => 'btn btn-primary submit-button', 'name' => 'next']) ?>
                <?php endif; ?>
                <br>
            </div>
            <br>
            <div class = "header-label">
                <h3 class = "surveys-view-header">Badges</h3>
                <?= LinkPager::widget(['pagination' => $paginations[1]]) ?>    
                <table class="table table-striped table-bordered participants-table">  
                    <tr class = "dataset-table-header-row">
                        <th class = "dataset-header-column">
                            Preview
                        </th>
                        <th class = "dataset-header-column">
                            Name
                        </th>
                        <th class = "dataset-header-column">
                            Earn Condition
                        </th>
                    </tr>
                    <?php foreach ($badges as $badge): ?>
                        <tr>
                            <td>
                                <?= '<img class = "badge-image-preview" src="data:image/png;base64,'.base64_encode($badge->getBadge()->select('image')->one()['image'] ).'"/>'  ?>
                            </td>
                            <td>
                                <?= $badge->getBadge()->select('name')->one()['name']  ?>
                            </td>
                            <td>
                                <?= "Annotate <b>".$badge->ratecondition."</b> Resources to earn" ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if( sizeof( $survey->getSurveytobadges()->all() ) == 0 ): ?>
                        <tr>
                            <td colspan = "3" style = "text-align: center;"> <i>No badges used</i></td>
                        </tr>
                    <?php endif; ?>
                </table>
                <?php if( in_array(Yii::$app->user->identity->id, $survey->getOwner() ) && ! $survey->active ): ?>
                    <?= Html::a('Edit', 'index.php?r=site/badges-create&surveyid='.$survey->id, ['class' => 'btn btn-primary submit-button text-right', 'name' => 'next']) ?>
                <?php endif; ?>
                <br>
            </div>
       
            
        </div>
    </div>
</div>


<!-- <style type="text/css">
.table-fixed tbody {
    height: 300px;
    overflow-y: auto;
    width: 100%;
}

.table-fixed thead,
.table-fixed tbody,
.table-fixed tr,
.table-fixed td,
.table-fixed th {
    display: block;
}

.table-fixed tbody td,
.table-fixed tbody th,
.table-fixed thead > tr > th {
    float: left;
    position: relative;

    &::after {
        content: '';
        clear: both;
        display: block;
    }
}
</style> -->
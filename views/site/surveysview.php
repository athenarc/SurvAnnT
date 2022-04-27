<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use \kartik\datetime\DateTimePicker;
use kartik\select2\Select2;

date_default_timezone_set("Europe/Athens"); 
$date = date('Y-m-d hh:mm', time());

?>
<div class="survey-form">

    <div class ="outside-div about-div">
        <?php if($message != ''): ?>
            <div class = "row header-row dataset-header-row"> 
                <?php foreach ($tabs as $tab => $url): ?>
                    <div class = "tab col-md" style = "border-bottom: <?= ( $tab == $message ) ? 'none !important;' : '' ?>">
                        <a class = "<?= ( ! $url['enabled'] ) ? 'url-disabled' : '' ?> " href = "<?= ($url['enabled']) ? $url['link'].$surveyid : null ?>" ><h5 title = "<?= $message ?>" style = "opacity: <?= ( $url['enabled'] ) ? '1' : '' ?>"> <?= $tab ?></h5></a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <div class ="about-text">
            <?php if($message == ''): ?>
                <div class = "row about-row">
                    <h2>Campaign Overview</h2>
                </div>
            <?php endif; ?>
            <br>
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
                <?php if( in_array(Yii::$app->user->identity->id, $survey->getOwner() ) && ! $survey->completed && ! $survey->active ): ?>
                    <?= Html::a('Edit', 'index.php?r=site/survey-create&surveyid='.$survey->id, ['class' => 'btn btn-primary submit-button', 'name' => 'next']) ?>
                <?php endif; ?>
            </div>
            <br>
            <div class = "header-label">
                <h3 class = "surveys-view-header"> Collection of Resources & Questions</h3>
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
                <?php if( in_array(Yii::$app->user->identity->id, $survey->getOwner() ) && ! $survey->active ): ?>
                    <?= Html::a('Edit', 'index.php?r=site/resource-create&surveyid='.$survey->id, ['class' => 'btn btn-primary submit-button', 'name' => 'next']) ?>
                <?php endif; ?>
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
                            <td colspan = "3" style = "text-align: center;"> <i>No participants yet</i></td>
                        </tr>
                    <?php endif; ?>
                </table>
                <?php if( in_array(Yii::$app->user->identity->id, $survey->getOwner() ) && ! $survey->active ): ?>
                    <?= Html::a('Edit', 'index.php?r=site/participants-invite&surveyid='.$survey->id, ['class' => 'btn btn-primary submit-button', 'name' => 'next']) ?>
                <?php endif; ?>
            </div>
            <br>
            <div class = "header-label">
                <h3 class = "surveys-view-header">Badges</h3>
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
                    <?php foreach ($survey->getSurveytobadges()->all() as $badge): ?>
                        <tr>
                            <td>
                                <?= '<img src="data:image/png;base64,'.base64_encode($badge->getBadge()->select('image')->one()['image'] ).'"/>'  ?>
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
                    <?= Html::a('Edit', 'index.php?r=site/badges-create&surveyid='.$survey->id, ['class' => 'btn btn-primary submit-button', 'name' => 'next']) ?>
                <?php endif; ?>
                <br>
            </div>
            <?php if ( in_array(Yii::$app->user->identity->id, $survey->getOwner()) ): ?>
                <div class = "header-label">
                    <h3 class = "surveys-view-header">Results</h3>
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
                                <?= $resource->getRates()->groupBy(['resourceid', 'userid'])->count() ?>
                            </td>
                            <td>
                                <?= implode("<br>", $rates['resources'][$resource->id]['users']) ?>
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
                                <?= $question->getRates()->groupBy(['questionid', 'userid'])->count() ?>
                            </td>
                            
                            <td>
                                <?= implode("<br>", $rates['questions'][$question->id]['users']) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?> 
                    </table>
                </div>
            <?php endif; ?>
            <br>
            <?php if( in_array(Yii::$app->user->identity->id, $survey->getOwner() ) && ! $survey->active ): ?>
                <?php $form = ActiveForm::begin(['options' => ['class' => 'survey-create']]); ?>    
                    <div class = "row button-row">
                        <div class = "col-md-10"></div>
                        <div class = "col-md-1">
                            <?= Html::a('Previous', 'index.php?r=site/badges-create&surveyid='.$survey->id, ['class' => 'btn btn-primary submit-button', 'name' => 'next']) ?>
                        </div>
                        <?php if( $survey->getCollection()->one() && sizeof($survey->getCollection()->one()->getResources()->all()) > 0 && sizeof($survey->getQuestions()->all()) > 0 ): ?>
                            <div class = "col-md-1">
                                <?= Html::submitButton('Finish', ['class' => 'btn btn-primary submit-button', 'name' => 'finalize']) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php ActiveForm::end(); ?>
            <?php endif; ?>
        </div>
    </div>
</div>



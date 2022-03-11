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
        <div class ="about-text">
            <div class = "row about-row">
                <h2>Campaign Overview</h2>
            </div>
            <br>
            <div class = "header-label">
                <h3> Campaign </h3>
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
                            Annotations Goal
                        </th>
                    </tr>
                    <tr>
                        <td> <?= $survey->name ?></td>
                        <td> <?= str_replace("&&", ", ", $survey->fields) ?> </td>
                        <td> <?= isset( $survey->starts ) ? $survey->starts : '<i>Not determined yet</i>' ?> </td>
                        <td> <?= isset( $survey->ends ) ? $survey->ends : '<i>Not determined yet</i>' ?> </td>
                        <td> <?= ( $survey->locked ) ? 'Restricted' : 'Available' ?> </td>
                        <td> <?= ( $survey->minResEv > 0 ) ? $survey->minResEv : '<i>Not set</i>' ?> </td>
                    </tr>
                </table>
            </div>
            <br>
            <div class = "header-label">
                <h3>Resources & Questions</h3>
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
            </div>
            <br>
            <div class = "header-label">
                <h3>Participants</h3>
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
            </div>
            <br>
            <div class = "header-label">
                <h3>Badges</h3>
                <table class="table table-striped table-bordered participants-table">  
                    <tr class = "dataset-table-header-row">
                        <th class = "dataset-header-column" style = "width: 50%;">
                            Preview
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
                                <?= $badge->ratecondition ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if( sizeof( $survey->getSurveytobadges()->all() ) == 0 ): ?>
                        <tr>
                            <td colspan = "2" style = "text-align: center;"> <i>No badges used</i></td>
                        </tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>
</div>



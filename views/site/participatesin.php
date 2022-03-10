<?php
 
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $model app\models\participatesin */
/* @var $form ActiveForm */
$this->registerJsFile('@web/js/veto.js', ['position' => View::POS_END, 'depends' => [\yii\web\JqueryAsset::className()]]);

?>
<div class="participatesincreate survey-form">

    <div class="outside-div">

        <div class = "row header-row dataset-header-row">
            <?php foreach ($tabs as $tab => $url): ?>
                <div class = "tab col-md" style = "border-bottom: <?= ( $tab == $message ) ? 'none !important;' : '' ?>">
                    <a class = "<?= ( ! $url['enabled'] ) ? 'url-disabled' : '' ?> " href = "<?= ($url['enabled']) ? $url['link'].$surveyid : null ?>" ><h5 title = "<?= $message ?>" style = "opacity: <?= ( $url['enabled'] ) ? '1' : '' ?>"> <?= $tab ?></h5></a>
                </div>
            <?php endforeach; ?>
        </div>

        <?php $form = ActiveForm::begin(['action' =>['site/participants-invite', 'surveyid' => $survey->id], 'options' => ['class' => 'survey-create']]); ?>

        <div class = "row d-flex align-items-center" > 
            <div class="col-md-8">
                <h3 style = "margin-left: 2%; padding-left: 1%;"> Registered Users</h3>
                
            </div>
            <div class = "col-md-4 text-right" style = "padding-right: 3%;">
                <?= Html::checkbox( 'reset-filter', $limit_on_fields, ['class' => 'no-margin-label', 'id' => 'reset-filter', 'label' => 'Limit users based on Research fields', 'style' => 'margin-bottom:0%;']) ?>                       
            </div>
            <hr style = "width:95%; background-color:white; margin-top: 1%;">
        </div> 

        <!-- <h3 style = "margin-left: 2%; padding-left: 1%;"> Registered Users</h3> -->
        <!-- <hr style = "width:95%; background-color:white;"> -->
        <br>
        <div class = "row survey-fields table-row">
            

        </div>
        <br><br>
        <h3 style = "margin-left: 2%; padding-left: 1%;"> Invite Users to Register & Participate</h3>
        <hr style = "width:95%; background-color:white;">
        <br>
        <div class = "" style = "margin-left: 2%; margin-right: 2%;">
            <div class ="row" style = "padding-left:2%; padding-right: 2%;">
                <div class = "col-md-4">
                    <table class="table table-striped table-bordered invite-table"> 
                        <thead class="control-label" for="surveys-starts" style = "text-decoration: none;">
                            <tr>
                                <th style = "width: 75%;">Email</th>
                                <th  style = "text-align: center; width: 25%;" >
                                    <a class="fas fa-angle-down" style = "cursor: pointer; text-decoration: none;"></a>
                                </th>
                            </tr>
                        </thead>
                        <tbody class = "invite-body">
                            <tr>
                                <td>
                                    <input type="email" name="new-user-email" class ="form-control">
                                </td>
                                <td>
                                    <a id = "invite-new-user" class="fas fa-envelope add-user" title = "Invite!" style = "color: #77dd77; cursor: pointer; text-decoration: none;"></a>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <a id = "add-invitations" class="btn btn-primary submit-button white" title = "Add more invitation inputs!" style = "cursor: pointer; text-decoration: none;">Add</a>

                </div>
                <div class = "offset-md-2 col-md-6">
                <div class="card border-info">
                    <div class="card-body text-info">
                        <h4 class="card-title">Participant recommendations</h4>

                        <h6 class="card-subtitle mb-2 text-muted">powered by <a href="http://veto.imsi.athenarc.gr" target="_blank" class="text-info">VeTo <i class="fa-solid fa-square-arrow-up-right"></i></a></h6>
                        
                        <div class="card-text mb-2">Retrieve recommendations for individuals that share similar expertise with the current set of selected participants.</div>

                        <div class="mb-2">
                            <span style="display: inline-block;"> 
                                Dataset <i class="fa-solid fa-circle-info" title="VeTo uses this dataset to compute similarities between authors"></i>
                            </span>
                            <span class="" style="display: inline-block;">
                                <?= Html::dropDownList('veto-dataset', '', [ 'dblp' => 'DBLP' ], ['class' => 'form-control btn-sm']) ?>
                            </span>
                        </div>
                        <div class="mb-2">
                            Found participants: <span id="found-participants" class="text-secondary">
                                <div id="found-participants-loading" class="spinner-grow spinner-grow-sm text-info" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                            </span>

                            <?php 
                                $participants = array_filter($users, function($u) {
                                    return ($u['participates'] == true);  
                                });

                                echo Html::hiddenInput('veto-participants', json_encode($participants), ['id' => 'veto-participants']);
                            ?>
                        </div>
                        <div class="text-center">
                            <div style="padding-bottom: 10px;">
                                <button id="veto-exec-button" class="btn btn-info btn-sm" disabled="true" title="No selected participant can be found in the selected dataset">
                                    <i class="fa-solid fa-magnifying-glass"></i> Find
                                </button>
                            </div>
                            <div id="veto-loading-spinner" style="display: none;">
                                <i class="fa fa-spinner fa-pulse fa-1x"></i> <span id="loading-message"></span>
                            </div>
                            <div id="veto-loading-progress" style="display: none;" class="progress">
                                <div id="veto-loading-message" class="progress-bar progress-bar-striped progress-bar-animated bg-info" role="progressbar"></div>
                            </div>
                        </div>
                        
                        <!-- result are rendered with js in this list -->
                        <ul id="veto-results" class="list-group list-group-flush">
                        </ul>
                    </div>
                    </div>
                </div>
            </div>
        </div>
        <br>
        <?= Html::hiddenInput('', $action, ['id' => 'action']) ?>
        <?= Html::hiddenInput('', json_encode( $users ), ['id' => 'users_array']) ?>
        <?= Html::hiddenInput('', $survey->id, ['id' => 'surveyid']) ?>

        <div class = "row button-row">
            <div class = "col-md-10"></div>
            <div class = "col-md-1">
                <?= Html::a( 'Previous', $tabs['Questions']['link'].$surveyid, ['class' => 'btn btn-primary submit-button ', 'name' => 'test-name']); ?>
            </div>
            <div class = "col-md-1">
                <?= Html::submitButton('Next', ['class' => 'btn btn-primary submit-button ', 'name' => 'badges-create']) ?>
            </div>
        </div>

    </div>
    <?php ActiveForm::end(); ?>
</div>


<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->registerJsFile(
    '@web/js/surveysstatistics.js',
    ['depends' => [\yii\web\JqueryAsset::class]]
);

$this->registerCssFile(
    '@web/css/surveysstatistics.css',
    ['depends' => [\yii\bootstrap4\BootstrapAsset::class]]
);

?>
<div class="survey-form">

    <div class ="outside-div about-div">
      <div class ="about-text">
        <?php $form = ActiveForm::begin(['options' => ['class' => 'surveys-statistics-form']]); ?>
          <div class = "row surveys-view-header">
            <div class="col-md-3">
              <h3 >Select Campaign:</h3>
            </div>
            <div class="col-md-4">
              <?= Html::dropDownList( 'surveyid', $surveyid, $survey_names, ['class' => 'form-control surveys-selection']); ?>
            </div>
          </div>
          <?php foreach($surveys as $survey): ?>
            <div class="row surveys-view-header survey-row align-items-center">
              <div class="col-md-4">
                <h4> <?= $survey->name ?> </h4>
              </div>
              <div class="col-md-8 text-right">
                <?=  Html::a( '<i class="fas fa-angle-down"></i>', '', ['class' => 'btn submit-button pull-left display-section', 'id' => 'show-'.$survey->id, 'style' => 'float: unset !important;']) ?>
              </div>
            </div>
            <div class="row surveys-view-header survey-row section-<?=$survey->id?>" style = "display: <?= (sizeof($surveys) > 1 ) ? 'none;' : 'block;' ?>;">
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
                    <th class = "dataset-header-column">
                        # of Participants
                    </th>
                </tr>
                <tr>
                    <td> <?= $survey->name ?></td>
                    <td> <?= str_replace("&&", ", ", $survey->fields) ?> </td>
                    <td> <?= isset( $survey->starts ) ? $survey->starts : '<i>Not determined yet</i>' ?> </td>
                    <td> <?= isset( $survey->ends ) ? $survey->ends : '<i>Not determined yet</i>' ?> </td>
                    <td> <?= ( $survey->locked ) ? 'Restricted' : 'Available' ?> </td>
                    <td> <?= ( $survey->minResEv > 0 ) ? $survey->minResEv : '<i>Not set</i>' ?> </td>
                    <td> <?= $survey->getParticipatesin()->count() ?></td>
                </tr>
              </table>
              <table class="table table-striped table-bordered participants-table">  
                <tr class = "dataset-table-header-row">
                    <th class = "dataset-header-column">
                        Participants Area of Expertise
                    </th>
                </tr>
                <tr>
                  <td>
                    <div class = "chart">
                      <?= 
                        \onmotion\apexcharts\ApexchartsWidget::widget([
                          'type' => 'bar', // default area
                          'height' => '200', // default 350
                          // 'width' => '800', // default 100%
                          'chartOptions' => [
                              'chart' => [
                                  'toolbar' => [
                                      'show' => true,
                                      'autoSelected' => 'zoom'
                                  ],
                              ],
                              'xaxis' => [
                                  'type' => 'category',
                              ],
                              'plotOptions' => [
                                  'bar' => [
                                      'horizontal' => false,
                                  ],
                              ],
                              'dataLabels' => [
                                  'enabled' => true,
                              ],
                              'stroke' => [
                                  'show' => true,
                                  'colors' => ['transparent']
                              ],
                              'legend' => [
                                  'position' => 'bottom',
                                  'horizontalAlign' => 'center',
                                  'onItemHover' => [
                                    'highlightDataSeries' => true
                                  ],
                              ],
                          ],
                          'series' => $series[$survey->id]['user_res_fields']['data']
                      ]);
                      ?>
                    </div>
                  </td>
                </tr>
              </table>
               <table class="table table-striped table-bordered participants-table">  
                <tr class = "dataset-table-header-row">
                    <th class = "dataset-header-column">
                        Mean Question Values Per Resource (Numeric Questions)
                    </th>
                </tr>
                <tr>
                  <td>
                    <div class = "chart">
                      <?= 
                        \onmotion\apexcharts\ApexchartsWidget::widget([
                          'type' => 'line', 
                          'height' => '200', 
                          'chartOptions' => [
                              'chart' => [
                                  'toolbar' => [
                                      'show' => true,
                                      'autoSelected' => 'zoom'
                                  ],
                              ],
                              'xaxis' => [
                                  // 'type' => 'category'
                                'categories' => $series[$survey->id]['questions']['categories']
                              ],
                              'plotOptions' => [
                                  'bar' => [
                                      'horizontal' => false,
                                  ],
                              ],
                              'dataLabels' => [
                                  'enabled' => true,
                              ],
                              'stroke' => [
                                  'show' => true,
                                  'curve' => 'smooth',
                                  // 'colors' => ['transparent']
                              ],
                              // 'legend' => [
                              //     'position' => 'bottom',
                              //     'horizontalAlign' => 'center',
                              //     'onItemHover' => [
                              //       'highlightDataSeries' => true
                              //     ],
                              // ],
                          ],
                          'series' => $series[$survey->id]['questions']['data']
                      ]);
                      ?>
                    </div>
                  </td>
                </tr>
              </table>
            </div>
          <?php endforeach; ?>
        <?php ActiveForm::end(); ?>
      </div>
  </div>
</div>
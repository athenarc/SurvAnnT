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
                        # of Participants
                    </th>
                    <th class = "dataset-header-column">
                        # of Resources
                    </th>
                    <th class = "dataset-header-column">
                        # of Questions
                    </th>
                </tr>
                <tr>
                    <td> <?= $survey->name ?></td>
                    <td> <?= str_replace("&&", ", ", $survey->fields) ?> </td>
                    <td> <?= isset( $survey->starts ) ? $survey->starts : '<i>Not determined yet</i>' ?> </td>
                    <td> <?= isset( $survey->ends ) ? $survey->ends : '<i>Not determined yet</i>' ?> </td>
                    <td> <?= ( $survey->locked ) ? 'Restricted' : 'Available' ?> </td>
                    <td> <?= $survey->getParticipatesin()->count() ?></td>
                    <td> <?= $survey->getCollection()->one() ? $survey->getCollection()->one()->getResources()->count() : 0 ?></td>
                    <td> <?= $survey->getQuestions()->count() ?></td>
                </tr>
              </table>
              <table class="table table-striped table-bordered participants-table">
                <tr class = "dataset-table-header-row">
                  <th class = "dataset-header-column">
                    Campaign Goal Description
                  </th>
                  <th class = "dataset-header-column">
                    Goal
                  </th>
                  <th class = "dataset-header-column">
                    Progress
                  </th>
                </tr>
                <?php foreach ($survey->getCompletionCriteria() as $criteria): ?>
                  <tr>
                    <td><?=$criteria['description']?></td>
                    <td><?=$criteria['goal']?></td>
                    <td><?=$criteria['progress']?></td>
                  </tr>
                <?php endforeach; ?>
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
                          'height' => '350', // default 350
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
                                  'enabled' => false,
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
              <?php if ( sizeof( array_filter( array_column( $series[$survey->id]['questions']['data'], 'data' ) ) ) > 0 ): ?>
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
                                'legend' => [
                                    'position' => 'bottom',
                                    'horizontalAlign' => 'center',
                                    'onItemHover' => [
                                      'highlightDataSeries' => true
                                    ],
                                ],
                            ],
                            'series' => $series[$survey->id]['questions']['data']
                        ]);
                        ?>
                      </div>
                    </td>
                  </tr>
                </table>
              <?php else: ?>
                <table class="table table-striped table-bordered participants-table">  
                  <tr class = "dataset-table-header-row">
                      <th class = "dataset-header-column">
                            Mean Question Values Per Resource (Numeric Questions: 
                            <?php foreach ( $survey->getQuestions()->where(['!=', 'answertype', 'textInput'])->all() as $q): ?>
                              <?= $q->id.' <a class="fas fa-info-circle link-icon white" title = "'.$q->question.'"></a> ' ?>
                            <?php endforeach; ?>
                           )
                      </th>
                  </tr>
                  <tr>
                    <td>
                      No data available yet!
                    </td>
                  </tr>
                </table>
              <?php endif; ?>
              <?php foreach ($survey->getQuestions()->where(['answertype' => 'textInput'])->all() as $question): ?>
                <?php if ( sizeof( $series[$survey->id]['questions_text_input']['data'][$question->id] ) > 0 ): ?>
                  <table class="table table-striped table-bordered participants-table">  
                    <tr class = "dataset-table-header-row">
                        <th class = "dataset-header-column">
                            Text Labels ( Question: <?=$question->id?> <a class="fas fa-info-circle link-icon white" title = "<?= $question->question ?>"> </a> )
                        </th>
                    </tr>
                    <tr>
                      <td>
                        <div class = "chart">
                          <?= 
                            \onmotion\apexcharts\ApexchartsWidget::widget([
                              'type' => 'pie', 
                              'height' => '300', 
                              'chartOptions' => [
                                  'chart' => [
                                      'toolbar' => [
                                          'show' => true,
                                          'autoSelected' => 'zoom'
                                      ],
                                  ],
                                  'labels' => $series[$survey->id]['questions_text_input']['categories'][$question->id],

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
                                  'legend' => [
                                      'position' => 'bottom',
                                      'horizontalAlign' => 'center',
                                      'onItemHover' => [
                                        'highlightDataSeries' => true
                                      ],
                                  ],
                              ],
                              'series' => $series[$survey->id]['questions_text_input']['data'][$question->id]
                          ]);
                          ?>
                        </div>
                      </td>
                    </tr>
                  </table>
                <?php else: ?>
                  <table class="table table-striped table-bordered participants-table">  
                    <tr class = "dataset-table-header-row">
                        <th class = "dataset-header-column">
                            Text Labels ( Question: <?=$question->id?> <a class="fas fa-info-circle link-icon white" title = "<?= $question->question ?>"> </a> )
                        </th>
                    </tr>
                    <tr>
                      <td>
                        No data available yet!
                      </td>
                    </tr>
                  </table>
                <?php endif; ?>
              <?php endforeach; ?>
              <table class="w-100">
                <tr class = "text-right">
                  <td>
                    <?=  Html::a( 'Export <i class="fa-solid fa-download"></i>', '#', ['class' => 'btn submit-button', 'style' => 'float: unset !important;']) ?>
                  </td>
                </tr>
              </table>
            </div>
            
          <?php endforeach; ?>
        <?php ActiveForm::end(); ?>
      </div>
  </div>
</div>
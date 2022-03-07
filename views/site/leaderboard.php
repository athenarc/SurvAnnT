
<?php
$this->registerJsFile(
    '@web/js/leaderboard.js',
    ['depends' => [\yii\web\JqueryAsset::class]]
);

$this->registerCssFile(
    '@web/css/leaderboard.css',
    ['depends' => [\yii\bootstrap4\BootstrapAsset::class]]
);

?>
<div class = "outside-div" style = "">
  <div class="container-wrap">
    <?php foreach ($survey_leaderboards as $leaderboard_key => $leaderboard): ?>
      <div id = "leaderboard-div-<?=$leaderboard_key?>" class = "" style = "margin: 2%; padding: 3%; border:2px solid white; border-radius: 25px;">
        <section id="leaderboard-<?=$leaderboard_key?>">
          <nav class="ladder-nav">
            <div class="ladder-title">
            <h1><?= str_replace( "_", " ", $leaderboard_key) ?></h1>
            <?= ( isset( $leaderboard[0]['username'] ) ? $leaderboard[0]['username'].' <i class="fas fa-trophy" style = "color: #d4af37;"></i>' : '' ) ?>
            &nbsp;
            <?= ( isset( $leaderboard[1]['username'] ) ? $leaderboard[1]['username'].' <i class="fas fa-trophy" style = "color: silver;"></i>' : '' ) ?>
            &nbsp;
            <?= ( isset( $leaderboard[2]['username'] ) ? $leaderboard[2]['username'].' <i class="fas fa-trophy" style = "color: #CD7F32;"></i>' : '' ) ?>
            </div>
            <div class="ladder-search">
              <input type="text" id="search-leaderboard-<?=$leaderboard_key?>" class="live-search-box" placeholder="Search User, Annotations..." />
            </div>
          </nav>
          <table id="rankings-<?=$leaderboard_key?>" class="leaderboard-results" width="100%">
            <thead>
              <tr style = "text-align: center;">
                <th>Username</th>
                <th>Annotations</th>
                <th>Badges  
                  <a href="#" data-toggle="modal" data-target=".badges"><i class="fas fa-info-circle badges-info"></i></a>
                </th>
                <th>Points
                  <a href="#" data-toggle="modal" data-target=".achievements"><i class="fas fa-info-circle achievements-info"></i></a>
                </th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($leaderboard as $key => $lead): ?>
                <tr> 
                  <td>
                    <?= $lead['username'] ?>
                  </td>
                  <td><?= $lead['annotations'] ?></td>
                  <td><?= $lead['badge'] ?></td>
                  <td><?= $lead['points'] ?></td>
                </tr>
                <br>
              <?php endforeach; ?>   
            </tbody>
          </table>
        </section>
      </div>
    <?php endforeach; ?> 
  </div>
</div>
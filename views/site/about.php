<?php

/* @var $this yii\web\View */

use yii\helpers\Html;


$this->title = 'About';
?>
<div class="survey-form" >


    <div class ="outside-div" style = "background-color: #16122d !important;
opacity: 0.5;
color: white !important; margin-top: 15% !important; width: 80% !important;">
        <div class = "row header-row dataset-header-row" style = "">
            <?php foreach ($tabs as $tab => $url): ?>
                <div class = "tab col-md-<?= 12 / sizeof($tabs) ?>" style = "border-bottom: <?= ( $tab == $message ) ? 'none !important;' : '' ?>">
                    <a class = "<?= ( ! $url['enabled'] ) ? 'url-disabled' : '' ?> " href = "<?= ($url['enabled']) ? $url['link'].$tab : null ?>" ><h5 title = "<?= $message ?>" style = "opacity: <?= ( $url['enabled'] ) ? '1; color: white !important;' : 'color: white !important;' ?>"> <?= $tab ?></h5></a>
                </div>
            <?php endforeach; ?>
        </div>
        <div class = "col-md-12 dataset-form middle-text">
        <?php foreach ($about as $header => $text): ?>
            <?php if( $header == $message ): ?>
                <p class = "text-left"> <?= $text['text'] ?> </p>
            <?php endif; ?>
        <?php endforeach; ?>  
        </div>
    </div> 

</div>

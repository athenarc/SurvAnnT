<?php

/* @var $this yii\web\View */

use yii\helpers\Html;


$this->title = 'About';
?>
<div class="survey-form" >


    <div class ="outside-div about-div" >
        
        <div class = "about-text">
        <?php foreach ($about as $header => $text): ?>
            <?php if( ! isset( $text['enabled'] ) || (  isset( $text['enabled'] )  && $text['enabled']) ): ?>
                <div class="row about-row" >
                    <h3> 
                        <i><?= $header ?></i> 
                    </h3>
                </div>
                <?php if($header == 'Team'): ?>
                    <?php foreach($text as $member): ?>
                        <div class = "row align-items-center">
                            <div class="col-md-1 col-xs-2 ">
                                <img class = "img-circle img-responsive" src="<?=$member['image']?>" alt = "<?=$member['name']?>">
                            </div>
                            <div class="col-md-4">
                                <a href = "<?=$member['url']?>" target = "_blank"> <?= $member['name'] ?> 
                                    <i class="fas fa-external-link-square"></i> 
                                </a>
                                <br>
                                <?= $member['title'] ?>
                                <br>
                                <?= $member['email'] ?>
                            </div>
                        </div>
                        <br>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p> <?= $text['text'] ?> </p>
                <?php endif; ?>
            <?php endif; ?>
            
            
        <?php endforeach; ?>  
        </div>
    </div> 

</div>

<?php

/* @var $this yii\web\View */
use yii\bootstrap4\Progress;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

use yii\widgets\DetailView;
$this->title = 'My Yii Application';
?>
<div class="site-index">

	<div class="outside-div">
		<div style = "margin:2%;"> 
			<?= isset($message) ? $message : '' ?>
		</div>
		<div class = "row button-row">
	        <div class = "col-md-11"></div>
	        <div class = "col-md-1">
	            <?= Html::a( 'Campaigns', ['site/surveys-view'], ['class' => 'btn btn-primary submit-button ']); ?>
	        </div>
	    </div>
	</div>
	
</div>

<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\assets\AppAsset;
use app\widgets\Alert;
use yii\bootstrap4\Breadcrumbs;
use yii\bootstrap4\Html;
use yii\bootstrap4\Nav;
use yii\bootstrap4\NavBar;
use yii\helpers\Url;
use webvimark\modules\UserManagement\UserManagementModule;

AppAsset::register($this);
?>
<script src="https://kit.fontawesome.com/889ffab235.js"></script>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode(Yii::$app->params['title']) ?></title>
    <?php $this->head() ?>
</head>
<body class="d-flex flex-column h-100">
<?php $this->beginBody() ?>

<header>
    <?php
    NavBar::begin([
        'brandLabel' => Yii::$app->params['title'],
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar navbar-expand-md navbar-dark bg-dark fixed-top',
        ],
    ]);
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav'],
        'encodeLabels' => false,
        'items' => [
            // ['label' => 'Home <i class="fas fa-home"></i>', 'url' => ['/site/index']],
            ['label' => 'About <i class="fas fa-question-circle"></i>', 'url' => ['/site/about']],
            isset (Yii::$app->user->identity) && Yii::$app->user->identity->hasRole(["Rater", "Admin", "Superadmin"]) ? (['label' => 'Leaderboard <i class="fas fa-book"></i>', 'url' => ['/site/leaderboard']]) : '',
            isset (Yii::$app->user->identity) && Yii::$app->user->identity->hasRole(["Rater", "Admin", "Superadmin"]) ? (['label' => 'Campaigns <i class="fas fa-poll"></i>', 'url' => ['/site/surveys-view']]) : '',

            isset (Yii::$app->user->identity) && Yii::$app->user->identity->hasRole(["Rater", "Admin", "Superadmin"]) ? ([
                'label' => ( isset($this->params['requests']) && sizeof($this->params['requests']) > 0 ) ? 'Admin <span class = "dot" style = "background-color:red; color:white;">'.sizeof($this->params['requests']).'</span><i class="fas fa-cogs"></i>' : 'Admin <i class="fas fa-cogs"></i>',
                // 'items' => UserManagementModule::menuItems()
                'items' => 
                    [
                       Yii::$app->user->identity->hasRole(["Admin", "Superadmin"]) ? ['label' =>  UserManagementModule::t('back', 'Users') .' <i class="fas fa-users"></i> ', 'url' => ['/user-management/user/index']] : '',
                       Yii::$app->user->identity->hasRole(["Admin", "Superadmin"]) ? ['label' =>  UserManagementModule::t('back', 'Roles') .' <i class="fas fa-users"></i> ', 'url' => ['/user-management/role/index']] : '',
                       Yii::$app->user->identity->hasRole(["Admin", "Superadmin"]) ? ['label' =>  UserManagementModule::t('back', 'Permissions') .' <i class="fas fa-users"></i> ', 'url' => ['/user-management/permission/index']] : '',
                       Yii::$app->user->identity->hasRole(["Admin", "Superadmin"]) ? ['label' =>  UserManagementModule::t('back', 'Permission groups') .' <i class="fas fa-users"></i> ', 'url' => ['/user-management/auth-item-group/index']] : '',
                       Yii::$app->user->identity->hasRole(["Admin", "Superadmin"]) ? ['label' =>  UserManagementModule::t('back', 'Visit log') .' <i class="fas fa-users"></i> ', 'url' => ['/user-management/user-visit-log/index']] : '',
                        ['label' => 
                            isset($this->params['requests']) && sizeof($this->params['requests']) > 0 
                            ? 'My Campaigns <span class = "dot" style = "background-color:red; color:white;">'.sizeof($this->params['requests']).'</span> <i class="fas fa-database"></i>'
                            : 'My Campaigns <i class="fas fa-database"></i>'
                            , 'url' => ['/site/my-surveys-view']],
                       Yii::$app->user->identity->hasRole(["Admin", "Superadmin"]) ? ['label' => 'Statistics <i class="fas fa-chart-bar"></i>', 'url' => ['/site/index']] : '',
                       ['label' => 'Password <i class="fas fa-key"></i>', 'url'=>['/user-management/auth/change-own-password']],
                       ['label' => 'My profile <i class="fas fa-user"></i>', 'url' =>  Url::to(['/user-management/user/update', 'id' => Yii::$app->user->identity->id])],
                    ]
            ]) : '',

            !Yii::$app->user->isGuest && ! Yii::$app->user->identity->hasRole(["Rater", "Admin", "Superadmin"]) ? (
                ['label'=>'Password <i class="fas fa-key"></i>', 'url'=>['/user-management/auth/change-own-password']] ) : '',
            !Yii::$app->user->isGuest && ! Yii::$app->user->identity->hasRole(["Rater", "Admin", "Superadmin"]) ? (
                ['label' => 'My profile <i class="fas fa-user"></i>', 'url' =>  Url::to(['/user-management/user/update', 'id' => Yii::$app->user->identity->id])] ) : '',
            Yii::$app->user->isGuest ? (
                ['label'=>'Register <i class="fas fa-user-plus"></i>', 'url'=>['/user-management/auth/registration']] ) : '',
            Yii::$app->user->isGuest ? (
                ['label' => 'Login <i class="fas fa-sign-in-alt"></i>', 'url' => ['/site/login']]
            ) : (
                '<li>'
                . Html::beginForm(['/site/logout'], 'post', ['class' => 'form-inline'])
                . Html::submitButton(
                    'Logout (' . Yii::$app->user->identity->username . ') <i class="fas fa-sign-out-alt"></i>',
                    ['class' => 'btn btn-link logout']
                )
                . Html::endForm()
                . '</li>'
            )
        ],
    ]);
    NavBar::end();
    ?>
</header>

<main role="main" class="flex-shrink-0">
    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</main>

<footer class="footer mt-auto py-3 text-muted">
    <div class="container">
        <p class="float-left">&copy; SurvAnnT <?= date('Y') ?></p>
        <p class="float-right">
            Icons made by <a href="https://www.flaticon.com/authors/iconixar" title="iconixar">iconixar</a> from <a href="https://www.flaticon.com/" title="Flaticon">www.flaticon.com</a>
        </p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>

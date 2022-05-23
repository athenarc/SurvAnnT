<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Main application asset bundle.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',
        'css/main.css',
        'css/rate.css',
        'css/progress_bar.css',
        'css/index.css',
        'css/about.css',
        'css/surveyform.css',
        'css/about.css',
        'css/tables.css'
    ];
    public $js = [
        'js/surveycreate.js',
        'js/surveyview.js',
        'js/resourcecreate.js',
        'js/acceptusers.js',
        'js/questionscreate.js',
        'js/badgescreate.js',
        'js/surveyoverview.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap4\BootstrapAsset',

    ];
}


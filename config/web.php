<?php
$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

use yii\filters\Cors;
use yii\rest\UrlRule;

$config = [
    'id' => 'products-api-yii2',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'components' => [
        'request' => [
            'cookieValidationKey' => 'changeme-please',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => false,
            'enableSession' => false,
            'loginUrl' => null,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => true,
            'rules' => [
                [
                    'class' => UrlRule::class,
                    'controller' => ['product'],
                    'pluralize' => true,
                    'prefix' => 'api',
                    'extraPatterns' => [
                        'GET search' => 'search',
                        'POST bulk' => 'bulk-create',
                        'PATCH {id}/adjust-inventory' => 'adjust-inventory',
                    ],
                ],
                // fallback home route if needed
                'GET /' => 'site/index',
            ],
        ],
        'response' => [
            'format' => yii\web\Response::FORMAT_JSON,
            'charset' => 'UTF-8',
        ],
    ],
    'params' => $params,
];

return $config;

<?php
namespace app\controllers;

use yii\web\Controller;

class SiteController extends Controller
{
    public $enableCsrfValidation = false;

    public function actionIndex()
    {
        return ['status' => 'ok', 'message' => 'Products API (Yii2) is running. See /api/products'];
    }

    public function actionError()
    {
        $exception = \Yii::$app->errorHandler->exception;
        return ['error' => $exception ? $exception->getMessage() : 'Unknown error'];
    }
}

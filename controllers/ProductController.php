<?php
namespace app\controllers;

use app\models\Product;
use yii\rest\ActiveController;
use yii\filters\Cors;
use yii\data\ActiveDataProvider;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use Yii;

class ProductController extends ActiveController
{
    public $modelClass = Product::class;

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        // CORS
        $behaviors['corsFilter'] = [
            'class' => Cors::class,
            'cors' => [
                'Origin' => ['*'],
                'Access-Control-Request-Method' => ['GET','POST','PUT','PATCH','DELETE','OPTIONS'],
                'Access-Control-Request-Headers' => ['*'],
                'Access-Control-Allow-Credentials' => false,
                'Access-Control-Max-Age' => 86400,
            ],
        ];

        return $behaviors;
    }

    // Customize index data provider to support filtering like ?search=&supplier_id=&min_price=&max_price=
    public function actions()
    {
        $actions = parent::actions();
        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
        return $actions;
    }

    public function prepareDataProvider()
    {
        $query = Product::find();
        $req = Yii::$app->request;

        if (($search = $req->get('search')) !== null && $search !== '') {
            $query->andFilterWhere(['like', 'name', $search]);
        }
        if (($supplierId = $req->get('supplier_id')) !== null && $supplierId !== '') {
            $query->andWhere(['supplier_id' => (int)$supplierId]);
        }
        if (($min = $req->get('min_price')) !== null && $min !== '') {
            $query->andWhere(['>=', 'selling_price', $min]);
        }
        if (($max = $req->get('max_price')) !== null && $max !== '') {
            $query->andWhere(['<=', 'selling_price', $max]);
        }

        return new ActiveDataProvider([
            'query' => $query->orderBy(['id' => SORT_DESC]),
            'pagination' => [
                'pageSize' => (int)$req->get('pageSize', 50),
            ],
        ]);
    }

    // GET /api/products/search?name=... (alias of index filters but explicit)
    public function actionSearch()
    {
        return $this->prepareDataProvider();
    }

    // POST /api/products/bulk  body: [{...}, {...}]
    public function actionBulkCreate()
    {
        $rows = Yii::$app->request->bodyParams;
        if (!is_array($rows) || empty($rows)) {
            throw new BadRequestHttpException('Provide an array of products');
        }

        $created = [];
        $errors = [];

        foreach ($rows as $i => $data) {
            $model = new Product();
            $model->load($data, '');
            if ($model->save()) {
                $created[] = $model;
            } else {
                $errors[] = ['index' => $i, 'errors' => $model->errors];
            }
        }
        return ['created' => $created, 'errors' => $errors];
    }

    // PATCH /api/products/{id}/adjust-inventory  body: { "delta": 5 }
    public function actionAdjustInventory($id)
    {
        $model = Product::findOne((int)$id);
        if (!$model) {
            throw new NotFoundHttpException('Product not found');
        }
        $body = Yii::$app->request->bodyParams;
        if (!isset($body['delta']) || !is_numeric($body['delta'])) {
            throw new BadRequestHttpException('Missing or invalid "delta"');
        }
        $model->inventory_count = (int)$model->inventory_count + (int)$body['delta'];
        if ($model->inventory_count < 0) {
            throw new BadRequestHttpException('Inventory cannot go below zero');
        }
        $model->save(false, ['inventory_count']);
        return $model;
    }
}

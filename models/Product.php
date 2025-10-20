<?php
namespace app\models;

use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property string $name
 * @property int $inventory_count
 * @property string $cost
 * @property string $selling_price
 * @property int|null $supplier_id
 */
class Product extends ActiveRecord
{
    public static function tableName()
    {
        return 'products';
    }

    public function rules()
    {
        return [
            [['name', 'inventory_count', 'cost', 'selling_price'], 'required'],
            [['inventory_count', 'supplier_id'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['cost', 'selling_price'], 'match', 'pattern' => '/^-?\d+(?:\.\d{1,2})?$/'],
        ];
    }

    public function fields()
    {
        return [
            'id', 'name', 'inventory_count', 'cost', 'selling_price', 'supplier_id'
        ];
    }
}

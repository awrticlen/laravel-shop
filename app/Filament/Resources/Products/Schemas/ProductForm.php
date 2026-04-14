<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Models\Product;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return CommonProductForm::configure($schema, productType: Product::TYPE_NORMAL);
    }
}

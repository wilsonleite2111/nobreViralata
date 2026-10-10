<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductVariant;
use App\Models\Setting;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $category = ProductCategory::query()
            ->where('slug', $request->string('categoria')->toString())
            ->first();

        $products = Product::query()
            ->active()
            ->when($category, fn ($query) => $query->whereBelongsTo($category, 'category'))
            ->with(['media', 'variants'])
            ->orderBy('name')
            ->simplePaginate(12)
            ->withQueryString();

        return view('products.index', [
            'products' => $products,
            'category' => $category,
            'categories' => ProductCategory::query()
                ->whereHas('products', fn ($query) => $query->active())
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function show(Product $product): View
    {
        abort_unless($product->is_active, 404);

        $whatsappNumber = Setting::read(Setting::STORE_WHATSAPP);
        $availableVariants = $product->variants->filter(fn (ProductVariant $variant): bool => $variant->isInStock());

        return view('products.show', [
            'product' => $product,
            'photos' => $product->getMedia(Product::PHOTOS),
            'canBePurchased' => $product->isInStock() && $product->purchaseUrl($whatsappNumber) !== null,
            'purchaseUrl' => $product->purchaseUrl($whatsappNumber),
            'variantPurchaseUrls' => $availableVariants->mapWithKeys(
                fn (ProductVariant $variant): array => [$variant->id => $product->purchaseUrl($whatsappNumber, $variant)],
            ),
            'selectedVariantId' => $availableVariants->first()?->id,
        ]);
    }
}

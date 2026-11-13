<?php

namespace App\Http\Controllers;

use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Models\Product;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Illuminate\Support\Facades\Gate;


class ProductController extends Controller
{
    protected function index(Request $request)
    {
        //filter is a scope function in the Product model
        $products = $request->user()->business->products()->with([
            'createdBy' => function ($query) {
                $query->withTrashed();
            }
        ])->filter($request->only(['search', 'stock', 'orderBy']))
            ->paginate(15)->appends($request->all());
        return Inertia::render('Authenticated/Inventory/index', [
            'products' => $products,
            'filter' => $request->only(['search', 'stock', 'orderBy']),
        ]);
    }

    protected function show(Request $request, Product $product)
    {
        Gate::authorize('show', $product);

        return response()->json($product);
    }

    protected function store(StoreProductRequest $request)
    {
        Gate::authorize('store',Product::class);
        $product = $request->validated();
        $product = new Product($product);
        $product->business_id = $request->user()->business_id;
        $product->createdBy_id = $request->user()->id;
        Gate::authorize('store', $product);

        if ($request->hasFile('imageFile')) {
            $product['img'] = $this->storeImg($request);
        }
        $product->save();

        return redirect()->back()->with('success', __('Successfully created'));
    }

    protected function destroy(Product $product)
    {
        Gate::authorize('destroy', $product);

        if (isset($product->img))
            $this->deleteImg($product->img);
        $product->delete();
        return redirect()->back()->with('success', __('Successfully deleted'));
    }

    protected function update(UpdateProductRequest $request, Product $product)
    {
        Gate::authorize('update', $product);
        $newProduct = $request->validated();
        // The image changes only when the request uploads a new file (imageFile) or
        // explicitly removes it (img: null). The "Update Stock" form sends only
        // `stock`: it keeps the image. An image name sent by the client is never
        // stored, so a request cannot point the image to (and later delete) another file.
        $removeImg = array_key_exists('img', $newProduct) && $newProduct['img'] === null;
        unset($newProduct['img'], $newProduct['imageFile']);
        if ($request->hasFile('imageFile')) {
            $newProduct['img'] = $this->storeImg($request);
        } else if ($removeImg) {
            $newProduct['img'] = null;
        }

        $oldImg = $product->img;
        $product->update($newProduct);

        // Delete the old file only after the new value is saved.
        if (isset($oldImg) && $oldImg !== $product->img) {
            $this->deleteImg($oldImg);
        }

        return redirect()->back()->with('success', __('Successfully updated'));
    }


    private function deleteImg(string $filePath)
    {
        if (config('filesystems.default') == 's3')
            return Storage::disk('products-images')->delete(basename($filePath));
        return Storage::disk('products-images')->delete($filePath);
    }

    /**Store an img file in the correspond filesystem and return the img path/name. path if it stored in the cloud and name if locally  */
    private function storeImg(Request $request): string
    {
        if (config('filesystems.default') == 's3')
            return $this->productsImagesStorage()->url($request->imageFile->store('', 'products-images'));
        return $request->imageFile->store('', 'products-images');
    }

    //to ignore an error of intelephense extension.
    private function productsImagesStorage(): FilesystemAdapter
    {
        return Storage::disk('products-images');
    }
}

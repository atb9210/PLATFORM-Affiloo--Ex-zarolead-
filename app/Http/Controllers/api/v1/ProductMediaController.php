<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use App\ProductMedia as ProductMedia;
use App\Http\Resources\api\v1\ProductMediaResource;
use App\TrafficSource as TrafficSource;

class ProductMediaController extends Controller
{
    /**
     * Get all product medias list.
     *
     * @urlParam filter_date_from string From date (Y-m-d format)
     * @urlParam filter_date_to string To date (Y-m-d format)
     * @urlParam filter_product int Product's id
     *
     * @response {
     *  "data": [product_medias],
     * }
     */
    public function index(Request $request)
    {
        // Param validation
        $params = $request->validate([
            'filter_date_from' => 'date|date_format:Y-m-d',
            'filter_date_to' => 'date|date_format:Y-m-d',
            'filter_product' => 'integer|exists:products,id',
        ]);

        $product_medias = ProductMedia::query();
        if(!Auth::user()->can('viewAny', 'App\ProductMedia')) {
            $product_medias = $product_medias->where('user_id', Auth::user()->id);
        }
        if (isset($params['filter_date_from'])) {
            $product_medias = $product_medias->where('created_at', '>=', $params['filter_date_from'] . " 00:00:00");
        }
        if (isset($params['filter_date_to'])) {
            $product_medias = $product_medias->where('created_at', '<=', $params['filter_date_to'] . " 23:59:59");
        }
        if (isset($params['filter_product'])) {
            $product_medias = $product_medias->where('product_id', $params['filter_product']);
        }

        return ProductMediaResource::collection($product_medias->get());
    }

    /**
     * Get product media's informations.
     *
     * @urlParam id int required ProductMedia's id
     *
     * @response {
     *  "data": product_media,
     * }
     */
    public function show($id)
    {
        $product_media = ProductMedia::findOrFail($id);
        $this->authorize('view', $product_media);
        return new ProductMediaResource($product_media);
    }

    /**
     * Create a new product media.
     *
     * @bodyParam product_id int required ProductMedia's form id
     * @bodyParam type string required ProductMedia's type (image, video)
     * @bodyParam file file required ProductMedia's image or video
     * @bodyParam file_preview file ProductMedia's preview
     *
     * @response 201 {
     *  "message": "Product media created",
     *  "data": ad,
     * }
     */
    public function store(Request $request)
    {
        $this->authorize('create', 'App\ProductMedia');

        // Params validation
        $params = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'type' => 'required|in:image,video',
            'file' => 'required|mimes:jpeg,jpg,png,bmp,gif,m3u8,ts,mp4,m4a,m4v,f4v,f4a,m4b,m4r,f4b,mov,3gp,3gp2,3g2,3gpp,3gpp2,ogg,oga,ogv,ogx,wmv,wma,webm,flv,avi',
            'file_preview' => 'mimes:jpeg,jpg,png,bmp,gif',
        ]);
        $params['user_id'] = Auth::user()->id;

        // File(s) check
        $file = '';
        if (!$request->hasFile('file')) {
            return response([
                'errors' => 'ProductMedia\'s file (image or video) not uploaded'
            ], 400);
        }
        $file = $request->file('file');
        $params['original_file_name'] = $file->getClientOriginalName();
        if ($request->hasFile('file_preview')) {
            $params['original_file_name_preview'] = $request->file('file_preview')->getClientOriginalName();
        }

        // Create ProductMedia
        $product_media = ProductMedia::create($params);

        // File(s) upload
        $path = Storage::disk('dropbox')->putFileAs($product_media->getStoragePath(), $file, $product_media->getFilename());
        $product_media->pathname = $path;
        if ($request->hasFile('file_preview')) {
            $file_preview = $request->file('file_preview');
            $path_preview = Storage::disk('dropbox')->putFileAs($product_media->getStoragePath(), $file_preview, $product_media->getFilename('preview'));
            $product_media->pathname_preview = $path_preview;
        }
        $product_media->save();

        return response([
            "message" => "Product media created",
            "data" => new ProductMediaResource($product_media),
        ], 201);
    }

    /**
     * Update the product media's fields.
     *
     * @urlParam id int required Product media's id
     *
     * @bodyParam product_id int ProductMedia's form id
     * @bodyParam type string ProductMedia's type (image, video)
     * @bodyParam file file ProductMedia's image or video
     * @bodyParam file_preview file ProductMedia's preview
     *
     * @response {
     *  "data": product_media,
     * }
     */
    public function update(Request $request, $id)
    {
        $product_media = ProductMedia::findOrFail($id);
        $this->authorize('update', $product_media);

        // Params validation
        $params = $request->validate([
            'product_id' => 'integer|exists:products,id',
            'type' => 'in:image,video',
            'file' => 'mimes:jpeg,jpg,png,bmp,gif,m3u8,ts,mp4,m4a,m4v,f4v,f4a,m4b,m4r,f4b,mov,3gp,3gp2,3g2,3gpp,3gpp2,ogg,oga,ogv,ogx,wmv,wma,webm,flv,avi',
            'file_preview' => 'mimes:jpeg,jpg,png,bmp,gif',
        ]);

        // File(s) check
        $file_uploaded = false;
        $file_preview_uploaded = false;
        $file = '';
        if ($request->hasFile('file')) {
            $file_uploaded = true;
        }
        if ($request->hasFile('file_preview')) {
            $file_preview_uploaded = true;
        }

        // Delete old file(s) and upload new ones
        if ($file_uploaded) {
            $file = $request->file('file');
            Storage::disk('dropbox')->delete($product_media->getStoragePathname());
            $path = Storage::disk('dropbox')->putFileAs($product_media->getStoragePath(), $file, $product_media->getFilename());
            $params['original_file_name'] = $file->getClientOriginalName();
            $params['pathname'] = $path;
        }
        if ($file_preview_uploaded) {
            $file_preview = $request->file('file_preview');
            Storage::disk('dropbox')->delete($product_media->getStoragePathname('preview'));
            $path_preview = Storage::disk('dropbox')->putFileAs($product_media->getStoragePath(), $file_preview, $product_media->getFilename('preview'));
            $params['original_file_name_preview'] = $request->file('file_preview')->getClientOriginalName();
            $params['pathname_preview'] = $path_preview;
        }
        $product_media->update($params);

        return response(new ProductMediaResource($product_media));
    }

    /**
     * Delete the specified product media.
     *
     * @urlParam id int required ProductMedia's id
     *
     * @response {
     *  "message": "Product media has been deleted",
     * }
     */
    public function destroy($id)
    {
        $product_media = ProductMedia::findOrFail($id);

        $this->authorize('delete', $product_media);

        // Delete the ad image/video (and preview)
        if ($product_media->pathname != null && $product_media->pathname != '') {
            Storage::disk('dropbox')->delete($product_media->pathname);
        }
        if ($product_media->pathname_preview != null && $product_media->pathname_preview != '') {
            Storage::disk('dropbox')->delete($product_media->pathname_preview);
        }

        // Delete the ad record
        $product_media->delete();

        return response([
            'message' => 'Product media has been deleted'
        ]);
    }
}

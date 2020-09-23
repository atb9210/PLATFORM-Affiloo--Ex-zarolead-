<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use App\Ad as Ad;
use App\Http\Resources\api\v1\AdResource;
use App\TrafficSource as TrafficSource;

class AdController extends Controller
{
    /**
     * Get all ads list.
     *
     * @urlParam filter_date_from string From date (Y-m-d format)
     * @urlParam filter_date_to string To date (Y-m-d format)
     * @urlParam filter_traffic_source int Traffic source's id
     * @urlParam filter_form int Form's id
     *
     * @response {
     *  "data": [ads],
     * }
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', 'App\Ad');

        // Param validation
        $params = $request->validate([
            'filter_date_from' => 'nullable|date|date_format:Y-m-d',
            'filter_date_to' => 'nullable|date|date_format:Y-m-d',
            'filter_traffic_source' => 'nullable|integer|exists:traffic_sources,id',
            'filter_form' => 'nullable|integer|exists:forms,id',
        ]);

        $ads = Ad::query();
        if (isset($params['filter_date_from'])) {
            $ads = $ads->where('created_at', '>=', $params['filter_date_from'] . " 00:00:00");
        }
        if (isset($params['filter_date_to'])) {
            $ads = $ads->where('created_at', '<=', $params['filter_date_to'] . " 23:59:59");
        }
        if (isset($params['filter_traffic_source'])) {
            $ads = $ads->where('traffic_source_id', $params['filter_traffic_source']);
        }
        if (isset($params['filter_form'])) {
            $ads = $ads->where('form_id', $params['filter_form']);
        }

        return AdResource::collection($ads->get());
    }

    /**
     * Get ad's informations.
     *
     * @urlParam id int required Ad's id
     *
     * @response {
     *  "data": ad,
     * }
     */
    public function show($id)
    {
        $ad = Ad::findOrFail($id);
        $this->authorize('view', $ad);
        return new AdResource($ad);
    }

    /**
     * Create a new ad.
     *
     * @bodyParam traffic_source_id int required Ad's traffic source id
     * @bodyParam form_id int required Ad's form id
     * @bodyParam ctr float required Ad's CTR
     * @bodyParam cpc float required Ad's CPC
     * @bodyParam cpa float required Ad's CPA
     * @bodyParam info json required Ad's info (title and text for example...)
     * @bodyParam type string required Ad's type (image, video)
     * @bodyParam file file required Ad's image or video
     * @bodyParam file_preview file Ad's preview
     *
     * @response 201 {
     *  "message": "Ad created",
     *  "data": ad,
     * }
     */
    public function store(Request $request)
    {
        $this->authorize('create', 'App\Ad');

        // Params validation
        $params = $request->validate([
            'traffic_source_id' => 'required|integer|exists:traffic_sources,id',
            'form_id' => 'required|integer|exists:forms,id',
            'ctr' => 'required|numeric|min:0',
            'cpc' => 'required|numeric|min:0',
            'cpa' => 'required|numeric|min:0',
            'info' => 'required|json',
            'type' => 'required|in:image,video',
            'file' => 'required|mimes:jpeg,jpg,png,bmp,gif,m3u8,ts,mp4,m4a,m4v,f4v,f4a,m4b,m4r,f4b,mov,3gp,3gp2,3g2,3gpp,3gpp2,ogg,oga,ogv,ogx,wmv,wma,webm,flv,avi',
            'file_preview' => 'mimes:jpeg,jpg,png,bmp,gif',
        ]);

        // Validate info JSON
        $params['info'] = json_decode($params['info'], TRUE);
        $traffic_source = TrafficSource::findOrFail($params['traffic_source_id']);
        $rules = $traffic_source->getAdModel('validation');
        $validator = Validator::make($params['info'], $rules);
        if ($validator->fails()) {
            return response([
                'message' => 'The given data - for the info JSON param - was invalid.',
                'errors' => $validator->messages(),
            ], 422);
        }

        // File(s) check
        $file = '';
        if (!$request->hasFile('file')) {
            return response([
                'errors' => 'Ad\'s file (image or video) not uploaded'
            ], 400);
        }
        $file = $request->file('file');
        $params['original_file_name'] = $file->getClientOriginalName();
        if ($request->hasFile('file_preview')) {
            $params['original_file_name_preview'] = $request->file('file_preview')->getClientOriginalName();
        }

        // Create Ad
        $ad = Ad::create($params);

        // File(s) upload
        $path = Storage::disk('dropbox')->putFileAs($ad->getStoragePath(), $file, $ad->getFilename());
        $ad->pathname = $path;
        if ($request->hasFile('file_preview')) {
            $file_preview = $request->file('file_preview');
            $path_preview = Storage::disk('dropbox')->putFileAs($ad->getStoragePath(), $file_preview, $ad->getFilename('preview'));
            $ad->pathname_preview = $path_preview;
        }
        $ad->save();

        return response([
            "message" => "Ad created",
            "data" => new AdResource($ad),
        ], 201);
    }

    /**
     * Update the ad's fields.
     *
     * @urlParam id int required Form's id
     *
     * @bodyParam form_id int Ad's form id
     * @bodyParam traffic_source_id int Ad's traffic source id
     * @bodyParam ctr float Ad's CTR
     * @bodyParam cpc float Ad's CPC
     * @bodyParam cpa float Ad's CPA
     * @bodyParam info json Ad's info (title and text for example...)
     * @bodyParam type string Ad's type (image, video)
     * @bodyParam file file Ad's image or video
     * @bodyParam file_preview file Ad's preview
     *
     * @response {
     *  "data": ad,
     * }
     */
    public function update(Request $request, $id)
    {
        $ad = Ad::findOrFail($id);
        $this->authorize('update', $ad);

        // Params validation
        $params = $request->validate([
            'traffic_source_id' => 'integer|exists:traffic_sources,id',
            'form_id' => 'integer|exists:forms,id',
            'ctr' => 'numeric|min:0',
            'cpc' => 'numeric|min:0',
            'cpa' => 'numeric|min:0',
            'info' => 'json',
            'type' => 'in:image,video',
            'file' => 'mimes:jpeg,jpg,png,bmp,gif,m3u8,ts,mp4,m4a,m4v,f4v,f4a,m4b,m4r,f4b,mov,3gp,3gp2,3g2,3gpp,3gpp2,ogg,oga,ogv,ogx,wmv,wma,webm,flv,avi',
            'file_preview' => 'mimes:jpeg,jpg,png,bmp,gif',
        ]);

        // Validate info JSON
        if (isset($params['info'])) {
            $params['info'] = json_decode($params['info'], TRUE);
            $traffic_source = TrafficSource::findOrFail($params['traffic_source_id']);
            $rules = $traffic_source->getAdModel('validation');
            $validator = Validator::make($params['info'], $rules);
            if ($validator->fails()) {
                return response([
                    'message' => 'The given data - for the info JSON param - was invalid.',
                    'errors' => $validator->messages(),
                ], 422);
            }
        }

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
            Storage::disk('dropbox')->delete($ad->getStoragePathname());
            $path = Storage::disk('dropbox')->putFileAs($ad->getStoragePath(), $file, $ad->getFilename());
            $params['original_file_name'] = $file->getClientOriginalName();
            $params['pathname'] = $path;
        }
        if ($file_preview_uploaded) {
            $file_preview = $request->file('file_preview');
            Storage::disk('dropbox')->delete($ad->getStoragePathname('preview'));
            $path_preview = Storage::disk('dropbox')->putFileAs($ad->getStoragePath(), $file_preview, $ad->getFilename('preview'));
            $params['original_file_name_preview'] = $request->file('file_preview')->getClientOriginalName();
            $params['pathname_preview'] = $path_preview;
        }
        $ad->update($params);

        return response(new AdResource($ad));
    }

    /**
     * Get ad's image/video.
     *
     * @urlParam id int required Ad's id
     *
     * @response {
     *  image/video,
     * }
     */
    public function getAdFile($id)
    {
        $ad = Ad::findOrFail($id);
		if(!Storage::disk('dropbox')->exists($ad->pathname)) {
            return response([
                'error' => 'File not found in storage!',
            ], 404);
        }
        $contents = Storage::disk('dropbox')->get($ad->pathname);
        return response($contents)
            ->header('Content-Type', Storage::disk('dropbox')->mimeType($ad->pathname));
    }

    /**
     * Get ad's preview image.
     *
     * @urlParam id int required Ad's id
     *
     * @response {
     *  image,
     * }
     */
    public function getAdFilePreview($id)
    {
        $ad = Ad::findOrFail($id);
        $pathname = '';
        if ($ad->type == 'image') {
            $pathname = $ad->pathname;
        } elseif ($ad->type == 'video' && $ad->pathname_preview != '') {
            $pathname = $ad->pathname_preview;
        }
		if($pathname == '' || !Storage::disk('dropbox')->exists($pathname)) {
            return response([
                'error' => 'File not found in storage!',
            ], 404);
        }
        $file = Storage::disk('dropbox')->get($pathname);
        return response($file)
            ->header('Content-Type', Storage::disk('dropbox')->mimeType($pathname));
    }
}

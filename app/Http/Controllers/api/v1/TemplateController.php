<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Template as Template;
use App\Http\Resources\api\v1\TemplateResource;

class TagController extends Controller
{
    /**
     * Get all tags list.
     *
     * @response {
     *  "data": [tags],
     * }
     */
    public function index()
    {
        $this->authorize('viewAny', 'App\Tag');

        return TagResource::collection(Tag::where('archived', 0)->get());
    }

    /**
     * Get tag's informations.
     *
     * @urlParam id int required Tag's id
     *
     * @response {
     *  "data": tag,
     * }
     */
    public function show($id)
    {
        $tag = Tag::findOrFail($id);
        $this->authorize('view', $tag);

        if ($tag->archived) {
            return response([
                "message" => "That tag is archived"
            ], 403);
        }

        return new TagResource($tag);
    }

    /**
     * Create a new tag.
     *
     * @bodyParam name string required Tag's name
     * @bodyParam bg_color string Background color
     * @bodyParam fg_color string Foreground color
     *
     * @response 201 {
     *  "message": "Tag created",
     *  "data": tag,
     * }
     */
    public function store(Request $request)
    {
        $this->authorize('create', 'App\Tag');

        // Params validation
        $params = $request->validate([
            'name' => 'required|string|min:3',
            'bg_color' => 'string',
            'fg_color' => 'string',
        ]);

        $data = [
            'name' => $params['name'],
            'info' => [
                'bg_color' => '#ffffff',
                'fg_color' => '#000000',
            ],
        ];

        if (isset($params['bg_color'])) {
            $data['info']['bg_color'] = $params['bg_color'];
        }
        if (isset($params['fg_color'])) {
            $data['info']['fg_color'] = $params['fg_color'];
        }

        $tag = Tag::create($data);

        return response([
            "message" => "Tag created",
            "data" => new TagResource($tag),
        ], 201);
    }

    /**
     * Update the tag's fields.
     *
     * @urlParam id int required Tag's id
     *
     * @bodyParam name string Tag's name
     * @bodyParam bg_color string Background color
     * @bodyParam fg_color string Foreground color
     *
     * @response {
     *  "data": tag,
     * }
     */
    public function update(Request $request, $id)
    {
        $tag = Tag::findOrFail($id);
        $this->authorize('update', $tag);

        if ($tag->archived) {
            return response([
                "message" => "That tag is archived"
            ], 403);
        }

        // Params validation
        $params = $request->validate([
            'name' => 'string|min:3',
            'bg_color' => 'string',
            'fg_color' => 'string',
        ]);

        $data = [
            'info' => $tag->info
        ];

        if (isset($params['name'])) {
            $data['name'] = $params['name'];
        }

        if (isset($params['bg_color'])) {
            $data['info']['bg_color'] = $params['bg_color'];
        }
        if (isset($params['fg_color'])) {
            $data['info']['fg_color'] = $params['fg_color'];
        }

        $tag->update($data);

        return new TagResource($tag);
    }

    /**
     * Delete the specified tag.
     *
     * @urlParam id int required Tag's id
     *
     * @response {
     *  "message": "Tag has been archived",
     * }
     */
    public function destroy($id)
    {
        $tag = Tag::findOrFail($id);

        $this->authorize('delete', $tag);

        // Archive the tag
        $tag->archived = 1;
        $tag->save();

        return response([
            'message' => 'Tag has been archived'
        ]);
    }
}

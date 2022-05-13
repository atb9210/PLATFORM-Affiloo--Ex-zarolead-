<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Template as Template;
use App\Http\Resources\api\v1\TemplateResource;

class TemplateController extends Controller
{
    /**
     * Get all templates list.
     *
     * @response {
     *  "data": [templates],
     * }
     */
    public function index()
    {
        $this->authorize('viewAny', 'App\Template');

        return TemplateResource::collection(Template::where('archived', 0)->get());
    }

    /**
     * Get template's informations.
     *
     * @urlParam id int required Template's id
     *
     * @response {
     *  "data": template,
     * }
     */
    public function show($id)
    {
        $template = Template::findOrFail($id);
        $this->authorize('view', $template);

        if ($template->archived) {
            return response([
                "message" => "That template is archived"
            ], 403);
        }

        return new TemplateResource($template);
    }

    /**
     * Create a new template.
     *
     * @bodyParam name string required Template's name
     * @bodyParam bg_color string Background color
     * @bodyParam fg_color string Foreground color
     *
     * @response 201 {
     *  "message": "Template created",
     *  "data": template,
     * }
     */
    public function store(Request $request)
    {
        $this->authorize('create', 'App\Template');

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

        $template = Template::create($data);

        return response([
            "message" => "Template created",
            "data" => new TemplateResource($template),
        ], 201);
    }

    /**
     * Update the template's fields.
     *
     * @urlParam id int required Template's id
     *
     * @bodyParam name string Template's name
     * @bodyParam bg_color string Background color
     * @bodyParam fg_color string Foreground color
     *
     * @response {
     *  "data": template,
     * }
     */
    public function update(Request $request, $id)
    {
        $template = Template::findOrFail($id);
        $this->authorize('update', $template);

        if ($template->archived) {
            return response([
                "message" => "That template is archived"
            ], 403);
        }

        // Params validation
        $params = $request->validate([
            'name' => 'string|min:3',
            'bg_color' => 'string',
            'fg_color' => 'string',
        ]);

        $data = [
            'info' => $template->info
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

        $template->update($data);

        return new TemplateResource($template);
    }

    /**
     * Delete the specified template.
     *
     * @urlParam id int required Template's id
     *
     * @response {
     *  "message": "Template has been archived",
     * }
     */
    public function destroy($id)
    {
        $template = Template::findOrFail($id);

        $this->authorize('delete', $template);

        // Archive the template
        $template->archived = 1;
        $template->save();

        return response([
            'message' => 'Template has been archived'
        ]);
    }

    public function getStrings($id)
    {
        $template = Template::findOrFail($id);

        $strings = [];

        $info = $template->info;
        if ($info !== null && isset($info['strings'])) {
            $strings = $info['strings'];
        }

        return response($strings);
    }
}

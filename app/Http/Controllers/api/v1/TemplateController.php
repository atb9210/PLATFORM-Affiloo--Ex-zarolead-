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
        // TODO: Authorization
        //$this->authorize('viewAny', 'App\Template');

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

        // TODO: Authorization
        //$this->authorize('view', $template);

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
     * @bodyParam info json Teamplate's info
     *
     * @response 201 {
     *  "message": "Template created",
     *  "data": template,
     * }
     */
    public function store(Request $request)
    {
        // TODO: Authorization
        //$this->authorize('create', 'App\Template');

        // Params validation
        $params = $request->validate([
            'name' => 'required|string|min:3',
            'info' => 'nullable|json',
        ]);

        if (isset($params['info'])) {
            $params['info'] = json_decode($params['info'], true);
        }

        $template = Template::create($params);

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
     * @bodyParam info json Teamplate's info
     *
     * @response {
     *  "data": template,
     * }
     */
    public function update(Request $request, $id)
    {
        $template = Template::findOrFail($id);

        // TODO: Authorization
        //$this->authorize('update', $template);

        if ($template->archived) {
            return response([
                "message" => "That template is archived"
            ], 403);
        }

        // Params validation
        $params = $request->validate([
            'name' => 'required|string|min:3',
            'info' => 'nullable|json',
        ]);

        if (isset($params['info'])) {
            $params['info'] = json_decode($params['info'], true);
        }

        $template->update($params);

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

        // TODO: Authorization
        //$this->authorize('delete', $template);

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

        // TODO: Authorization

        $strings = [];

        $info = $template->info;
        if ($info !== null && isset($info['strings'])) {
            $strings = $info['strings'];
        }

        return response($strings);
    }
}

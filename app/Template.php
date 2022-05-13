<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\Storage;

class Template extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'info', 'archived',
    ];

    protected $casts = [
        'info' => 'array',
    ];

    public function install(int $domainId, string $path): bool
    {
        $result = false;

        $domain = Domain::find($domainId);
        if ($domain === null) {
            return false;
        }

        $templatePath = 'templates/' . strval($this->id);

        $domain->uploadFileContents(
            $path . '/template.php',
            Storage::disk('public')->get($templatePath . '/template.php'));
        $domain->uploadFileContents(
            $path . '/index.php',
            Storage::disk('public')->get($templatePath . '/index.php'));

        $folders = [
            'fonts',
            'files',
            'webfonts',
        ];
        foreach ($folders as $folder) {
            $allFiles = Storage::disk('public')->allFiles($templatePath . '/' . $folder);
            foreach ($allFiles as $file) {
                $filePath = $file;
                $file = str_replace($templatePath . '/', '', $file);
                $domain->uploadFileContents(
                    $path . '/' . $file, // TODO: What about nested folders?
                    Storage::disk('public')->get($filePath));
            }
        }
        $result = true;

        return $result;
    }

}

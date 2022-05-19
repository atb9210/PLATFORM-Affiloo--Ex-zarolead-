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

    public function getPath(): string
    {
        return 'templates/' .  strval($this->id);
    }

    public function cloneRoot(): bool
    {
        $result = false;

        $srcPath = 'templates/root';
        $dstPath = $this->getPath();

        $allFiles = Storage::disk('public')->allFiles($srcPath);
        foreach ($allFiles as $file) {
            $dstFile = str_replace($srcPath, $dstPath, $file);
            Storage::disk('public')->copy($file, $dstFile);

            // Replace {id} with strval($this->id) in index.php
            $bname = basename($dstFile);
            if ($bname == 'index.php') {
                $contents = Storage::disk('public')->get($dstFile);
                $contents = str_replace('{id}', strval($this->id), $contents);
                Storage::disk('public')->put($dstFile, $contents);
            }
        }
        $result = true;

        return $result;
    }

    public function installImages($images): array
    {
        $newImages = [];

        $dstPath = $this->getPath() . '/files';
        foreach ($images as $image) {
            $name = basename($image);
            $contents = file_get_contents($image);
            $dstName = $dstPath . '/' . $name;
            Storage::disk('public')->put($dstName, $contents);
            $newImages[$image] = './files/' . $name;
        }

        return $newImages;
    }

    public function install(int $domainId, string $path): bool
    {
        $result = false;

        $domain = Domain::find($domainId);
        if ($domain === null) {
            return false;
        }

        $templatePath = $this->getPath();

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

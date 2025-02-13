<?php

namespace Kalexhaym\LaravelTelegramBot;

use Illuminate\Support\Facades\Storage;

abstract class File
{
    /**
     * @var string
     */
    protected string $name = 'file';

    /**
     * @var string
     */
    private string $path;

    /**
     * @var string
     */
    private string $disk;

    /**
     * @var string
     */
    private string $filename;

    /**
     * @var string
     */
    private string $contents;

    /**
     * @var array
     */
    private array $headers;

    /**
     * @param string $path
     * @param string $disk
     */
    public function __construct(string $path, string $disk = 'local')
    {
        $this->path = $path;
        $this->disk = $disk;
        $this->filename = basename(Storage::disk($this->disk)->path($this->path));
        $this->contents = Storage::disk($this->disk)->get($this->path);
        $this->headers = ['Content-Type' => Storage::disk($this->disk)->mimeType($this->path)];
    }

    /**
     * @return array
     */
    public function get(): array
    {
        return [
            'name'     => $this->name,
            'filename' => $this->filename,
            'contents' => $this->contents,
            'headers'  => $this->headers,
        ];
    }
}

<?php

namespace Kalexhaym\LaravelTelegramBot;

use Illuminate\Support\Facades\Storage;

abstract class File
{
    /**
     * @var string
     */
    private string $path;
    /**
     * @var string
     */
    private string $disk = 'local';

    /**
     * @var string
     */
    protected string $name;

    /**
     * @param $path
     */
    public function __construct($path)
    {
        $this->path = $path;
    }

    /**
     * @param string $disk
     *
     * @return $this
     */
    public function disk(string $disk): static
    {
        $this->disk = $disk;

        return $this;
    }

    /**
     * @return array
     */
    public function get(): array
    {
        return [
            'name'     => $this->name,
            'contents' => Storage::disk($this->disk)->get($this->path),
            'filename' => basename(Storage::disk($this->disk)->path($this->path)),
            'headers'  => ['Content-Type' => Storage::disk($this->disk)->mimeType($this->path)],
        ];
    }
}

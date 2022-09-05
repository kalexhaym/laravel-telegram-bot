<?php

namespace Kalexhaym\LaravelTelegramBot\Console;

use Illuminate\Console\Concerns\CreatesMatchingTest;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

class TextHandlerMakeCommand extends GeneratorCommand
{
    use CreatesMatchingTest;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:telegram-text-handler';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Telegram Text Handler class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'TelegramTextHandler';

    /**
     * Execute the console command.
     *
     * @throws FileNotFoundException
     */
    public function handle()
    {
        $name = $this->input->getArgument('name');

        $this->input->setArgument('name', 'Telegram/' . $name . 'Handler');

        if (parent::handle() === false) {
            return false;
        }
    }

    /**
     * Resolve the fully-qualified path to the stub.
     *
     * @param string $stub
     *
     * @return string
     */
    protected function resolveStubPath(string $stub): string
    {
        return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
            ? $customPath
            : __DIR__.$stub;
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub(): string
    {
        return $this->resolveStubPath('/stubs/text-handler.stub');
    }
}

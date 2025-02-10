<?php

namespace Kalexhaym\LaravelTelegramBot\Console;

use Illuminate\Console\Concerns\CreatesMatchingTest;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

class TelegramCommandMakeCommand extends GeneratorCommand
{
    use CreatesMatchingTest;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:telegram-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Telegram Command class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'TelegramCommand';

    /**
     * Execute the console command.
     *
     * @throws FileNotFoundException
     *
     * @return bool
     */
    public function handle(): bool
    {
        $name = $this->input->getArgument('name');

        $this->input->setArgument('name', 'Telegram/'.$name.'Command');

        if (parent::handle() === false) {
            return false;
        }

        return true;
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
        return $this->resolveStubPath('/stubs/telegram-command.stub');
    }
}

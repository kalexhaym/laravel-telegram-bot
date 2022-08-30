<?php

namespace Kalexhaym\LaravelTelegramBot\Console;

use Illuminate\Console\Concerns\CreatesMatchingTest;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

class TelegramCallbackMakeCommand extends GeneratorCommand
{
    use CreatesMatchingTest;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:telegram-callback';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Telegram Callback class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'TelegramCallback';

    /**
     * Execute the console command.
     *
     * @throws FileNotFoundException
     */
    public function handle()
    {
        $name = $this->input->getArgument('name');

        $this->input->setArgument('name', 'Telegram/' . $name . 'Callback');

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
        return $this->resolveStubPath('/stubs/telegram-callback.stub');
    }
}

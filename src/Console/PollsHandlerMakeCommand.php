<?php

namespace Kalexhaym\LaravelTelegramBot\Console;

use Illuminate\Console\Concerns\CreatesMatchingTest;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

class PollsHandlerMakeCommand extends GeneratorCommand
{
    use CreatesMatchingTest;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:telegram-polls-handler';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Telegram Polls Handler class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'TelegramPollsHandler';

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

        $this->input->setArgument('name', 'Telegram/'.$name.'Handler');

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
        return $this->resolveStubPath('/stubs/polls-handler.stub');
    }
}

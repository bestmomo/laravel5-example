<?php

namespace Illuminate\Foundation\Console;

use Illuminate\Support\Str;
use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;

class HandlerEventCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'handler:event';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new event handler class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Handler';

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildClass($name)
    {
        $stub = parent::buildClass($name);

        $event = $this->option('event');

        if (! Str::startsWith($event, $this->laravel->getNamespace())) {
            $event = $this->laravel->getNamespace().'Events\\'.$event;
        }

        $stub = str_replace(
            'DummyEvent', class_basename($event), $stub
        );

        $stub = str_replace(
            'DummyFullEvent', $event, $stub
        );

        return $stub;
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        if ($this->option('queued')) {
            return __DIR__.'/stubs/event-handler-queued.stub';
        } else {
            return __DIR__.'/stubs/event-handler.stub';
        }
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Handlers\Events';
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['event', null, InputOption::VALUE_REQUIRED, 'The event class the handler handles.'],

            ['queued', null, InputOption::VALUE_NONE, 'Indicates the event handler should be queued.'],
        ];
    }
}

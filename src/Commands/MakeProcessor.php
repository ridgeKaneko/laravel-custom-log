<?php

namespace CustomLog\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\View\View;

class MakeProcessor extends BaseMakeCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:logProcessor {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    const TEMPLATE_PATH = __DIR__ . '/templates/processor_template.blade.php';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    protected function configPrefix(): string
    {
        return config('customlog.commands.processor_dir');
    }

    protected function template(): View
    {
        $namespace = str_replace('/','\\',str_replace(app_path(),'App',$this->createFileDir));

        return $this->view(self::TEMPLATE_PATH,[
            'phpTag' => "<?php",
            'namespace' => $namespace,
            'class' => $this->createFileClass,
        ]);
    }
}

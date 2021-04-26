<?php

namespace CustomLog\Commands;

use CustomLog\Drivers\CsvDriver;
use CustomLog\Drivers\DayRotateDriver;
use CustomLog\Drivers\SingleDriver;
use Illuminate\Contracts\View\View;
use Illuminate\View\Factory;

class MakeDriver extends BaseMakeCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:logDriver {name} {--type=day}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    const TYPE_SINGLE = 'single';
    const TYPE_DAY = 'day';
    const TYPE_CSV = 'csv';

    const TYPE_CLASS_MAP = [
        self::TYPE_SINGLE => SingleDriver::class,
        self::TYPE_DAY => DayRotateDriver::class,
        self::TYPE_CSV => CsvDriver::class
    ];

    const TYPE_TEMPLATE_MAP = [
        self::TYPE_SINGLE => __DIR__ . '/templates/driver_template.blade.php',
        self::TYPE_DAY => __DIR__ . '/templates/driver_template.blade.php',
        self::TYPE_CSV => __DIR__ . '/templates/csv_driver_template.blade.php'
    ];

    protected function configPrefix() : string
    {
        return config('customlog.commands.driver_dir');
    }

    protected function template() : View
    {
        $type = $this->option('type');
        $use = self::TYPE_CLASS_MAP[$type];
        $templateName = self::TYPE_TEMPLATE_MAP[$type];
        $parentName = substr($use,strrpos($use,'\\') + 1);
        $namespace = str_replace('/','\\',str_replace(app_path(),'App',$this->createFileDir));

        return $this->view($templateName,[
            'phpTag' => "<?php",
            'namespace' => $namespace,
            'class' => $this->createFileClass,
            'parentName' => $parentName,
            'parentUse' => $use,
        ]);
    }
}

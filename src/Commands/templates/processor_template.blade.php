{!! $phpTag !!}


namespace {{ $namespace }};

use CustomLog\ProcessorRecordBag;
use CustomLog\Processors\MonologProcessor;

class {{ $class }} extends MonologProcessor
{
    /**
     * レコード登録処理
     *
     * @param ProcessorRecordBag $bag
     * @return mixed|void
     */
    public function process(ProcessorRecordBag $bag)
    {
        //例：keyをキーにvalueを登録
        //$recordBag->set('key','value');
    }
}

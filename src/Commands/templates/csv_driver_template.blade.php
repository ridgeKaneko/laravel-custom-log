{!! $phpTag !!}

namespace {{ $namespace }};

use CustomLog\Drivers\CsvDriver;
use CustomLog\ProcessorRecordBag;

class {{ $class }} extends CsvDriver
{
    /**
    * ログフォーマット定義
    *
    * csvのヘッダ名とレコードのキーをマップで紐づけることでフォーマットを定義
    *
    * 共通のレコードとして以下が使用可能です。
    * ・message : ログ本文
    * ・context : ログ詳細（ログ書き込みメソッドの第二引数がここに当たります）
    * ・channel : ログチャンネル名
    * ・level_name : ログレベル
    * ・datetime : 日付
    *
    * @var array
    */
    protected $record_map = [
        '本文' => 'message',
        '詳細' => 'context'
    ];

    /**
    * 日付フォーマット文字列定義
    *
    * 共通レコードであるdatetimeのフォーマットを定義
    *
    * @var string
    */
    protected $dateFormat = "Y-m-d";

    /**
    * プロセッサクラス名一覧配列
    *
    * 例：[MonologProcessor::class,TestProcessor::class]
    *
    * @var string[]
    */
    protected $processors = [];

    /**
    * ドライバ固有レコード登録処理
    *
    * ドライバ固有のレコード登録処理が定義可能です。
    * プロセッサクラスの実装が不要と判断した場合はこちらに登録処理を定義してください。
    *
    * @param ProcessorRecordBag $recordBag
    */
    protected function process(ProcessorRecordBag $recordBag)
    {
        //例：keyをキーにvalueを登録
        //$recordBag->set('key','value');
    }
}


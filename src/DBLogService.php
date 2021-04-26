<?php


namespace CustomLog;


use DateTime;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Database\Events\TransactionBeginning;
use Illuminate\Database\Events\TransactionCommitted;
use Illuminate\Database\Events\TransactionRolledBack;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Psr\Log\LoggerInterface;
use Throwable;

/**
 * データベースログ処理クラス
 *
 * @package CustomLog
 */
class DBLogService
{
    const TYPE_SELECT = 1;
    const TYPE_INSERT = 2;
    const TYPE_DELETE = 3;
    const TYPE_UPDATE = 4;
    const TYPE_EXPLAIN = 5;

    const TYPES_STR_MAP = [
        self::TYPE_SELECT => "select",
        self::TYPE_INSERT => "insert",
        self::TYPE_DELETE => "delete",
        self::TYPE_UPDATE => "update",
        self::TYPE_EXPLAIN => "explain"
    ];

    private $channel;
    private $targetQuery;
    private $explain;
    private $listenTrans;
    private $listened = false;


    public function __construct(array $config)
    {
        $this->channel = $config['channel'];

        foreach ($config['target'] as $queryStr => $isNeed)
        {
            if($isNeed){
                $this->targetQuery[] = array_flip(self::TYPES_STR_MAP)[$queryStr];
            }
        }

        $this->explain = $config['explain'];
        $this->listenTrans = $config['transaction'];
    }

    /**
     * dbログチャンネル　ロガー取得
     * @return LoggerInterface
     */
    protected function logger() : LoggerInterface
    {
        return Log::channel($this->channel);
    }

    /**
     * チャンネル変更
     * @param $channelName
     */
    public function setChannel($channelName)
    {
        $this->channel = $channelName;
    }

    /**
     * クエリ関連　ログ出力リスナー登録
     */
    public function listenQuery() : void
    {
        //リスナが登録されていない場合
        if(!$this->listened)
        {
            DB::listen(function ($query) {
                $this->logQuery($query);
            });

            //transactionのロギングを行う場合
            if ($this->listenTrans) {
                $this->listenTransaction();
            }

            $this->listened = true;
        }
    }

    /**
     * トランザクション関連　ログ出力リスナー登録
     */
    private function listenTransaction()
    {
        //トランザクション関連リスナー登録
        Event::listen(TransactionBeginning::class,function(){
            $this->logger()->info(null,["transaction" => "Begin"]);
        });

        Event::listen(TransactionCommitted::class,function(){
            $this->logger()->info(null,["transaction" => "Commit"]);
        });

        Event::listen(TransactionRolledBack::class,function(){
            $this->logger()->warning(null,["transaction" => "Rollback"]);
        });
    }

    /**
     * sql関係のエラーをログ出力する
     * @param Throwable $exception
     */
    public function logException(Throwable $exception)
    {
        $trace = $exception->getTrace();
        $context = ["message" => $exception->getMessage()];

        //コントローラーのエラーが起こった位置を取得
        $file = Arr::first($trace,function($t){
            return array_key_exists("file",$t) && strpos($t["file"],"Controllers/") !== false;
        });

        if(!is_null($file)){
            $context["file"] = $file["file"];
            $context["line"] = $file["line"];
        }

        //ログ出力
        $this->logger()->error(null,$context);
    }

    /**
     * sqlクエリをログ出力する
     * @param QueryExecuted $query
     */
    private function logQuery(QueryExecuted $query)
    {
        //ログ出力が必要なクエリの場合
        if($this->shouldLogging($query))
        {
            //文字列バインド
            $str = $this->queryBinding($query->sql, $query->bindings);

            //ログ出力
            $this->logger()->info(null, ["execute_query" => $str]);

            //explainが必要な場合
            if ($this->explain && $this->judgeType($query->sql) != self::TYPE_EXPLAIN) {
                DB::select('explain ' . $str);
            }
        }
    }

    /**
     * バインドした結果のクエリを返す
     * @param $query
     * @param $bindings
     * @return string|string[]|null
     */
    private function queryBinding($query,$bindings)
    {
        foreach ($bindings as $binding) {
            if (is_string($binding)) {
                $binding = "'{$binding}'";
            } elseif (is_bool($binding)) {
                $binding = $binding ? '1' : '0';
            } elseif ($binding === null) {
                $binding = 'NULL';
            } elseif ($binding instanceof Carbon) {
                $binding = "'{$binding->toDateTimeString()}'";
            } elseif ($binding instanceof DateTime) {
                $binding = "'{$binding->format('Y-m-d H:i:s')}'";
            }

            $query = preg_replace("/\?/", $binding, $query, 1);
        }

        return $query;
    }

    /**
     * ログ書き込みが必要なクエリかを判定
     *
     * @param $query
     * @return bool
     */
    private function shouldLogging(QueryExecuted $query)
    {
        $type = $this->judgeType($query->sql);

        return in_array($type,$this->targetQuery);
    }

    /**
     * クエリ種別を判定し返す
     * @param $query string
     * @return int
     */
    private function judgeType(string $query) : int
    {
        $type = 0;

        foreach (self::TYPES_STR_MAP as $key => $typeStr)
        {
            $upper = strtoupper($typeStr);
            $lower = strtolower($typeStr);
            if(strpos($query,$upper) === 0 || strpos($query,$lower) === 0){
                $type = $key;
            }
        }

        return $type;
    }
}

<?php


namespace CustomLog;

use Illuminate\Support\Arr;

class ProcessorRecordBag
{
    private $records;

    public function __construct(array $records)
    {
        $this->records = $records;
    }

    /**
     * recordを登録
     *
     * @param string|array $key
     * @param string|null $value
     * @param int|null $length 文字列固定長
     */
    public function set($key,$value = null,$length = null)
    {
        $keys = is_array($key) ? $key : [$key => $value];

        //固定長指定無
        if (is_null($length))
        {
            foreach ($keys as $key => $value) {
                Arr::set($this->records, $key, $value);
            }
        }
        //固定長指定有
        else
        {
            foreach ($keys as $key => $value) {
                Arr::set($this->records, $key, self::fixed($value,$length));
            }
        }
    }

    /**
     * レコード取得
     *
     * @param string $key
     * @param null $default
     * @return string|null
     */
    public function get(string $key,$default = null)
    {
        return $this->records[$key] ?? $default;
    }

    /**
     * 全レコード取得
     *
     * @return array
     */
    public function all()
    {
        return $this->records;
    }

    /**
     * レコード存在確認
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key)
    {
        return !is_null($this->get($key));
    }

    /**
     * 文字列を固定長に修正する
     *
     * @param $str
     * @param $length
     * @param string $omit
     * @param string $pad
     * @return string
     */
    protected static function fixed($str,$length,$omit = "…",$pad = "_")
    {
        return (strlen($str) < $length) ? str_pad($str,$length,$pad,STR_PAD_LEFT) : mb_strimwidth($str,0,$length,$omit);
    }
}

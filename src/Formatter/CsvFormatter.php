<?php


namespace CustomLog\Formatter;


use Monolog\Formatter\LineFormatter;

class CsvFormatter extends LineFormatter
{
    protected $charEncoding;

    public function __construct(array $format, ?string $dateFormat = null,$charEncoding = null)
    {
        $this->charEncoding = $charEncoding;

        //文字列フォーマット生成
        $format = '%'.implode('%,%',array_values($format)).'%';

        parent::__construct($format, $dateFormat, false,false);
    }

    /**
     * {@inheritdoc}
     */
    public function format(array $record): string
    {
        $vars = $this->normalize($record);

        $output = $this->format;

        foreach ($vars['extra'] as $var => $val) {
            if (false !== strpos($output, '%extra.'.$var.'%')) {
                $output = str_replace('%extra.'.$var.'%', $this->stringify($val), $output);
                unset($vars['extra'][$var]);
            }
        }

        if(isset($vars['context']) && is_array($vars['context']))
        {
            foreach ($vars['context'] as $var => $val) {
                if (false !== strpos($output, '%context.' . $var . '%')) {
                    $output = str_replace('%context.' . $var . '%', $this->stringify($val), $output);
                    unset($vars['context'][$var]);
                }
            }
        }

        if ($this->ignoreEmptyContextAndExtra) {
            if (empty($vars['context'])) {
                unset($vars['context']);
                $output = str_replace('%context%', '', $output);
            }

            if (empty($vars['extra'])) {
                unset($vars['extra']);
                $output = str_replace('%extra%', '', $output);
            }
        }

        foreach ($vars as $var => $val) {
            if (false !== strpos($output, '%'.$var.'%')) {
                $output = str_replace('%'.$var.'%', $this->stringify($val), $output);
            }
        }

        // remove leftover %extra.xxx% and %context.xxx% if any
        if (false !== strpos($output, '%')) {
            $output = preg_replace('/%(?:extra|context)\..+?%/', '', $output);
        }

        //文字コード指定の場合
        if (isset($this->charEncoding)){
            return mb_convert_encoding($output.PHP_EOL,$this->charEncoding);
        }

        return $output."\r\n";
    }
}

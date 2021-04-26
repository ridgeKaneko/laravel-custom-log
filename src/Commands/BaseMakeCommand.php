<?php


namespace CustomLog\Commands;


use Illuminate\Console\Command;
use Illuminate\Contracts\View\View;
use Illuminate\View\Factory;
use Throwable;

abstract class BaseMakeCommand extends Command
{
    //作成ファイルパス関連
    protected $createFilePath;
    protected $createFileDir;
    protected $createFileClass;

    /**
     * Execute the console command.
     *
     * @return void
     * @throws Throwable
     */
    public function handle()
    {
        //パス生成
        $this->initPath();

        //ファイル作成、ストリーム取得
        $stream = $this->stream();

        //ファイル内容書き込み
        fwrite($stream, $this->template()->render());

        fclose($stream);
    }

    /**
     * 作成ファイルのパスを生成
     */
    protected function initPath()
    {
        $name = $this->argument('name');
        $classOffset = strrpos($name,'/');
        $this->createFileClass = substr($name,$classOffset ? $classOffset + 1 : 0);
        $this->createFilePath = app_path($this->configPrefix()).$name.'.php';
        $this->createFileDir = substr($this->createFilePath,0,strrpos($this->createFilePath,'/'));
    }

    /**
     * ファイルを作成し、ストリームを取得
     *
     * @return false|resource
     */
    protected function stream()
    {
        //ディレクトリが未作成の場合
        if (!file_exists($this->createFileDir)) {
            mkdir($this->createFileDir, 0777, true);
        }

        return fopen($this->createFilePath, 'w+');
    }

    /**
     * フルパスからビューを構築
     *
     * @param $path
     * @param $params
     * @return mixed
     */
    protected function view($path,$params)
    {
        return app(Factory::class)->file($path,$params);
    }

    /**
     * 設定ファイルのディレクトリ指定文字列を取得
     *
     * @return string
     */
    protected abstract function configPrefix(): string;

    /**
     * テンプレートを生成
     *
     * @return View
     */
    protected abstract function template() : View;
}

# laravel-custom-log
##下準備
設定ファイル反映

`php artisan vendor:publish --tag=custom-log`

`php artisan config:cache`

##機能
###・ロギング拡張

手順

1. ログチャンネルドライバ作成

   `php artisan make:logDriver {ドライバクラス名}`
   
   フォーマット等ドライバ処理実装（ドライバphpdoc参照）
    
1. ログチャンネル設定追加
    
    設定ファイル  `config/logging.php`
    
    channelsに下記を追記
    ```
    {ログチャンネルキー} => [  
        'driver'    => 'custom',
        'name'      => {チャンネル表示名}
        'via'       => {ドライバクラス名},  
        'path'      => {ログファイル保存先パス},
        'level'     => {出力対象最低ログレベル},
        'days'      => {最大ログファイル数}
    ],
    ```
   補足  
   ログチャンネルキー：チャンネル指定時に利用するキー文字列  
   チャンネル表示名：ログ内で利用するチャンネル名  
   ドライバクラス名：手順1で作成したドライバのクラス名（TestDriver::class）  
   ログファイル保存先パス：ログファイルの保存先パス指定  
   出力対象最低ログレベル：ロギングを行うログレベルの絞り込み  
   最大ログファイル数：日付ごとのログファイルを何日分保持するか
   
   設定反映  `php artisan config:cache`
   
1. ロギング処理実装

    ログの書き込みには`CustomLog/Facades/Log`を使用してください。
    
    実際に書き込みを行うメソッドに関しては、laravelのLogファサードと同じく、ログレベルがメソッド名と対応している形になります。
    
    チャンネルの指定には`channel()`または`pinChannel()`が利用できます。
    
    channel()：指定チャンネルのロガーを返します。チャンネルの指定からロギングまでメソッドチェーンで行うことが出来ます。
    
    pinChannel()：ログチャンネルのピン止めを行うことができます。ピン止めを行うと、チャンネルの指定をせずにファサードから直接ロギングを行った場合のチャンネルを固定することができます。


###・リクエスト/レスポンス ロギング

手順

1. ミドルウェア設定追加

    設定ファイル `app/Http/Kernel.php`
    
    $routeMiddlewaresに下記を追記
    
    `'httpLog' => \CustomLog\Middleware\LogHttpTransaction::class`
    
1. ミドルウェア反映

    リクエスト/レスポンスのロギングを行いたい対象にミドルウェアを反映してください。  
    下記のようにミドルウェア指定の際にログチャンネルを指定することが出来ます。  
    (ログチャンネルの指定を行わなかった場合は、デフォルトチャンネルが使用されます）
    
    `httpLog:{ログチャンネルキー}`
    
    当ミドルウェアはチャンネルのピン止めも兼ねています。  
    ミドルウェアが反映されている処理内では、チャンネルが指定チャンネルにピン止めされます。
    
    
###・DBクエリログ    

手順

1. ミドルウェア設定追加

    設定ファイル `app/Http/Kernel.php`
    
    $routeMiddlewaresに下記を追記
    
    `'queryLog' => \CustomLog\Middleware\LogDBQuery::class`
    
1. ミドルウェア反映

    dbのクエリロギングを行いたい対象にミドルウェアを反映してください。  

1. ロギング詳細設定
    
    設定ファイルよりログ内容の調整が可能です。  
    `config/customlog.php`
    
    各項目詳細
    
    db.channel：ロギングの際に使用するログチャンネルキー  
    db.target：ロギング対象クエリ種別指定（true:対象 false:対象外）  
    db.explain：ロギングを行う際に実行計画（explain）も同時に実行しログに残すか  
    db.transaction：transactionに関するクエリのロギングを行うか

    
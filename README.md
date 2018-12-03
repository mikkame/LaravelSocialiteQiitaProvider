# LaravelSocialiteQiitaProvider について

このプラグインはLaravel Sociliate向けのQiitaでログインを実装するためのプラグインです。
Laravel5.5で動作を確認しています。
他のSociliateプラグインにテストがなかったのでどうやってテストすればいいか分からなかったのでノーテストです
取りあえず使えそうなので公開します。

# 使い方

基本的にSociliateと一緒です。

パッケージのインストール

```
composer require mikkame/laravel-socialite-qiita-provider
```


config/service.php に設定を追加します

```
'qiita' => [
        'client_id' => 'xxxxx',
        'client_secret' => 'xxxx',
        'redirect' => 'xxxxxx'
    ]
```



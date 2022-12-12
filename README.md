# line-notify
LineNotify を利用するための SDK

## Support versions
* PHP8.1

## Install

利用するプロジェクトの `composer.json` に以下を追加する。
```composer.json
"repositories": {
    "line-notify": {
        "type": "vcs",
        "url": "https://github.com/shimoning/line-notify.git"
    }
}
```

その後以下でインストールする。

```bash
composer require shimoning/line-notify
```

## How to use

### 初期化
```php
$lineNotify = new \Shimoning\LinNotify\LINENotify\LINENotify(
  $channelId,
  $channelSecret,
  $callbackUrl,
);
```

-----

### 認証トークンが既にあってメッセージを送信する場合
通常は後述の利用フローでユーザを認証してから利用する。

#### 基本
送信可能なメッセージは1000文字まで。

```php
$lineNotify->notify(
    $accessToken,
    new \Shimoning\LineNotify\ValueObject\Message('送信するメッセージ'),
);
```

#### スタンプを送信したい
第3引数に \Shimoning\LineNotify\Entity\Input\Sticker のインスタンスを渡す。

スタンプの番号については下記の[公式ページ](https://developers.line.biz/ja/docs/messaging-api/sticker-list/)を参照。

```php
$sticker = new \Shimoning\LineNotify\Entity\Input\Sticker(
    1988,   // パッケージ識別子
    446,    // Sticker識別子
);
$lineNotify->notify(
    $accessToken,
    new \Shimoning\LineNotify\ValueObject\Message('送信するメッセージ'),
    $sticker,
);
```

#### ネット上に公開されている画像を送信したい
第4引数に \Shimoning\LineNotify\Entity\Input\Image のインスタンスを渡す。

サムネイルとフルサイズの両方の画像URIが必要(同じでも問題はないが、許容サイズに違いがある)。
- サムネイル: 最大 240×240px
- フルサイズ: 最大 2048×2048px

対応画像は JPEG のみ (拡張子で簡易判定; 第2引数に false を入れると拡張子のチェックを回避できる)。

```php
$image = new \Shimoning\LineNotify\Entity\Input\Image(
    new \Shimoning\LineNotify\ValueObject\ImageUri('https://example.com/thumbnail.jpg'),
    new \Shimoning\LineNotify\ValueObject\ImageUri('https://example.com/fullsize.jpg'),
);
$lineNotify->notify(
    $accessToken,
    new \Shimoning\LineNotify\ValueObject\Message('送信するメッセージ'),
    null,
    $image,
);
```

#### サーバ上にある画像を送信したい (画像アップロード)
第4引数に \Shimoning\LineNotify\Entity\Input\Image のインスタンスを渡す。

対応画像は JPEG と PNG のみが利用可能 (拡張子で簡易判定; 第2引数に false を入れると拡張子のチェックを回避できる)。

```php
$image = new \Shimoning\LineNotify\Entity\Input\Image(
    null, // サムネイル用
    null, // フルサイズ用
    new \Shimoning\LineNotify\ValueObject\ImageFile('/dir/biz/image.png'),
);
$lineNotify->notify(
    $accessToken,
    new \Shimoning\LineNotify\ValueObject\Message('送信するメッセージ'),
    null,
    $image,
);
```

なお、画像アップロードとURIを同時に指定することができるが、画像アップロードが優先される。

#### メッセージは送信するが、PUSH通知はしたく無い場合
第5引数に true を入れる。

```php
$lineNotify->notify(
    $accessToken,
    new \Shimoning\LineNotify\ValueObject\Message('送信するメッセージ'),
    null,
    null,
    true,
);
```

#### 投稿順
メッセージ、スタンプ、画像を同時に指定すると、次の順番で投稿が実行される。
1. メッセージ
2. スタンプ
3. 画像

#### 静的呼び出し
メッセージの送信を `$lineNotify->notify()` と実行する例を紹介しているが、静的に呼び出すことも可能。

メッセージを送信するだけならインスタンスを作成する必要はないので、むしろこちらの方を推奨。

```php
\Shimoning\LineNotify\Communicator\Api::notify(
    $accessToken,
    new \Shimoning\LineNotify\ValueObject\Message('送信するメッセージ'),
);
```

-----

### 利用開始フロー

#### 1. ユーザーに認証許可を得るためのURLを取得する
Notify の利用を開始のための認証URLを取得する。

```php
$uriToRedirect = $lineNotify->generateAuthUri(
    $state, // CSRF 対策のトークンなどを入れる
); // https://notify-bot.line.me/oauth/authorize?response_type=code&client_id=...
```

*state について*

ユーザが認証してシステムに帰ってきた時に、`code` と一緒に `state` が戻ってくるので、一致するか確認することが推奨される。

> CSRF 攻撃に対応するための任意のトークンを指定します
> 典型的にはユーザのセッションIDから生成されるハッシュ値などを指定し、redirect_uri アクセス時に state パラメータを検証することでCSRF攻撃を防ぎます。
>
> LINE Notify ではウェブアプリケーション連携を想定しているため state パラメータを必須とさせて頂いています。
>
> -- 引用元: <cite>[公式](https://notify-bot.line.me/doc/ja/)</cite>


*response_mode について*

第2引数に `response_mode` を指定できる。
引き受ける値は `form_post` のみで、これを指定すると、通常のリダイレクト(GET)ではなく `POST` で送信される。

> これはレスポンスの code パラメータが特定環境で漏洩することを防ぐためであるため、指定することをお勧めします
>
> -- 引用元: <cite>[公式](https://notify-bot.line.me/doc/ja/)</cite>
> -- 関連: <cite>[関連記事](http://arstechnica.com/security/2016/07/new-attack-that-cripples-https-crypto-works-on-macs-windows-and-linux/)</cite>

#### 2. 上記URLをユーザに踏んでもらい、リダイレクトもしくはポストで値が返ってきた時

```php
// 配列の場合 (GET)
$queryData = filter_input_array(INPUT_GET, $_GET); // Pure PHP
$queryData = $request->query(); // Laravel
$resultOrError = $lineNotify->parseAuthResult($queryData);

// 配列の場合 (POST)
$formData = filter_input_array(INPUT_POST, $_POST); // Pure PHP
$formData = $request->input(); // Laravel
$resultOrError = $lineNotify->parseAuthResult($formData);

// クエリストリングの場合
$queryString = 'code=ABCD...&state=1234567890...';
$resultOrError = $lineNotify->parseAuthResult($queryString);

// JSONの場合
$queryJSON = '{"code":"ABCD...","state":"1234567890..."}';
$resultOrError = $lineNotify->parseAuthResult($queryJSON);

// 判定
if ($resultOrError->isSucceeded()) {
    // 成功時
    $state = $resultOrError->getState();    // 送信した値と一致するかチェックすること
    $code = $resultOrError->getCode();
    // next step
    ...
} else {
    // 失敗時
    $error = $resultOrError->getError();
    $errorDescription = $resultOrError->getErrorDescription();
}
```

#### 3. コードをアクセストークンに変換する
ここで取得された access_token でメッセージ送信等を行う。

```php
$accessTokenOrNull = $lineNotify->exchangeCode4AccessToken($code);    // Abc1234...
if ($accessTokenOrNull === null) {
    // something wrong
    ...
}
```

-----

### その他

#### 連携状態を確認する

```php
$statusOrNull = $lineNotify->status($accessToken); // インスタンスを利用する
$statusOrNull = \Shimoning\LineNotify\Communicator\Api::status($accessToken); // 静的呼び出し

if ($statusOrNull) {
    $targetType = $status->getTargetTypeValue();    // GROUP or USER
    $target = $status->getTarget(); // GROUP の場合はグループ名, USER の場合はユーザ名
} else {
    // 未連携 or アクセストークンが無効
}
```

#### 連携を解除する (アクセストークンを失効させる)
```php
$result = $lineNotify->revokeAccessToken($accessToken); // インスタンスを利用する
$result = \Shimoning\LineNotify\Communicator\Api::revokeAccessToken($accessToken); // 静的呼び出し

if ($result) {
    // 解除に成功 or 既に失効している
} else {
    // 何らかのエラー
}
```

#### 一斉送信したい！
サポート予定。
現状はループを回す。

```php
$message = new \Shimoning\LineNotify\ValueObject\Message('送信するメッセージ');

$accessTokens = [];
foreach ($accessTokens as $accessToken) {
    $lineNotify->notify(
        $accessToken,
        $message,
    );
}
```


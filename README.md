# Laravel News (PHP ローカル編)

## PHP File

`index.php` ... 複数の記事表示 & 記事作成
`article.php` ... 投稿記事ごとの php(パラメータを使用しての動的)

## data

`data.txt` ... 記事のデータ

```
[id, date, title, text]

id ... uniqueId(一意のもの)
date ... 投稿時刻(create_time)
title ... string 記事のタイトル
text ... contentの内容
```

`comment.txt` ... コメントのデータ

```
[id, articleId, text]

id ... uniqueId(一意のもの)
articleId ... どこの記事に投稿されているかを判別
text ... コメントの内容
```

## その他ファイル

`css/` ... css の設定ファイル

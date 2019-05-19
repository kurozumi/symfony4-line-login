# Symfony4でLineログインを実装

Symfony4でLineログインを実装する方法です。

## 使用しているパッケージ

Lineログインを実装するため、以下のパッケージを使用しています

```
knpuniversity/oauth2-client-bundle 
osapon/oauth2-line
```

emailがJWTでエンコードされているので以下のパッケージでデコードしています。

```
firebase/php-jwt
```

## LINE_CLIENT_IDとLINE_CLIENT_SECRETを設定

.envに取得したLINE_CLIENT_IDとLINE_CLIENT_SECRETを設定してください。
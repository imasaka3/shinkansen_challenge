# shinkansen_challenge
新幹線の帰りにcopilot only で作成するチャレンジ

## 構成

- `server/` - PHP REST API サーバー
- `local_env/` - Docker 開発環境

## 使用方法

### Docker環境での起動
```bash
cd local_env
docker compose up -d
```

### アクセス先
- API サーバー: http://localhost:8080
- phpMyAdmin: http://localhost:8081
- MySQL: localhost:3306

### APIエンドポイント
- `GET /` - ウェルカムメッセージ
- `GET /health` - ヘルスチェック
- `GET /api/trains` - 新幹線データ一覧

### PHPサーバー単体での起動
```bash
cd server
composer start
```

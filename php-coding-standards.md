# PHP コーディング規約

## 概要

このドキュメントは、サーバーサイドPHP開発におけるモダンなコーディング規約を定義します。
PHP 8.0以上を対象とし、PSR（PHP Standard Recommendation）に準拠した現代的な開発手法を採用します。

## 目次

1. [基本方針](#基本方針)
2. [PSR準拠](#psr準拠)
3. [ファイル構成](#ファイル構成)
4. [命名規則](#命名規則)
5. [型宣言とプロパティ](#型宣言とプロパティ)
6. [クラス設計](#クラス設計)
7. [セキュリティ](#セキュリティ)
8. [パフォーマンス](#パフォーマンス)
9. [テスト](#テスト)
10. [ドキュメント](#ドキュメント)

## 基本方針

### 可読性の重視
- コードは人間が読むために書く
- 自己説明的なコードを心がける
- 複雑な処理には適切なコメントを追加

### モダンPHPの活用
- PHP 8.0以上の機能を積極的に使用
- 型宣言を必須とする
- 非推奨機能の使用を禁止

### セキュリティファースト
- 入力値の検証を徹底
- SQLインジェクション対策を必須
- XSS対策を実装

## PSR準拠

### PSR-1: Basic Coding Standard
- PHPタグは `<?php` のみ使用
- ファイルエンコーディングはUTF-8（BOMなし）
- 1ファイルにつき1つのクラス定義

### PSR-4: Autoloading Standard
- 名前空間とディレクトリ構造の対応
- Composerオートローダーの使用

### PSR-12: Extended Coding Style
- インデントは4スペース
- 行末の不要な空白は削除
- 1行の長さは120文字以内を推奨

```php
<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\UserRepositoryInterface;
use App\Entity\User;

final class UserService
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository
    ) {
    }

    public function createUser(string $email, string $name): User
    {
        // 実装
    }
}
```

## ファイル構成

### ディレクトリ構造
```
src/
├── Controller/          # コントローラー
├── Service/            # ビジネスロジック
├── Repository/         # データアクセス層
├── Entity/             # エンティティ
├── ValueObject/        # 値オブジェクト
├── Exception/          # 例外クラス
└── Infrastructure/     # インフラ層
```

### ファイル命名
- クラス名とファイル名は一致させる
- PascalCaseを使用
- インターフェースには `Interface` サフィックス
- 抽象クラスには `Abstract` プレフィックス

## 命名規則

### クラス名
```php
// ✅ 良い例
class UserService {}
interface UserRepositoryInterface {}
abstract class AbstractController {}

// ❌ 悪い例
class userservice {}
class user_service {}
```

### メソッド名・プロパティ名
```php
// ✅ 良い例
private string $userName;
public function getUserById(int $id): ?User {}

// ❌ 悪い例
private string $user_name;
public function get_user_by_id($id) {}
```

### 定数
```php
// ✅ 良い例
public const MAX_RETRY_COUNT = 3;
public const DEFAULT_TIMEOUT_SECONDS = 30;

// ❌ 悪い例
public const maxRetryCount = 3;
public const default_timeout = 30;
```

## 型宣言とプロパティ

### 厳密な型宣言
```php
<?php

declare(strict_types=1);

// ✅ 必須
class UserService
{
    public function processUser(int $userId, string $action): bool
    {
        // 実装
    }
}
```

### プロパティの型宣言
```php
// ✅ PHP 8.0+ プロパティプロモーション
class User
{
    public function __construct(
        private readonly int $id,
        private string $name,
        private string $email,
    ) {
    }
}

// ✅ 従来の書き方（必要な場合）
class User
{
    private int $id;
    private string $name;
    private string $email;

    public function __construct(int $id, string $name, string $email)
    {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
    }
}
```

### Nullableと Union Types
```php
// ✅ PHP 8.0+ Union Types
function processValue(int|string $value): bool
{
    // 実装
}

// ✅ Nullable
function findUser(int $id): ?User
{
    // 実装
}
```

## クラス設計

### 単一責任原則
```php
// ✅ 良い例：単一責任
class EmailSender
{
    public function send(string $to, string $subject, string $body): void
    {
        // メール送信のみに特化
    }
}

class UserValidator
{
    public function validate(array $userData): bool
    {
        // バリデーションのみに特化
    }
}
```

### 依存性注入
```php
// ✅ 良い例：コンストラクタインジェクション
class UserService
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly EmailSenderInterface $emailSender,
        private readonly LoggerInterface $logger,
    ) {
    }
}
```

### readonly プロパティの活用
```php
// ✅ PHP 8.1+ readonly
class UserData
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $email,
    ) {
    }
}
```

## セキュリティ

### 入力値検証
```php
// ✅ 必須：すべての入力値を検証
class UserController
{
    public function createUser(array $input): User
    {
        $validator = new UserValidator();
        if (!$validator->validate($input)) {
            throw new ValidationException('Invalid input data');
        }

        // 処理続行
    }
}
```

### SQLインジェクション対策
```php
// ✅ 良い例：プリペアドステートメント
class UserRepository
{
    public function findById(int $id): ?User
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE id = :id');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $this->mapToUser($stmt->fetch());
    }
}

// ❌ 悪い例：生のクエリ
public function findById(int $id): ?User
{
    $query = "SELECT * FROM users WHERE id = {$id}";
    return $this->pdo->query($query);
}
```

### XSS対策
```php
// ✅ 良い例：エスケープ処理
function renderUserName(string $name): string
{
    return htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
}
```

### パスワード処理
```php
// ✅ 良い例：password_hash使用
class PasswordService
{
    public function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_ARGON2ID);
    }

    public function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
}
```

## パフォーマンス

### 遅延読み込み
```php
// ✅ 必要時のみデータ取得
class UserService
{
    public function getUser(int $id): User
    {
        return $this->userRepository->findById($id) 
            ?? throw new UserNotFoundException();
    }
}
```

### キャッシュ戦略
```php
// ✅ キャッシュ機能の実装
class CachedUserService
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly CacheInterface $cache,
    ) {
    }

    public function getUser(int $id): User
    {
        $cacheKey = "user:{$id}";
        
        return $this->cache->get($cacheKey) 
            ?? $this->cache->set($cacheKey, $this->userRepository->findById($id));
    }
}
```

## テスト

### ユニットテスト
```php
// ✅ PHPUnit使用
class UserServiceTest extends TestCase
{
    private UserService $userService;
    private MockObject $userRepository;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->userService = new UserService($this->userRepository);
    }

    public function testCreateUserSuccess(): void
    {
        // Arrange
        $email = 'test@example.com';
        $name = 'Test User';

        // Act
        $user = $this->userService->createUser($email, $name);

        // Assert
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($email, $user->getEmail());
    }
}
```

### テストカバレッジ
- 最低80%のコードカバレッジを維持
- 重要なビジネスロジックは100%カバー
- エラーケースのテストも必須

## ドキュメント

### PHPDoc
```php
/**
 * ユーザーサービス
 * 
 * ユーザーの作成、更新、削除等の
 * ビジネスロジックを担当する
 */
class UserService
{
    /**
     * ユーザーを作成する
     *
     * @param string $email ユーザーのメールアドレス
     * @param string $name ユーザー名
     * @return User 作成されたユーザー
     * @throws ValidationException バリデーションエラー時
     */
    public function createUser(string $email, string $name): User
    {
        // 実装
    }
}
```

### README.mdの整備
- プロジェクトの概要
- セットアップ手順
- 使用方法
- コーディング規約への言及

## 禁止事項

### 使用禁止機能
- `eval()` 関数
- `extract()` 関数（特別な理由がない限り）
- グローバル変数の使用
- `goto` 文
- 短縮PHPタグ（`<?` `<?=`）

### 非推奨パターン
```php
// ❌ 静的呼び出しの多用
class BadService
{
    public static function process(): void
    {
        // 避けるべき
    }
}

// ❌ 神クラス（多すぎる責任）
class GodClass
{
    public function doEverything(): void
    {
        // 避けるべき
    }
}
```

## ツール設定

### Composer設定例
```json
{
    "require": {
        "php": ">=8.0"
    },
    "require-dev": {
        "phpunit/phpunit": "^10.0",
        "phpstan/phpstan": "^1.0",
        "squizlabs/php_codesniffer": "^3.0"
    },
    "autoload": {
        "psr-4": {
            "App\\": "src/"
        }
    }
}
```

### PHPStan設定
```neon
# phpstan.neon
parameters:
    level: max
    paths:
        - src
    checkMissingIterableValueType: false
```

### PHP CS Fixer設定
```php
// .php-cs-fixer.php
<?php

return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR12' => true,
        'declare_strict_types' => true,
        'array_syntax' => ['syntax' => 'short'],
    ])
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->in(__DIR__ . '/src')
    );
```

## まとめ

このコーディング規約は、モダンなPHP開発における最新のベストプラクティスに基づいています。
チーム全体でこれらの規約を遵守することで、保守性が高く、セキュアで、パフォーマンスに優れたPHPアプリケーションを開発できます。

規約は定期的に見直し、PHPの新機能やコミュニティの動向に合わせて更新していくことが重要です。
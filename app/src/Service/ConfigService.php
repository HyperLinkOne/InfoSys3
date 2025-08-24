<?php

declare(strict_types=1);

namespace App\Service;

class ConfigService
{
    private array $config;

    public function __construct()
    {
        $this->config = [
            'app' => [
                'env' => $_ENV['APP_ENV'] ?? 'dev',
                'name' => $_ENV['APP_NAME'] ?? 'InfoSys3',
                'debug' => filter_var($_ENV['APP_DEBUG'] ?? 'true', FILTER_VALIDATE_BOOLEAN),
            ],
            'database' => [
                'host' => $_ENV['DB_HOST'] ?? 'mariadb',
                'port' => (int)($_ENV['DB_PORT'] ?? 3306),
                'name' => $_ENV['DB_NAME'] ?? 'infosys3',
                'user' => $_ENV['DB_USER'] ?? 'dbuser',
                'pass' => $_ENV['DB_PASS'] ?? 'Alcohol99%',
            ],
            'security' => [
                'csrf_token_lifetime' => (int)($_ENV['CSRF_TOKEN_LIFETIME'] ?? 3600),
            ],
            'logging' => [
                'level' => $_ENV['LOG_LEVEL'] ?? 'debug',
            ],
            'mail' => [
                'host' => $_ENV['MAIL_HOST'] ?? 'smtp.gmail.com',
                'port' => (int)($_ENV['MAIL_PORT'] ?? 587),
                'username' => $_ENV['MAIL_USERNAME'] ?? '',
                'password' => $_ENV['MAIL_PASSWORD'] ?? '',
                'encryption' => $_ENV['MAIL_ENCRYPTION'] ?? 'tls',
            ],
            'cache' => [
                'driver' => $_ENV['CACHE_DRIVER'] ?? 'file',
                'ttl' => (int)($_ENV['CACHE_TTL'] ?? 3600),
            ],
            'session' => [
                'lifetime' => (int)($_ENV['SESSION_LIFETIME'] ?? 120),
                'secure' => filter_var($_ENV['SESSION_SECURE'] ?? 'false', FILTER_VALIDATE_BOOLEAN),
            ],
        ];
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $keys = explode('.', $key);
        $value = $this->config;

        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return $default;
            }
            $value = $value[$k];
        }

        return $value;
    }

    public function isDevelopment(): bool
    {
        return $this->get('app.env') === 'dev';
    }

    public function isProduction(): bool
    {
        return $this->get('app.env') === 'prod';
    }

    public function isDebug(): bool
    {
        return $this->get('app.debug', false);
    }

    public function getDatabaseConfig(): array
    {
        return $this->get('database', []);
    }

    public function getMailConfig(): array
    {
        return $this->get('mail', []);
    }
}

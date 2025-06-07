<?php
namespace Config;

use Dotenv\Dotenv;

class EnvLoader
{
    public static function load(string $basePath): void
    {
        $appEnv = $_SERVER['APP_ENV'] ?? $_ENV['APP_ENV'] ?? 'local';

        $envFile = match ($appEnv) {
            'production' => '.env.production',
            'staging' => '.env.staging',
            default => '.env',
        };

        $envPath = "$basePath/$envFile";

        if (file_exists($envPath)) {
            $dotenv = Dotenv::createImmutable($basePath, $envFile);
            $dotenv->load();

            // Debug: remove in production
            foreach (['SMTP_FROM', 'QUOTE_EMAIL'] as $key) {
                if (!isset($_ENV[$key])) {
                    error_log("Missing .env variable: $key");
                }
            }
        } else {
            throw new \RuntimeException("Environment file $envFile not found at $envPath.");
        }
    }

}

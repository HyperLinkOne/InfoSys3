<?php

declare(strict_types=1);

/**
 * Setup script for InfoSys3 PHP Framework
 * Run this script to configure your environment
 */

echo "🚀 InfoSys3 PHP Framework Setup\n";
echo "================================\n\n";

// Check if .env file exists
$envFile = __DIR__ . '/.env';
$envExampleFile = __DIR__ . '/env.example';

if (!file_exists($envFile)) {
    if (file_exists($envExampleFile)) {
        echo "📋 Creating .env file from env.example...\n";
        copy($envExampleFile, $envFile);
        echo "✅ .env file created successfully!\n\n";
    } else {
        echo "❌ env.example file not found. Please create it first.\n";
        exit(1);
    }
} else {
    echo "✅ .env file already exists.\n\n";
}

// Check if var directory exists
$varDir = __DIR__ . '/var';
if (!is_dir($varDir)) {
    echo "📁 Creating var directory...\n";
    mkdir($varDir, 0755, true);
    echo "✅ var directory created.\n\n";
} else {
    echo "✅ var directory exists.\n\n";
}

// Check if cache directory exists
$cacheDir = $varDir . '/cache';
if (!is_dir($cacheDir)) {
    echo "📁 Creating cache directory...\n";
    mkdir($cacheDir, 0755, true);
    echo "✅ cache directory created.\n\n";
} else {
    echo "✅ cache directory exists.\n\n";
}

// Check if twig cache directory exists
$twigCacheDir = $cacheDir . '/twig';
if (!is_dir($twigCacheDir)) {
    echo "📁 Creating Twig cache directory...\n";
    mkdir($twigCacheDir, 0755, true);
    echo "✅ Twig cache directory created.\n\n";
} else {
    echo "✅ Twig cache directory exists.\n\n";
}

// Check if composer is installed
if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
    echo "📦 Composer dependencies not installed.\n";
    echo "   Please run: composer install\n\n";
} else {
    echo "✅ Composer dependencies installed.\n\n";
}

// Check if database migration exists
$migrationFile = __DIR__ . '/database/migrations/001_create_blog_posts_table.sql';
if (file_exists($migrationFile)) {
    echo "✅ Database migration file exists.\n";
    echo "   Run the migration with: mysql -h localhost -P 3306 -u dbuser -p infosys3 < database/migrations/001_create_blog_posts_table.sql\n\n";
} else {
    echo "❌ Database migration file not found.\n\n";
}

echo "🎉 Setup completed!\n\n";
echo "Next steps:\n";
echo "1. Edit .env file with your configuration\n";
echo "2. Run: composer install\n";
echo "3. Run: docker-compose up -d\n";
echo "4. Run the database migration\n";
echo "5. Visit: http://localhost:8080\n\n";

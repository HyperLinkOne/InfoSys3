<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use App\Service\ConfigService;
use App\Service\DatabaseService;
use App\Repository\BlogPostRepository;

echo "🔍 Database Debug Script\n";
echo "=======================\n\n";

try {
    // Load environment
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    
    echo "✅ Environment loaded\n";
    
    // Create config service
    $config = new ConfigService();
    echo "✅ Config service created\n";
    
    // Test database connection
    $dbService = new DatabaseService($config);
    echo "✅ Database service created\n";
    
    $pdo = $dbService->getConnection();
    echo "✅ Database connection established\n";
    
    // Check if table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'blog_posts'");
    $tableExists = $stmt->rowCount() > 0;
    
    if ($tableExists) {
        echo "✅ blog_posts table exists\n";
        
        // Check table structure
        $stmt = $pdo->query("DESCRIBE blog_posts");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "📋 Table structure:\n";
        foreach ($columns as $column) {
            echo "  - {$column['Field']}: {$column['Type']}\n";
        }
        
        // Count records
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM blog_posts WHERE deleted_at IS NULL");
        $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        echo "📊 Active blog posts: {$count}\n";
        
        if ($count > 0) {
            // Show sample data
            $stmt = $pdo->query("SELECT * FROM blog_posts WHERE deleted_at IS NULL ORDER BY created_at DESC LIMIT 3");
            $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo "📝 Sample posts:\n";
            foreach ($posts as $post) {
                echo "  - ID: {$post['id']}, Title: {$post['title']}, Created: {$post['created_at']}\n";
            }
        }
        
        // Test repository
        $repository = new BlogPostRepository($dbService);
        echo "✅ Repository created\n";
        
        $allPosts = $repository->findAll();
        echo "📝 Repository findAll() returned " . count($allPosts) . " posts\n";
        
        if (!empty($allPosts)) {
            foreach ($allPosts as $post) {
                echo "  - ID: {$post->getId()}, Title: {$post->getTitle()}\n";
            }
        }
        
    } else {
        echo "❌ blog_posts table does not exist\n";
        echo "💡 Run the migration: mysql -h localhost -P 3306 -u dbuser -p infosys3 < database/migrations/001_create_blog_posts_table.sql\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "🔍 Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n🎯 Debug completed!\n";

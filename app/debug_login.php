<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use App\Service\ConfigService;
use App\Service\DatabaseService;
use App\Service\UserService;
use App\Repository\UserRepository;

echo "🔍 Login Debug Script\n";
echo "====================\n\n";

try {
    // Load environment
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    
    echo "✅ Environment loaded\n";
    
    // Create services
    $config = new ConfigService();
    $dbService = new DatabaseService($config);
    $userRepository = new UserRepository($dbService);
    $userService = new UserService($userRepository);
    
    echo "✅ Services created\n";
    
    // Check if users table exists
    $pdo = $dbService->getConnection();
    $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
    $tableExists = $stmt->rowCount() > 0;
    
    if (!$tableExists) {
        echo "❌ Users table does not exist!\n";
        echo "💡 Run the migration: mysql -h localhost -P 3306 -u dbuser -p infosys3 < database/migrations/002_create_users_table.sql\n";
        exit(1);
    }
    
    echo "✅ Users table exists\n";
    
    // Check table structure
    $stmt = $pdo->query("DESCRIBE users");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "📋 Users table structure:\n";
    foreach ($columns as $column) {
        echo "  - {$column['Field']}: {$column['Type']}\n";
    }
    
    // Count users
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE deleted_at IS NULL");
    $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    echo "📊 Total users: {$count}\n";
    
    // Check for admin user
    $stmt = $pdo->query("SELECT * FROM users WHERE username = 'admin' AND deleted_at IS NULL");
    $adminUser = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($adminUser) {
        echo "✅ Admin user found:\n";
        echo "  - ID: {$adminUser['id']}\n";
        echo "  - Username: {$adminUser['username']}\n";
        echo "  - Email: {$adminUser['email']}\n";
        echo "  - Role: {$adminUser['role']}\n";
        echo "  - Active: " . ($adminUser['is_active'] ? 'Yes' : 'No') . "\n";
        echo "  - Password Hash: {$adminUser['password_hash']}\n";
        
        // Test password verification
        $testPassword = 'admin123';
        $isValid = password_verify($testPassword, $adminUser['password_hash']);
        echo "  - Password 'admin123' valid: " . ($isValid ? 'YES' : 'NO') . "\n";
        
        if (!$isValid) {
            echo "❌ Password verification failed!\n";
            echo "💡 The password hash in the migration might be incorrect.\n";
            
            // Generate correct hash
            $correctHash = password_hash($testPassword, PASSWORD_DEFAULT);
            echo "🔧 Correct hash for 'admin123': {$correctHash}\n";
            
            // Update the password
            $updateStmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE username = 'admin'");
            $updateStmt->execute([$correctHash]);
            echo "✅ Password hash updated in database\n";
            
            // Test again
            $isValid = password_verify($testPassword, $correctHash);
            echo "  - Password 'admin123' valid after update: " . ($isValid ? 'YES' : 'NO') . "\n";
        }
        
    } else {
        echo "❌ Admin user not found!\n";
        echo "💡 Creating admin user...\n";
        
        // Create admin user with correct password
        $password = 'admin123';
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        
        $insertStmt = $pdo->prepare("
            INSERT INTO users (username, email, password_hash, role, first_name, last_name, is_active) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $insertStmt->execute(['admin', 'admin@example.com', $passwordHash, 'admin', 'Admin', 'User', 1]);
        
        echo "✅ Admin user created with password 'admin123'\n";
    }
    
    // Test authentication through service
    echo "\n🧪 Testing authentication through UserService:\n";
    $user = $userService->authenticateUser('admin', 'admin123');
    
    if ($user) {
        echo "✅ Authentication successful!\n";
        echo "  - User ID: {$user->getId()}\n";
        echo "  - Username: {$user->getUsername()}\n";
        echo "  - Role: {$user->getRole()}\n";
        echo "  - Active: " . ($user->isActive() ? 'Yes' : 'No') . "\n";
    } else {
        echo "❌ Authentication failed!\n";
    }
    
    // Test with wrong password
    $user = $userService->authenticateUser('admin', 'wrongpassword');
    if (!$user) {
        echo "✅ Wrong password correctly rejected\n";
    } else {
        echo "❌ Wrong password was accepted (this is bad!)\n";
    }
    
    // Test with non-existent user
    $user = $userService->authenticateUser('nonexistent', 'admin123');
    if (!$user) {
        echo "✅ Non-existent user correctly rejected\n";
    } else {
        echo "❌ Non-existent user was accepted (this is bad!)\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "🔍 Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n🎯 Debug completed!\n";
echo "\nNext steps:\n";
echo "1. If password was updated, try logging in again with admin/admin123\n";
echo "2. Check the application logs for any errors\n";
echo "3. Verify your .env file has correct database settings\n";

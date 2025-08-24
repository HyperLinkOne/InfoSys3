<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use App\Security\CsrfTokenManager;
use App\Service\ConfigService;
use App\Service\DatabaseService;
use App\Repository\BlogPostRepository;
use App\Service\BlogService;
use App\Service\AuthService;
use App\Controller\BlogController;
use App\Http\Request;
use Twig\Environment;

echo "🔐 CSRF Token Flow Debug\n";
echo "========================\n\n";

try {
    // Create all necessary services
    $config = new ConfigService();
    $dbService = new DatabaseService($config);
    $blogRepo = new BlogPostRepository($dbService);
    $blogService = new BlogService($blogRepo);
    $authService = new AuthService();
    $csrfManager = new CsrfTokenManager();
    
    echo "✅ All services created\n";
    
    // Simulate the controller flow
    echo "\n🧪 Testing Controller Flow:\n";
    
    // Generate token like the controller does
    $controllerToken = $csrfManager->generateToken('default');
    echo "Controller generated token: {$controllerToken}\n";
    
    // Simulate form submission
    echo "\n📝 Simulating Form Submission:\n";
    
    // Create a mock request with the token
    $_POST['_csrf_token'] = $controllerToken;
    $request = new Request();
    
    $submittedToken = $request->input('_csrf_token');
    echo "Submitted token: {$submittedToken}\n";
    
    // Validate the token
    $isValid = $csrfManager->isTokenValid('default', $submittedToken);
    echo "Token validation: " . ($isValid ? '✅ VALID' : '❌ INVALID') . "\n";
    
    // Test with Twig extension token
    echo "\n🎨 Testing Twig Extension:\n";
    $twigToken = $csrfManager->generateToken('default');
    echo "Twig extension token: {$twigToken}\n";
    
    // Check if they're the same
    if ($controllerToken === $twigToken) {
        echo "✅ Tokens are the same\n";
    } else {
        echo "❌ Tokens are different - this is the problem!\n";
    }
    
    // Check session
    echo "\n📋 Session Info:\n";
    echo "Session ID: " . session_id() . "\n";
    if (isset($_SESSION['_csrf_tokens'])) {
        echo "CSRF tokens in session: " . print_r($_SESSION['_csrf_tokens'], true) . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\n🎯 CSRF Flow Debug completed!\n";

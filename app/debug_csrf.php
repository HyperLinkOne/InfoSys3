<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use App\Security\CsrfTokenManager;

echo "🔐 CSRF Token Debug Script\n";
echo "==========================\n\n";

try {
    // Test CSRF token generation and validation
    $csrfManager = new CsrfTokenManager();
    
    echo "✅ CSRF Token Manager created\n";
    
    // Generate a token
    $token = $csrfManager->generateToken('default');
    echo "🔑 Generated token: {$token}\n";
    
    // Validate the token
    $isValid = $csrfManager->isTokenValid('default', $token);
    echo "✅ Token validation: " . ($isValid ? 'VALID' : 'INVALID') . "\n";
    
    // Test with wrong token
    $isValid = $csrfManager->isTokenValid('default', 'wrong-token');
    echo "❌ Wrong token validation: " . ($isValid ? 'VALID (BAD!)' : 'INVALID (GOOD!)') . "\n";
    
    // Test with null token
    $isValid = $csrfManager->isTokenValid('default', null);
    echo "❌ Null token validation: " . ($isValid ? 'VALID (BAD!)' : 'INVALID (GOOD!)') . "\n";
    
    // Test with empty token
    $isValid = $csrfManager->isTokenValid('default', '');
    echo "❌ Empty token validation: " . ($isValid ? 'VALID (BAD!)' : 'INVALID (GOOD!)') . "\n";
    
    // Test session
    echo "\n📋 Session info:\n";
    echo "Session ID: " . session_id() . "\n";
    echo "Session status: " . session_status() . "\n";
    
    if (isset($_SESSION['_csrf_tokens'])) {
        echo "CSRF tokens in session: " . print_r($_SESSION['_csrf_tokens'], true) . "\n";
    } else {
        echo "No CSRF tokens in session\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\n🎯 CSRF Debug completed!\n";

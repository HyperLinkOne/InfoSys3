<?php

declare(strict_types=1);

echo "🔐 Password Hash Generator\n";
echo "==========================\n\n";

$password = 'admin123';
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "Password: {$password}\n";
echo "Hash: {$hash}\n\n";

// Verify the hash
$isValid = password_verify($password, $hash);
echo "Verification: " . ($isValid ? '✅ Valid' : '❌ Invalid') . "\n\n";

echo "You can use this hash in your migration file.\n";

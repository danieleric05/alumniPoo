<?php

/**
 * Setup verification script
 * Tests that all components are correctly configured
 */

echo "========================================\n";
echo "  Alumni CNDA - Setup Verification\n";
echo "========================================\n\n";

$checks = [];
$errors = [];

// Check 1: PHP Version
echo "1. Checking PHP version... ";
if (version_compare(PHP_VERSION, '7.4.0', '>=')) {
    echo "✓ OK (PHP " . PHP_VERSION . ")\n";
    $checks[] = true;
} else {
    echo "✗ FAILED (Need PHP 7.4+, got " . PHP_VERSION . ")\n";
    $errors[] = "PHP version too old";
    $checks[] = false;
}

// Check 2: Required PHP Extensions
echo "2. Checking required PHP extensions... ";
$required_extensions = ['pdo', 'pdo_mysql', 'mbstring'];
$missing = [];
foreach ($required_extensions as $ext) {
    if (!extension_loaded($ext)) {
        $missing[] = $ext;
    }
}
if (empty($missing)) {
    echo "✓ OK\n";
    $checks[] = true;
} else {
    echo "✗ FAILED (Missing: " . implode(', ', $missing) . ")\n";
    $errors[] = "Missing PHP extensions: " . implode(', ', $missing);
    $checks[] = false;
}

// Check 3: Composer
echo "3. Checking Composer installation... ";
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    echo "✓ OK\n";
    $checks[] = true;
} else {
    echo "✗ FAILED (Run: composer install)\n";
    $errors[] = "Composer dependencies not installed";
    $checks[] = false;
}

// Check 4: Environment file
echo "4. Checking .env file... ";
if (file_exists(__DIR__ . '/.env')) {
    echo "✓ OK\n";
    $checks[] = true;
} else {
    echo "✗ FAILED (Copy .env.example to .env)\n";
    $errors[] = ".env file not found";
    $checks[] = false;
}

// Check 5: File permissions
echo "5. Checking file permissions... ";
$writable = [];
$not_writable = [];
$dirs = ['logs', 'src/views', 'core'];
foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        continue;
    }
    if (!is_writable($dir)) {
        $not_writable[] = $dir;
    } else {
        $writable[] = $dir;
    }
}
if (empty($not_writable)) {
    echo "✓ OK\n";
    $checks[] = true;
} else {
    echo "⚠ WARNING (Some directories not writable: " . implode(', ', $not_writable) . ")\n";
    $checks[] = true; // Not critical
}

// Check 6: Bootstrap loadable
echo "6. Checking bootstrap file... ";
if (file_exists(__DIR__ . '/core/bootstrap.php')) {
    echo "✓ OK\n";
    $checks[] = true;
} else {
    echo "✗ FAILED (bootstrap.php not found)\n";
    $errors[] = "bootstrap.php not found";
    $checks[] = false;
}

// Check 7: Models exist
echo "7. Checking model files... ";
$models = ['Users', 'UserContactInfo', 'UserWorkExperience', 'Country', 'Cities'];
$missing_models = [];
foreach ($models as $model) {
    $path = __DIR__ . '/src/Formulair/Model/' . $model . '.php';
    if (!file_exists($path)) {
        $missing_models[] = $model;
    }
}
if (empty($missing_models)) {
    echo "✓ OK\n";
    $checks[] = true;
} else {
    echo "✗ FAILED (Missing: " . implode(', ', $missing_models) . ")\n";
    $errors[] = "Missing model files";
    $checks[] = false;
}

// Check 8: Controllers exist
echo "8. Checking controller files... ";
$controllers = ['BaseController', 'AuthController', 'AlumniController'];
$missing_controllers = [];
foreach ($controllers as $controller) {
    $path = __DIR__ . '/src/Formulair/Controller/' . $controller . '.php';
    if (!file_exists($path)) {
        $missing_controllers[] = $controller;
    }
}
if (empty($missing_controllers)) {
    echo "✓ OK\n";
    $checks[] = true;
} else {
    echo "✗ FAILED (Missing: " . implode(', ', $missing_controllers) . ")\n";
    $errors[] = "Missing controller files";
    $checks[] = false;
}

// Check 9: Views exist
echo "9. Checking view files... ";
$views = [
    'layouts/base.php',
    'auth/login.php',
    'auth/register.php',
    'alumni/dashboard.php',
];
$missing_views = [];
foreach ($views as $view) {
    $path = __DIR__ . '/src/views/' . $view;
    if (!file_exists($path)) {
        $missing_views[] = $view;
    }
}
if (empty($missing_views)) {
    echo "✓ OK\n";
    $checks[] = true;
} else {
    echo "⚠ WARNING (Missing: " . implode(', ', $missing_views) . ")\n";
    $checks[] = true; // Not critical for setup
}

// Check 10: Web entry point
echo "10. Checking web entry point... ";
if (file_exists(__DIR__ . '/web/index.php')) {
    echo "✓ OK\n";
    $checks[] = true;
} else {
    echo "✗ FAILED (web/index.php not found)\n";
    $errors[] = "web/index.php not found";
    $checks[] = false;
}

// Summary
echo "\n========================================\n";
$total = count($checks);
$passed = array_sum($checks);
echo "  Results: $passed/$total checks passed\n";
echo "========================================\n\n";

if (!empty($errors)) {
    echo "❌ Setup Issues Found:\n";
    foreach ($errors as $error) {
        echo "  - $error\n";
    }
    echo "\nPlease fix these issues before running the application.\n";
    exit(1);
} else {
    echo "✅ Setup is complete!\n\n";
    echo "Next steps:\n";
    echo "  1. Start the development server:\n";
    echo "     composer run dev-server\n";
    echo "  2. Or load test data:\n";
    echo "     php src/Formulair/DataFixtures/LoadFixtures.php\n";
    echo "  3. Then visit: http://localhost:8000/login\n\n";
    exit(0);
}

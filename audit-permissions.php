<?php

/**
 * Permission Audit Script
 *
 * Scans all controllers to check for missing authorization checks
 */

$controllers = [];
$issues = [];

// Find all controllers
$modulePaths = [
    'app/Modules/Members/Http/Controllers',
    'app/Modules/Fund/Http/Controllers',
    'app/Modules/Graveyard/Http/Controllers',
];

foreach ($modulePaths as $path) {
    if (!is_dir($path)) continue;

    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($path)
    );

    foreach ($files as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $relativePath = str_replace(getcwd() . DIRECTORY_SEPARATOR, '', $file->getPathname());

            // Skip Auth controllers
            if (strpos($relativePath, '/Auth/') !== false) {
                continue;
            }

            $controllers[] = $relativePath;
        }
    }
}

echo "=================================================\n";
echo "CONTROLLER PERMISSION AUDIT\n";
echo "=================================================\n\n";

$totalControllers = 0;
$controllersWithIssues = 0;
$secureControllers = 0;

foreach ($controllers as $controllerPath) {
    $totalControllers++;
    $content = file_get_contents($controllerPath);

    // Extract controller name
    preg_match('/class\s+(\w+Controller)/', $content, $matches);
    $controllerName = $matches[1] ?? basename($controllerPath, '.php');

    // Check for CRUD methods
    $methods = [
        'index' => 'list',
        'store' => 'create',
        'show' => 'read',
        'update' => 'update',
        'destroy' => 'delete',
    ];

    $methodsFound = [];
    $missingAuth = [];

    foreach ($methods as $method => $permission) {
        // Check if method exists
        if (preg_match('/public\s+function\s+' . $method . '\s*\(/', $content)) {
            $methodsFound[] = $method;

            // Extract the method content
            $methodStart = strpos($content, "function $method");
            if ($methodStart !== false) {
                // Find the method body (simplified - looks for next function or end of class)
                $methodEnd = strpos($content, 'public function', $methodStart + 1);
                if ($methodEnd === false) {
                    $methodEnd = strrpos($content, '}');
                }

                $methodContent = substr($content, $methodStart, $methodEnd - $methodStart);

                // Check for authorization
                $hasAuthorize =
                    strpos($methodContent, '$this->authorize(') !== false ||
                    strpos($methodContent, 'Gate::authorize(') !== false ||
                    strpos($methodContent, 'Gate::allows(') !== false ||
                    strpos($methodContent, '@can') !== false;

                if (!$hasAuthorize) {
                    $missingAuth[] = $method;
                }
            }
        }
    }

    if (!empty($missingAuth)) {
        $controllersWithIssues++;
        echo "❌ INSECURE: $controllerName\n";
        echo "   File: $controllerPath\n";
        echo "   Missing authorization in: " . implode(', ', $missingAuth) . "\n\n";

        $issues[] = [
            'controller' => $controllerName,
            'file' => $controllerPath,
            'methods' => $missingAuth,
        ];
    } elseif (!empty($methodsFound)) {
        $secureControllers++;
        echo "✅ SECURE: $controllerName\n";
        echo "   Methods checked: " . implode(', ', $methodsFound) . "\n\n";
    }
}

echo "\n=================================================\n";
echo "AUDIT SUMMARY\n";
echo "=================================================\n\n";

echo "Total Controllers Scanned: $totalControllers\n";
echo "Secure Controllers: $secureControllers (" . round($secureControllers/$totalControllers*100, 1) . "%)\n";
echo "Insecure Controllers: $controllersWithIssues (" . round($controllersWithIssues/$totalControllers*100, 1) . "%)\n";
echo "Controllers without CRUD: " . ($totalControllers - $secureControllers - $controllersWithIssues) . "\n\n";

if (!empty($issues)) {
    echo "=================================================\n";
    echo "PRIORITY FIXES NEEDED\n";
    echo "=================================================\n\n";

    foreach ($issues as $issue) {
        echo "Controller: {$issue['controller']}\n";
        echo "File: {$issue['file']}\n";
        echo "Add authorization to these methods:\n";
        foreach ($issue['methods'] as $method) {
            echo "  - $method()\n";
        }
        echo "\n";
    }
}

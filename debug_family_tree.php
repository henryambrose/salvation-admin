<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== DEBUGGING THERESA'S FAMILY TREE ===\n";

$theresa = \App\Models\UnifiedPerson::where('uid', 'M-2')->first(); // Theresa Mendonca

if ($theresa) {
    echo "Theresa: " . $theresa->first_name . " " . $theresa->last_name . " (UID: " . $theresa->uid . ")\n";
    
    echo "\n=== Step 1: Theresa's Children ===\n";
    $children = $theresa->getChildren();
    echo "Children count: " . $children->count() . "\n";
    
    foreach ($children as $child) {
        echo "- " . $child->first_name . " " . $child->last_name . " (UID: " . $child->uid . ")\n";
        
        echo "  Step 2: " . $child->first_name . "'s Children (Theresa's Grandchildren):\n";
        $grandchildren = $child->getChildren();
        echo "  Grandchildren count: " . $grandchildren->count() . "\n";
        
        foreach ($grandchildren as $grandchild) {
            echo "  - " . $grandchild->first_name . " " . $grandchild->last_name . " (UID: " . $grandchild->uid . ")\n";
            
            echo "    Step 3: " . $grandchild->first_name . "'s Children (Theresa's Great Grandchildren):\n";
            $greatGrandchildren = $grandchild->getChildren();
            echo "    Great Grandchildren count: " . $greatGrandchildren->count() . "\n";
            
            foreach ($greatGrandchildren as $greatGrandchild) {
                echo "    - " . $greatGrandchild->first_name . " " . $greatGrandchild->last_name . " (UID: " . $greatGrandchild->uid . ")\n";
                
                echo "      Step 4: " . $greatGrandchild->first_name . "'s Children (Theresa's Great Great Grandchildren):\n";
                $greatGreatGrandchildren = $greatGrandchild->getChildren();
                echo "      Great Great Grandchildren count: " . $greatGreatGrandchildren->count() . "\n";
                
                foreach ($greatGreatGrandchildren as $gggc) {
                    echo "      - " . $gggc->first_name . " " . $gggc->last_name . " (UID: " . $gggc->uid . ")\n";
                    echo "        Spouse UID: " . ($gggc->spouse_uid ?? 'null') . "\n";
                    
                    // Check if this is Shanaya
                    if ($gggc->uid === 'M-5') {
                        echo "        ✅ FOUND SHANAYA! This is Theresa's great great grandchild!\n";
                    }
                }
            }
        }
    }
    
    echo "\n=== Testing Direct Method Calls ===\n";
    
    echo "Theresa.getGrandchildren() count: " . $theresa->getGrandchildren()->count() . "\n";
    echo "Theresa.getGreatGrandchildren() count: " . $theresa->getGreatGrandchildren()->count() . "\n";
    echo "Theresa.getGreatGreatGrandchildren() count: " . $theresa->getGreatGreatGrandchildren()->count() . "\n";
    
    echo "\n=== Expected Family Structure ===\n";
    echo "Theresa (M-2)\n";
    echo "├── Xavier (M-1) - Child\n";
    echo "│   ├── Trini (M-4) - Grandchild\n";
    echo "│   │   ├── Shanaya (M-5) - Great Grandchild\n";
    echo "│   │   │   └── [Shanaya's children would be Great Great Grandchildren]\n";
    echo "│   └── [Other Xavier children]\n";
    echo "└── [Other Theresa children]\n";
    
} else {
    echo "Error: Could not find Theresa\n";
}

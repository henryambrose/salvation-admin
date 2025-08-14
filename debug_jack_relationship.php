<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== DEBUGGING JACK'S GREAT GREAT GRANDSON-IN-LAW RELATIONSHIP ===\n";

// Let's test with Theresa as current person (P1) and Jack as P2
$theresa = \App\Models\UnifiedPerson::where('uid', 'M-2')->first(); // Theresa Mendonca
$jack = \App\Models\UnifiedPerson::where('uid', 'M-5707')->first(); // Jack Branganza

if ($theresa && $jack) {
    echo "Theresa (P1): " . $theresa->first_name . " " . $theresa->last_name . " (UID: " . $theresa->uid . ")\n";
    echo "Jack (P2): " . $jack->first_name . " " . $jack->last_name . " (UID: " . $jack->uid . ")\n";
    
    echo "\n=== Testing Relationship Calculation ===\n";
    
    $service = new \App\Services\FamilyTreeService();
    $relationship = $service->calculateRelationship($theresa, $jack);
    
    echo "Relationship: " . $relationship . "\n";
    
    if ($relationship === 'Great Great Grandson-in-Law') {
        echo "✅ SUCCESS: Jack is correctly identified as Great Great Grandson-in-Law to Theresa!\n";
    } else {
        echo "❌ FAILED: Jack is showing as '" . $relationship . "' instead of 'Great Great Grandson-in-Law'\n";
    }
    
    echo "\n=== Debugging the Logic ===\n";
    
    // Check Theresa's great great grandchildren
    echo "Theresa's great great grandchildren:\n";
    $greatGreatGrandchildren = $theresa->getGreatGreatGrandchildren();
    echo "Count: " . $greatGreatGrandchildren->count() . "\n";
    
    foreach ($greatGreatGrandchildren as $gggc) {
        echo "- " . $gggc->first_name . " " . $gggc->last_name . " (UID: " . $gggc->uid . ")\n";
        echo "  Spouse UID: " . ($gggc->spouse_uid ?? 'null') . "\n";
        
        if ($gggc->spouse_uid === $jack->uid) {
            echo "  ✅ MATCH: This great great grandchild has Jack as spouse!\n";
        }
    }
    
    // Check if Jack is married to any of Theresa's great great grandchildren
    echo "\nChecking if Jack is married to any great great grandchild:\n";
    foreach ($greatGreatGrandchildren as $gggc) {
        if ($gggc->spouse_uid === $jack->uid) {
            echo "✅ FOUND: " . $gggc->first_name . " " . $gggc->last_name . " is married to Jack\n";
            echo "This should make Jack 'Great Great Grandson-in-Law' to Theresa\n";
        }
    }
    
    // Check Jack's spouse
    echo "\nJack's spouse info:\n";
    if ($jack->spouse_uid) {
        $jackSpouse = \App\Models\UnifiedPerson::find($jack->spouse_uid);
        if ($jackSpouse) {
            echo "Jack is married to: " . $jackSpouse->first_name . " " . $jackSpouse->last_name . " (UID: " . $jackSpouse->uid . ")\n";
            
            // Check if Jack's spouse is Theresa's great great grandchild
            $isGreatGreatGrandchild = false;
            foreach ($greatGreatGrandchildren as $gggc) {
                if ($gggc->uid === $jackSpouse->uid) {
                    $isGreatGreatGrandchild = true;
                    echo "✅ Jack's spouse IS Theresa's great great grandchild!\n";
                    break;
                }
            }
            
            if (!$isGreatGreatGrandchild) {
                echo "❌ Jack's spouse is NOT Theresa's great great grandchild\n";
            }
        }
    } else {
        echo "❌ Jack has no spouse_uid\n";
    }
    
} else {
    echo "Error: Could not find Theresa or Jack\n";
}

<?php

require_once 'vendor/autoload.php';

use App\Models\UnifiedPerson;
use App\Services\FamilyTreeService;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Testing UnifiedPerson Family Tree ===\n\n";

// Test 1: Check if view exists and has data
echo "1. Checking unified_people view...\n";
$totalPeople = UnifiedPerson::count();
echo "Total people in unified view: $totalPeople\n";

$members = UnifiedPerson::where('source', 'Member')->count();
$externals = UnifiedPerson::where('source', 'External')->count();
echo "Members: $members, External: $externals\n\n";

// Test 2: Find a specific person (Trevin)

$trevin = UnifiedPerson::where('uid', '=', 'E-1')->first();
if ($trevin) {
    echo "Found: {$trevin->full_name} (UID: {$trevin->uid})\n";
    echo "Family No: {$trevin->family_no}\n";
    echo "Father: " . ($trevin->father ? $trevin->father->full_name : 'None') . "\n";
    echo "Mother: " . ($trevin->mother ? $trevin->mother->full_name : 'None') . "\n";
    echo "Spouse: " . ($trevin->spouse ? $trevin->spouse->full_name : 'None') . "\n\n";
} else {
    echo "Not found\n\n";
}

// Test 3: Full family relationship map
if ($trevin) {
    echo "3. Generating Full Family Relationship Map for $trevin->first_name...\n";

    $service = new FamilyTreeService();
    // $familyTree = $service->getFamilyTree($trevin);

    // Load all people in same family
    $allFamilyMembers = UnifiedPerson::where('family_no', $trevin->family_no)
        ->where('uid', '!=', $trevin->uid)
        ->get();

    echo "Known relationships to $trevin->first_name:\n";
    foreach ($allFamilyMembers as $member) {
        $relation = $service->calculateRelationship($trevin, $member);
        echo "- {$member->full_name} ({$member->uid}) → $relation\n";
    }
    echo "\n";

    // Also show external members with relationship
    echo "External members:\n";
    $externalMembers = UnifiedPerson::where('family_no', $trevin->family_no)
        ->where('source', 'External')
        ->get();

    foreach ($externalMembers as $member) {
        $relation = $service->calculateRelationship($trevin, $member);
        echo "- {$member->full_name} ({$member->uid}) → $relation\n";
    }
    echo "\n";
}

// // Test 4: Look for Jessica specifically
// echo "4. Looking for Jessica...\n";
// $jessica = UnifiedPerson::where('first_name', 'LIKE', '%Jessica%')->first();
// if ($jessica) {
//     echo "Found: {$jessica->full_name} (UID: {$jessica->uid})\n";
//     echo "Source: {$jessica->source}\n";
//     echo "Family No: {$jessica->family_no}\n";
    
//     if ($trevin) {
//         echo "Jessica's relationship to Trevin: " . $service->calculateRelationship($trevin, $jessica) . "\n";
//     }
// } else {
//     echo "Jessica not found\n";
// }

// // Test 5: Show all people in SAL-002 family
// echo "\n5. All people in SAL-002 family:\n";
// $sal002People = UnifiedPerson::where('family_no', 'SAL-002')->get();
// foreach ($sal002People as $person) {
//     echo "- {$person->full_name} ({$person->source}) - UID: {$person->uid}\n";
// }

// // Test 6: Show all people with "Jess" in their name
// echo "\n6. All people with 'Jess' in their name:\n";
// $jessPeople = UnifiedPerson::where('first_name', 'LIKE', '%Jess%')->get();
// foreach ($jessPeople as $person) {
//     echo "- {$person->full_name} ({$person->source}) - UID: {$person->uid}\n";
// }

echo "\n=== Test Complete ===\n";

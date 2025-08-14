<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== DEEP DEBUGGING ===\n";

$shanaya = \App\Models\UnifiedPerson::where('uid', 'M-5')->first();
$jessica = \App\Models\UnifiedPerson::where('uid', 'E-2')->first();
$henry = \App\Models\UnifiedPerson::where('uid', 'E-1')->first();

if ($shanaya && $jessica && $henry) {
    echo "Shanaya: " . $shanaya->first_name . " " . $shanaya->last_name . " (UID: " . $shanaya->uid . ")\n";
    echo "Jessica: " . $jessica->first_name . " " . $jessica->last_name . " (UID: " . $jessica->uid . ")\n";
    echo "Henry: " . $henry->first_name . " " . $henry->last_name . " (UID: " . $henry->uid . ")\n";
    
    echo "\n=== Step by Step Debug ===\n";
    
    // Step 1: Check Shanaya's father
    if ($shanaya->father) {
        echo "1. Shanaya's Father: " . $shanaya->father->first_name . " " . $shanaya->father->last_name . " (UID: " . $shanaya->father->uid . ")\n";
        
        // Step 2: Check father's siblings
        $fatherSiblings = $shanaya->father->getSiblings();
        echo "2. Father's Siblings Count: " . $fatherSiblings->count() . "\n";
        
        foreach ($fatherSiblings as $sibling) {
            echo "   - Sibling: " . $sibling->first_name . " " . $sibling->last_name . " (UID: " . $sibling->uid . ")\n";
            
            // Step 3: Check if this sibling is Henry
            if ($sibling->uid === $henry->uid) {
                echo "   ✅ FOUND HENRY! This is Shanaya's uncle.\n";
                
                // Step 4: Check Henry's spouse
                if ($henry->spouse_uid) {
                    $henrySpouse = \App\Models\UnifiedPerson::find($henry->spouse_uid);
                    if ($henrySpouse) {
                        echo "   - Henry's Spouse: " . $henrySpouse->first_name . " " . $henrySpouse->last_name . " (UID: " . $henrySpouse->uid . ")\n";
                        
                        if ($henrySpouse->uid === $jessica->uid) {
                            echo "   ✅ FOUND JESSICA! She's Henry's wife.\n";
                            echo "   🎯 THEREFORE: Jessica should be Aunt to Shanaya!\n";
                        } else {
                            echo "   ❌ Henry's spouse is NOT Jessica. Expected: " . $jessica->uid . ", Got: " . $henrySpouse->uid . "\n";
                        }
                    }
                } else {
                    echo "   ❌ Henry has no spouse_uid\n";
                }
            }
        }
    } else {
        echo "❌ Shanaya has no father\n";
    }
    
    echo "\n=== Direct UID Checks ===\n";
    echo "Henry's spouse_uid: " . ($henry->spouse_uid ?? 'null') . "\n";
    echo "Jessica's uid: " . $jessica->uid . "\n";
    echo "Match: " . (($henry->spouse_uid === $jessica->uid) ? 'YES' : 'NO') . "\n";
    
} else {
    echo "Error: Could not find required people\n";
}

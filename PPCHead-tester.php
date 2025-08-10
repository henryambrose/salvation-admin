// Check if xavier@gmail.com has a member record
$user = \App\Models\User::where('email', 'xavier@gmail.com')->first();
echo $user->name . " - " . $user->roles->pluck('name')->implode(', ');

// Check if there's a member record for this email
$member = \App\Models\Member::where('email', 'xavier@gmail.com')->first();
if ($member) { echo "Member found: " . $member->id; } else { echo "No member record"; }

// If no member, create one (run this only if step 2 shows "No member record")
// <code_block_to_apply_changes_from>
// </code_block_to_apply_changes_from>

// Create PPCHead record
\App\Models\PPCHead::create(['member_id' => $member->id, 'community_id' => 1]);

// Verify
$ppcHeads = \App\Models\PPCHead::where('member_id', $member->id)->get();
echo "PPCHead records: " . $ppcHeads->count();

// Alternative: Quick fix - link to existing member
// If the above doesn't work, try this simpler approach:
$existingMember = \App\Models\PPCHead::first()->member;
// </rewritten_file>

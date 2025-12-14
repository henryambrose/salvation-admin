#!/bin/bash

echo "========================================="
echo "Generating Permission Tests for All Resources"
echo "========================================="
echo ""

# Members Module
echo "Members Module:"
php generate-permission-test.php Designation members
php generate-permission-test.php Cluster members
php generate-permission-test.php CommunityCluster members
php generate-permission-test.php ExternalMember members
php generate-permission-test.php BaptismRecord members
php generate-permission-test.php MarriageRecord members
php generate-permission-test.php DeathRecord members
php generate-permission-test.php IncomeRange members
php generate-permission-test.php AgeGroup members

echo ""
echo "========================================="
echo "Fund Module:"
php generate-permission-test.php MassIntention fund
php generate-permission-test.php MassType fund
php generate-permission-test.php PaymentMethod fund
php generate-permission-test.php AnnualContribution fund
php generate-permission-test.php CommunityContribution fund

echo ""
echo "========================================="
echo "Graveyard Module:"
php generate-permission-test.php Grave graveyard
php generate-permission-test.php PermanentGrave graveyard
php generate-permission-test.php TemporaryGrave graveyard
php generate-permission-test.php Niche graveyard
php generate-permission-test.php NicheTransfer graveyard
php generate-permission-test.php PermanentGraveBooking graveyard
php generate-permission-test.php TemporaryGraveBooking graveyard
php generate-permission-test.php AnnualMaintenanceFee graveyard
php generate-permission-test.php Payment graveyard
php generate-permission-test.php ServiceType graveyard

echo ""
echo "========================================="
echo "✅ All permission tests generated!"
echo ""
echo "Next steps:"
echo "1. Review and customize the generated tests"
echo "2. Run: php artisan test tests/Feature/Permissions"
echo "3. Fix controllers by adding authorization checks"
echo "========================================="

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Mass Intentions Performance Indexes
        Schema::table('mass_intentions', function (Blueprint $table) {
            // Check if indexes don't already exist before adding
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $indexesNames = $sm->listTableIndexes('mass_intentions');
            
            if (!isset($indexesNames['mass_intentions_mass_date_index'])) {
                $table->index('mass_date'); // For date range queries
            }
            if (!isset($indexesNames['mass_intentions_status_mass_date_index'])) {
                $table->index(['status', 'mass_date']); // Composite index for filtering
            }
            if (!isset($indexesNames['mass_intentions_created_at_index'])) {
                $table->index('created_at'); // For ordering and date filtering
            }
            if (!isset($indexesNames['mass_intentions_phone_index'])) {
                $table->index('phone'); // For phone number searches
            }
        });

        // Family Contributions Performance Indexes
        if (Schema::hasTable('family_contributions')) {
            Schema::table('family_contributions', function (Blueprint $table) {
                // Only add indexes that don't already exist
                if (!Schema::hasColumn('family_contributions', 'family_no_index')) {
                    $table->index('family_no'); // Critical for family searches
                }
                $table->index(['start_date', 'end_date']); // For date range filtering
                if (!Schema::hasColumn('family_contributions', 'status_index')) {
                    $table->index('status'); // For status filtering
                }
                $table->index('created_at'); // For date ordering
            });
        }

        // Permanent Grave Bookings Performance Indexes
        Schema::table('permanent_grave_bookings', function (Blueprint $table) {
            $table->index('booking_reference'); // For quick reference lookups
            $table->index('status'); // For status filtering
            $table->index('booking_date'); // For date range queries
            $table->index(['status', 'booking_date']); // Composite for dashboard queries
            $table->index('died_on'); // For death date queries
            $table->index('buried_on'); // For burial date queries
        });

        // Temporary Grave Bookings Performance Indexes
        Schema::table('temporary_grave_bookings', function (Blueprint $table) {
            $table->index('booking_reference'); // For quick reference lookups
            $table->index('status'); // For status filtering
            $table->index('booking_date'); // For date range queries
            $table->index('transfer_due_date'); // Critical for due date queries
            $table->index(['status', 'transfer_due_date']); // Composite for overdue queries
        });

        // Payments Performance Indexes
        Schema::table('payments', function (Blueprint $table) {
            $table->index('payment_reference'); // For quick payment lookups
            $table->index('payment_status'); // For status filtering
            $table->index(['payable_type', 'payable_id']); // Composite for polymorphic queries
            $table->index('payment_date'); // For date range queries
            $table->index('created_at'); // For recent payments
        });

        // Valid Members Performance Indexes (if table exists)
        if (Schema::hasTable('valid_members')) {
            Schema::table('valid_members', function (Blueprint $table) {
                $table->index('member_no'); // For member number searches
                $table->index('family_no'); // For family searches
                $table->index(['first_name', 'last_name']); // For name searches
                $table->index('is_deceased'); // For filtering deceased members
            });
        }

        // Members Performance Indexes (if table exists)
        if (Schema::hasTable('members')) {
            Schema::table('members', function (Blueprint $table) {
                if (!Schema::hasColumn('members', 'family_no_index')) {
                    $table->index('family_no'); // Critical for family operations
                }
                if (!Schema::hasColumn('members', 'member_no_index')) {
                    $table->index('member_no'); // For member searches
                }
                $table->index('created_at'); // For recent member queries
                $table->index(['first_name', 'last_name']); // For name searches
            });
        }

        // Permanent Graves Performance Indexes
        if (Schema::hasTable('permanent_graves')) {
            Schema::table('permanent_graves', function (Blueprint $table) {
                $table->index('grave_no'); // For grave number searches
                $table->index('section'); // For section-based queries
                $table->index(['section', 'row_no']); // Composite for location queries
                $table->index('is_available'); // For availability filtering
            });
        }

        // Temporary Graves Performance Indexes
        if (Schema::hasTable('temporary_graves')) {
            Schema::table('temporary_graves', function (Blueprint $table) {
                $table->index('grave_no'); // For grave number searches
                $table->index('section'); // For section-based queries
                $table->index(['section', 'row_no']); // Composite for location queries
                $table->index('is_available'); // For availability filtering
            });
        }

        // Niches Performance Indexes
        if (Schema::hasTable('niches')) {
            Schema::table('niches', function (Blueprint $table) {
                $table->index('niche_no'); // For niche number searches
                $table->index('section'); // For section-based queries
                $table->index('status'); // For status filtering
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop indexes in reverse order
        
        if (Schema::hasTable('niches')) {
            Schema::table('niches', function (Blueprint $table) {
                $table->dropIndex(['niche_no']);
                $table->dropIndex(['section']);
                $table->dropIndex(['status']);
            });
        }

        if (Schema::hasTable('temporary_graves')) {
            Schema::table('temporary_graves', function (Blueprint $table) {
                $table->dropIndex(['grave_no']);
                $table->dropIndex(['section']);
                $table->dropIndex(['section', 'row_no']);
                $table->dropIndex(['is_available']);
            });
        }

        if (Schema::hasTable('permanent_graves')) {
            Schema::table('permanent_graves', function (Blueprint $table) {
                $table->dropIndex(['grave_no']);
                $table->dropIndex(['section']);
                $table->dropIndex(['section', 'row_no']);
                $table->dropIndex(['is_available']);
            });
        }

        if (Schema::hasTable('members')) {
            Schema::table('members', function (Blueprint $table) {
                $table->dropIndex(['family_no']);
                $table->dropIndex(['member_no']);
                $table->dropIndex(['created_at']);
                $table->dropIndex(['first_name', 'last_name']);
            });
        }

        if (Schema::hasTable('valid_members')) {
            Schema::table('valid_members', function (Blueprint $table) {
                $table->dropIndex(['member_no']);
                $table->dropIndex(['family_no']);
                $table->dropIndex(['first_name', 'last_name']);
                $table->dropIndex(['is_deceased']);
            });
        }

        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['payment_reference']);
            $table->dropIndex(['payment_status']);
            $table->dropIndex(['payable_type', 'payable_id']);
            $table->dropIndex(['payment_date']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('temporary_grave_bookings', function (Blueprint $table) {
            $table->dropIndex(['booking_reference']);
            $table->dropIndex(['status']);
            $table->dropIndex(['booking_date']);
            $table->dropIndex(['transfer_due_date']);
            $table->dropIndex(['status', 'transfer_due_date']);
        });

        Schema::table('permanent_grave_bookings', function (Blueprint $table) {
            $table->dropIndex(['booking_reference']);
            $table->dropIndex(['status']);
            $table->dropIndex(['booking_date']);
            $table->dropIndex(['status', 'booking_date']);
            $table->dropIndex(['died_on']);
            $table->dropIndex(['buried_on']);
        });

        if (Schema::hasTable('family_contributions')) {
            Schema::table('family_contributions', function (Blueprint $table) {
                $table->dropIndex(['family_no']);
                $table->dropIndex(['start_date', 'end_date']);
                $table->dropIndex(['status']);
                $table->dropIndex(['created_at']);
            });
        }

        Schema::table('mass_intentions', function (Blueprint $table) {
            $table->dropIndex(['mass_date']);
            $table->dropIndex(['status', 'mass_date']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['phone']);
        });
    }
};
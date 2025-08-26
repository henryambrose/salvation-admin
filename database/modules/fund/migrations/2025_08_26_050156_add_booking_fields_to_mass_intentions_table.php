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
        Schema::table('mass_intentions', function (Blueprint $table) {
            // Add new fields for the booking form
            $table->string('non_member_name')->nullable()->after('member_id');
            $table->string('phone', 20)->nullable()->after('non_member_name');
            $table->date('mass_date')->nullable()->after('phone');
            $table->foreignId('mass_type_id')->nullable()->constrained('mass_types')->onDelete('set null')->after('mass_date');
            $table->text('special_instructions')->nullable()->after('mass_type_id');
            $table->foreignId('payment_method_id')->nullable()->constrained('payment_methods')->onDelete('set null')->after('special_instructions');
            
            // Make existing fields nullable since they're not used in the new booking flow
            $table->string('family_no', 50)->nullable()->change();
            $table->foreignId('mass_schedule_id')->nullable()->change();
            $table->string('intention_for')->nullable()->change();
            
            // Add indexes for new fields
            $table->index(['mass_date', 'mass_type_id']);
            $table->index('payment_method_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mass_intentions', function (Blueprint $table) {
            // Remove new fields
            $table->dropForeign(['mass_type_id']);
            $table->dropForeign(['payment_method_id']);
            $table->dropColumn([
                'non_member_name',
                'phone',
                'mass_date',
                'mass_type_id',
                'special_instructions',
                'payment_method_id'
            ]);
            
            // Restore original field constraints
            $table->string('family_no', 50)->nullable(false)->change();
            $table->foreignId('mass_schedule_id')->nullable(false)->change();
            $table->string('intention_for')->nullable(false)->change();
            
            // Remove indexes
            $table->dropIndex(['mass_date', 'mass_type_id']);
            $table->dropIndex(['payment_method_id']);
        });
    }
};

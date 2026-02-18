<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('niche_transfers')) {
            // Table doesn't exist at all — create it fresh with all columns
            Schema::create('niche_transfers', function (Blueprint $table) {
                $table->id();

                $table->string('transfer_reference')->unique();
                $table->enum('status', ['pending', 'approved', 'rejected', 'completed', 'cancelled'])->default('pending');

                $table->foreignId('from_temporary_grave_id')->constrained('temporary_graves')->cascadeOnDelete();
                $table->foreignId('from_booking_id')->constrained('temporary_grave_bookings')->cascadeOnDelete();
                $table->foreignId('to_niche_id')->nullable()->constrained('niches')->nullOnDelete();

                $table->enum('transfer_type', ['niche', 'permanent_grave', 'removal'])->default('niche');
                $table->foreignId('to_permanent_grave_id')->nullable()->constrained('permanent_graves')->nullOnDelete();

                $table->date('transfer_request_date')->default(now());
                $table->date('proposed_transfer_date');
                $table->date('actual_transfer_date')->nullable();
                $table->text('transfer_reason');

                $table->string('dead_first_name', 100);
                $table->string('dead_last_name', 100);
                $table->date('date_of_birth')->nullable();
                $table->date('died_on');
                $table->date('buried_on');

                $table->json('selected_services')->nullable();
                $table->decimal('total_cost', 10, 2)->default(0);
                $table->decimal('paid_amount', 10, 2)->default(0);
                $table->decimal('balance_amount', 10, 2)->default(0);
                $table->enum('payment_status', ['pending', 'partial', 'paid', 'completed'])->default('pending');
                $table->string('payment_method')->nullable();
                $table->text('payment_remarks')->nullable();

                $table->enum('applicant_type', ['member', 'external'])->default('external');
                $table->string('applicant_name');
                $table->string('contact_no');
                $table->string('contact_email')->nullable();
                $table->unsignedBigInteger('relationship_id')->nullable();
                $table->foreign('relationship_id')->references('id')->on('relationships');
                $table->text('applicant_address')->nullable();

                $table->string('permit_no')->nullable();
                $table->json('required_documents')->nullable();

                $table->decimal('pending_amount', 10, 2)->default(0);
                $table->integer('last_payment_year')->nullable();
                $table->json('partial_payment_months')->nullable();

                $table->unsignedBigInteger('created_by');
                $table->unsignedBigInteger('updated_by');
                $table->timestamps();
                $table->softDeletes();

                $table->foreign('created_by')->references('id')->on('users');
                $table->foreign('updated_by')->references('id')->on('users');

                $table->index(['status', 'transfer_request_date']);
                $table->index(['proposed_transfer_date']);
                $table->index(['actual_transfer_date']);
                $table->index(['from_temporary_grave_id']);
                $table->index(['to_niche_id']);
                $table->index(['from_booking_id']);
                $table->index(['transfer_reference']);
                $table->index(['dead_first_name', 'dead_last_name']);
            });

            return;
        }

        // Table already exists — apply only missing changes

        // Fix status ENUM if it doesn't have the full set
        DB::statement("ALTER TABLE niche_transfers MODIFY COLUMN status ENUM('pending', 'approved', 'rejected', 'completed', 'cancelled') NOT NULL DEFAULT 'pending'");

        // Make to_niche_id nullable if not already
        DB::statement("ALTER TABLE niche_transfers MODIFY COLUMN to_niche_id BIGINT UNSIGNED NULL");

        Schema::table('niche_transfers', function (Blueprint $table) {
            if (!Schema::hasColumn('niche_transfers', 'transfer_type')) {
                $table->enum('transfer_type', ['niche', 'permanent_grave', 'removal'])
                    ->default('niche')
                    ->after('to_niche_id');
            }

            if (!Schema::hasColumn('niche_transfers', 'to_permanent_grave_id')) {
                $table->foreignId('to_permanent_grave_id')
                    ->nullable()
                    ->after('transfer_type')
                    ->constrained('permanent_graves')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('niche_transfers')) {
            return;
        }

        if (Schema::hasColumn('niche_transfers', 'transfer_type')) {
            Schema::table('niche_transfers', function (Blueprint $table) {
                if (Schema::hasColumn('niche_transfers', 'to_permanent_grave_id')) {
                    $table->dropForeign(['to_permanent_grave_id']);
                    $table->dropColumn('to_permanent_grave_id');
                }
                $table->dropColumn('transfer_type');
            });

            DB::statement("ALTER TABLE niche_transfers MODIFY COLUMN to_niche_id BIGINT UNSIGNED NOT NULL");
            DB::statement("ALTER TABLE niche_transfers MODIFY COLUMN status ENUM('pending', 'confirmed') NOT NULL DEFAULT 'pending'");
        }
    }
};

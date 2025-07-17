<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_reference', 20)->unique();
            $table->foreignId('payer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('recipient_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('broker_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('chozi_code_id')->nullable()->constrained('chozi_codes')->onDelete('set null');
            $table->decimal('amount', 10, 2);
            $table->decimal('broker_commission', 8, 2)->default(0.00);
            $table->decimal('net_amount', 10, 2); // Amount after commission
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'cancelled'])->default('pending');
            $table->enum('payment_type', ['rent', 'deposit', 'maintenance', 'other'])->default('rent');
            $table->text('description')->nullable();
            $table->text('property_details')->nullable(); // JSON or text
            $table->string('payment_method', 50)->default('wallet');
            $table->timestamp('processed_at')->nullable();
            $table->string('transaction_hash', 64)->nullable(); // For encryption reference
            $table->json('security_metadata')->nullable(); // IP, user agent, etc.
            $table->timestamps();
            
            $table->index(['payment_reference']);
            $table->index(['payer_id', 'status']);
            $table->index(['recipient_id', 'status']);
            $table->index(['broker_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
}

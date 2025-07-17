<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('payment_id')->nullable()->constrained('payments')->onDelete('set null');
            $table->string('transaction_type', 50); // login, payment, chozi_code_usage, etc.
            $table->string('action', 100); // specific action performed
            $table->text('description')->nullable();
            $table->json('data')->nullable(); // Relevant data for the transaction
            $table->string('ip_address', 45)->nullable(); // IPv4 and IPv6 support
            $table->text('user_agent')->nullable();
            $table->string('session_id', 100)->nullable();
            $table->enum('severity', ['info', 'warning', 'error', 'critical'])->default('info');
            $table->boolean('is_suspicious')->default(false);
            $table->decimal('amount', 10, 2)->nullable(); // For financial transactions
            $table->string('reference_id', 50)->nullable(); // External reference
            $table->timestamps();
            
            $table->index(['user_id', 'created_at']);
            $table->index(['transaction_type', 'created_at']);
            $table->index(['is_suspicious']);
            $table->index(['payment_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}

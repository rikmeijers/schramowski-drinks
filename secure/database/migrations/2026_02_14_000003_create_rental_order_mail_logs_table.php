<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rental_order_mail_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rental_order_id')->constrained('rental_orders')->cascadeOnDelete();

            // confirmation | reminder | overdue
            $table->string('type');

            $table->string('to_email');
            $table->string('status'); // queued | sent | failed
            $table->text('error_message')->nullable();

            $table->timestamp('attempted_at')->nullable();

            $table->timestamps();

            $table->index(['rental_order_id', 'type']);
            $table->index(['type', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rental_order_mail_logs');
    }
};


<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rental_orders', function (Blueprint $table) {
            $table->id();

            // Customer / renter information (matches the A4 form)
            $table->string('customer_name');
            $table->string('customer_street')->nullable();
            $table->string('customer_city')->nullable();
            $table->string('customer_license_plate')->nullable(); // Kfz.-Kennzeichen
            $table->string('customer_phone')->nullable();
            $table->string('customer_id_number')->nullable(); // Pers.-Ausweis-Nr.
            $table->string('customer_driver_license_number')->nullable(); // Führerschein-Nr.

            // Rental period / date
            $table->date('rental_date')->nullable(); // date on the form
            $table->date('return_date')->nullable(); // for reminders / overdue

            // Items/quantities as structured data
            $table->json('items')->nullable();

            // Website & mail status
            $table->timestamp('confirmation_sent_at')->nullable();
            $table->timestamp('reminder_sent_at')->nullable();
            $table->timestamp('overdue_sent_at')->nullable();

            $table->timestamps();

            $table->index('return_date');
            $table->index('confirmation_sent_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rental_orders');
    }
};


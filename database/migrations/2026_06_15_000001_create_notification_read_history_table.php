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
        Schema::create('notification_read_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('item_module'); // leave, permission, overtime, pinjaman, resign, exit-interview
            $table->unsignedBigInteger('item_id');
            $table->dateTime('marked_at')->nullable(); // waktu item di-mark as read
            $table->dateTime('marked_all_at')->nullable(); // waktu "mark all as read" dilakukan
            $table->timestamps();

            // Unique constraint: satu user, satu item, hanya satu record
            $table->unique(['user_id', 'item_module', 'item_id']);

            // Indices untuk query performance
            $table->index('user_id');
            $table->index(['user_id', 'marked_all_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_read_history');
    }
};

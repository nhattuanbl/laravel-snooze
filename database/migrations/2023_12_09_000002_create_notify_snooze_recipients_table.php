<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notify_snooze_recipients', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('notify_snooze_id');
            $table->unsignedBigInteger('user_id');
            $table->string('channel', 30)->index();
            $table->string('content', 1000);
            $table->dateTime('seen_at')->nullable();
            $table->json('payload')->nullable();
            $table->dateTime('created_at')->useCurrent();

            $table->index('notify_snooze_id');
            $table->index('user_id');
            $table->index('created_at');
        });

        Schema::table('notify_snooze_recipients', function (Blueprint $table) {
            $table->foreign('notify_snooze_id')->references('id')->on('notify_snoozes')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('notify_snooze_recipients', function (Blueprint $table) {
            $table->dropForeign(['notify_snooze_id', 'user_id']);
        });
        Schema::dropIfExists('notify_snooze_recipients');
    }
};

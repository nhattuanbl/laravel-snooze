<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notify_snoozes', function (Blueprint $table) {
            $table->id();
            $table->string('unique_key')->nullable();
            $table->string('event');
            $table->dateTime('snooze_until')->nullable();
            $table->dateTime('sent_at')->nullable();
            $table->json('receiver');
            $table->unsignedBigInteger('notify_snooze_template_id')->nullable();
            $table->string('content', 1000)->nullable();
            $table->timestamps();

            $table->index('unique_key');
            $table->index('event');
            $table->index('snooze_until');
            $table->index('sent_at');
            $table->index('notify_snooze_template_id');
        });

        Schema::table('notify_snoozes', function (Blueprint $table) {
            $table->foreign('notify_snooze_template_id')->references('id')->on('notify_snooze_templates')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('notify_snoozes', function (Blueprint $table) {
            $table->dropForeign(['notify_snooze_template_id']);
        });
        Schema::dropIfExists('notify_snoozes');
    }
};

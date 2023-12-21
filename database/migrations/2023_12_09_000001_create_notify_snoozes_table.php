<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotifySnoozesTable extends Migration
{
    public function up(): void
    {
        Schema::connection(config('snooze.connection'))->create('notify_snoozes', function (Blueprint $table) {
            $table->id();
            $table->string('overlap');
            $table->dateTime('snooze_until')->nullable();
            $table->dateTime('sent_at')->nullable();
            $table->json('receiver')->nullable();
            $table->unsignedBigInteger('notify_snooze_template_id')->nullable();
            $table->string('content', 1000)->nullable();
            $table->json('channels')->nullable();
            $table->timestamps();

            $table->index('overlap');
            $table->index('snooze_until');
            $table->index('sent_at');
            $table->index('notify_snooze_template_id');
        });

        if (config('snooze.no_sql', false)) {
            Schema::connection(config('snooze.connection'))->table('notify_snoozes', function (Blueprint $table) {
                $table->foreign('notify_snooze_template_id')->references('id')->on('notify_snooze_templates')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        if (config('snooze.no_sql', false)) {
            Schema::connection(config('snooze.connection'))->table('notify_snoozes', function (Blueprint $table) {
                $table->dropForeign(['notify_snooze_template_id']);
            });
        }

        Schema::connection(config('snooze.connection'))->dropIfExists('notify_snoozes');
    }
};

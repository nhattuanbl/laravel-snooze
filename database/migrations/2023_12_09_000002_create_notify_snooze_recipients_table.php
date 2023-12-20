<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotifySnoozeRecipientsTable extends Migration
{
    public function up(): void
    {
        Schema::connection(config('snooze.connection'))->create('notify_snooze_recipients', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('notify_snooze_id');
            $table->unsignedBigInteger('user_id');
            $table->string('type')->nullable();
            $table->string('overlap')->nullable();
            $table->string('channel');
            $table->dateTime('seen_at')->nullable();
            $table->string('content', 1000)->nullable();
            $table->json('payload')->nullable();
            $table->dateTime('created_at')->useCurrent();

            $table->index('type');
            $table->index('overlap');
            $table->index('channel');
            $table->index('notify_snooze_id');
            $table->index('user_id');
            $table->index('created_at');
        });

        if (config('snooze.no_sql', false)) {
            Schema::connection(config('snooze.connection'))->table('notify_snooze_recipients', function (Blueprint $table) {
                $table->foreign('notify_snooze_id')->references('id')->on('notify_snoozes')->nullOnDelete();
                $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (config('snooze.no_sql', false)) {
            Schema::connection(config('snooze.connection'))->table('notify_snooze_recipients', function (Blueprint $table) {
                $table->dropForeign(['notify_snooze_id', 'user_id']);
            });
        }
        Schema::connection(config('snooze.connection'))->dropIfExists('notify_snooze_recipients');
    }
};

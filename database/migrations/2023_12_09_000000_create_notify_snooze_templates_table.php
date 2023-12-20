<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotifySnoozeTemplatesTable extends Migration
{
    public function up(): void
    {
        Schema::connection(config('snooze.connection'))->create('notify_snooze_templates', function (Blueprint $table) {
            $table->id();
            $table->string('type', 30)->unique();
            $table->json('channels');
            $table->string('context', 1000);
            $table->integer('min_snooze_daytime')->default(1); //in minutes
            $table->integer('max_snooze_daytime')->default(5); //in minutes
            $table->integer('min_snooze_nighttime')->default(-1); //-1 wait for morning
            $table->integer('max_snooze_nighttime')->default(5); //in minutes
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::connection(config('snooze.connection'))->dropIfExists('notify_snooze_templates');
    }
};

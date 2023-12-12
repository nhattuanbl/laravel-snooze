<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notify_snooze_templates', function (Blueprint $table) {
            $table->id();
            $table->string('type', 30);
            $table->json('channels');
            $table->string('context', 1000);
            $table->boolean('is_hidden')->default(false); //hidden in user notification center
            $table->integer('min_snooze_daytime')->default(0);
            $table->integer('max_snooze_daytime')->default(5);
            $table->integer('min_snooze_nighttime')->default(-1); //-1 wait for morning
            $table->integer('max_snooze_nighttime')->default(5);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notify_snooze_templates');
    }
};

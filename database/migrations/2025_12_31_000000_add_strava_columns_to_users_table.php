<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('strava_id')->nullable()->unique();
            $table->text('strava_access_token')->nullable();
            $table->text('strava_refresh_token')->nullable();
            $table->timestamp('strava_token_expires_at')->nullable();
            $table->string('strava_scope')->nullable();
            $table->timestamp('strava_connected_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['strava_id']);
            $table->dropColumn([
                'strava_id',
                'strava_access_token',
                'strava_refresh_token',
                'strava_token_expires_at',
                'strava_scope',
                'strava_connected_at',
            ]);
        });
    }
};

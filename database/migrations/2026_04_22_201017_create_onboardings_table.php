<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Saasykit\FilamentOnboarding\Constants\OnboardingStatus;

return new class extends Migration
{
    public function up()
    {
        Schema::create('onboardings', function (Blueprint $table) {
            $table->id();

            $table->morphs('onboardable');
            $table->string('status')->default(OnboardingStatus::NOT_STARTED);

            $table->timestamps();
        });
    }
};

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
        Schema::create('noties', function (Blueprint $table) {

            $table->id();

            $table->string('title');

            $table->text('message');

            $table->string('type')->default('General');

            $table->string('image')->nullable();

            $table->string('send_to')->default('All Users');

            $table->enum('status', [
                'Pending',
                'Sent',
                'Failed',
                'Partial Success'
            ])->default('Pending');

            $table->integer('total_users')->default(0);

            $table->integer('success_count')->default(0);

            $table->integer('failed_count')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification');
    }
};

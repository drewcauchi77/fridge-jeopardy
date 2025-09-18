<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * @return void
     */
    public function up(): void
    {
        Schema::create('fridges', function (Blueprint $table) {
            $table->id();
            $table->string('author');
            $table->string('permalink');
            $table->decimal('post_created_at', 15, 1);
            $table->timestamps();
        });
    }
};

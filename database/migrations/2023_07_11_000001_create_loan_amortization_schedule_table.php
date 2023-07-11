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
        //monthly payment, principal component, interest component, and ending balance.
        Schema::create('mortgage_schedule', function (Blueprint $table) {
            $table->id();
            $table->integer('month_number')->default(1);
            $table->float('starting_balance',10,2)->default(0);
            $table->float('ending_balance',10,2)->default(0);
            $table->float('monthly_payment',10,2)->default(0);
            $table->float('principal',10,2)->default(0);
            $table->float('interest',10,2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_amortization_schedule');
    }
};

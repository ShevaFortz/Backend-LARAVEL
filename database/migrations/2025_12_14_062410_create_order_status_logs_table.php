<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('order_status_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')
                ->constrained('orders')
                ->onDelete('cascade');

            $table->enum('status', [
                'menunggu_jemput',
                'dijemput',
                'dicuci',
                'diantar',
                'selesai'
            ]);

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('order_status_logs');
    }
};

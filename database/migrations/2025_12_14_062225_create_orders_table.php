<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->foreignId('id_user')
                ->constrained('users')
                ->onDelete('cascade');

            $table->foreignId('id_outlet')
                ->constrained('outlets')
                ->onDelete('cascade');

            $table->string('kode_order')->unique();
            $table->text('alamat_jemput');
            $table->date('tanggal_jemput');
            $table->time('jam_jemput')->nullable();
            $table->text('catatan')->nullable();

            $table->enum('metode_bayar', ['cash', 'transfer']);

            $table->enum('status', [
                'menunggu_jemput',
                'dijemput',
                'dicuci',
                'diantar',
                'selesai'
            ])->default('menunggu_jemput');

            $table->decimal('total', 12, 2)->default(0);

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
};

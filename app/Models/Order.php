<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'id_user',
        'id_outlet',
        'kode_order',
        'alamat_jemput',
        'tanggal_jemput',
        'jam_jemput',
        'catatan',
        'status',
        'status_bayar',
        'metode_bayar',
        'total'
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusLogs()
    {
        return $this->hasMany(OrderStatusLog::class);
    }

    // ✅ RELASI USER
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    // ✅ RELASI OUTLET
    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'id_outlet');
    }
}

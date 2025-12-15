<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use App\Models\OrderStatusLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class OrderApiController extends Controller
{
    // PELANGGAN BUAT ORDER
    public function store(Request $request)
    {
        $request->validate([
            'id_user' => 'required|exists:users,id',
            'id_outlet' => 'required|exists:outlets,id',
            'alamat_jemput' => 'required',
            'tanggal_jemput' => 'required|date',
            'jam_jemput' => 'required',
            'metode_bayar' => 'required|in:cash,transfer',
            'items' => 'required|array|min:1'
        ]);

        DB::beginTransaction();

        try {
            $order = Order::create([
                'id_user' => $request->id_user,
                'id_outlet' => $request->id_outlet,
                'kode_order' => 'ORD-' . strtoupper(Str::random(6)),
                'alamat_jemput' => $request->alamat_jemput,
                'tanggal_jemput' => $request->tanggal_jemput,
                'jam_jemput' => $request->jam_jemput,
                'catatan' => $request->catatan ?? null,
                'metode_bayar' => $request->metode_bayar,
                'status' => 'menunggu_jemput'
            ]);

            $total = 0;

            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);

                if (!$product) {
                    DB::rollBack();
                    return response()->json([
                        'status' => false,
                        'message' => 'Produk dengan ID ' . $item['product_id'] . ' tidak ditemukan'
                    ], 404);
                }

                $qty = $product->jenis === 'kiloan'
                    ? ($item['berat'] ?? 0)
                    : ($item['qty'] ?? 0);

                $subtotal = $product->harga * $qty;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'berat' => $item['berat'] ?? null,
                    'qty' => $item['qty'] ?? null,
                    'subtotal' => $subtotal
                ]);

                $total += $subtotal;
            }

            $order->update(['total' => $total]);

            OrderStatusLog::create([
                'order_id' => $order->id,
                'status' => 'menunggu_jemput'
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Order berhasil dibuat',
                'data' => $order
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    // ADMIN / DRIVER UPDATE STATUS
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:menunggu_jemput,dijemput,dicuci,diantar,selesai'
        ]);

        $order = Order::findOrFail($id);
        $order->update(['status' => $request->status]);

        OrderStatusLog::create([
            'order_id' => $id,
            'status' => $request->status
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Status order diperbarui'
        ]);
    }

    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'order_ids' => 'required|array|min:1',
            'order_ids.*' => 'exists:orders,id',
            'status' => 'required|in:menunggu_jemput,dijemput,dicuci,diantar,selesai'
        ]);

        DB::transaction(function () use ($request) {

            $orders = Order::whereIn('id', $request->order_ids)->get();

            foreach ($orders as $order) {
                $order->update(['status' => $request->status]);

                OrderStatusLog::create([
                    'order_id' => $order->id,
                    'status' => $request->status
                ]);
            }
        });

        return response()->json([
            'status' => true,
            'message' => 'Order terpilih berhasil diperbarui'
        ]);
    }


    public function updateAllStatus(Request $request)
    {
        $request->validate([
            'status' => 'required|in:menunggu_jemput,dijemput,dicuci,diantar,selesai'
        ]);

        DB::transaction(function () use ($request) {

            $orders = Order::all();

            foreach ($orders as $order) {
                $order->update(['status' => $request->status]);

                OrderStatusLog::create([
                    'order_id' => $order->id,
                    'status' => $request->status
                ]);
            }
        });

        return response()->json([
            'status' => true,
            'message' => 'Semua order berhasil diperbarui'
        ]);
    }


    // LIHAT SEMUA ORDER
    public function index()
    {
        $orders = Order::with(['items.product', 'statusLogs', 'user', 'outlet'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $orders
        ]);
    }

    // LIHAT DETAIL ORDER
    public function show($id)
    {
        $order = Order::with(['items.product', 'statusLogs', 'user', 'outlet'])->find($id);

        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'Order tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $order
        ]);
    }
}

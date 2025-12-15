<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Outlet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showRegister()
    {
        return view('Register.register');
    }
    public function register(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6'
        ]);

        User::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'pelanggan',      // default
            'id_outlet' => 1            // default outlet (WAJIB ADA)
        ]);

        return redirect('/')->with('pesan', 'Registrasi berhasil, silakan login');
    }


    public function index()
    {
        $users = User::join('outlets', 'users.id_outlet', '=', 'outlets.id')
            ->select(['outlets.*', 'users.username', 'users.email', 'users.role', 'users.id', 'users.id', 'users.foto'])->get();

        $outlets = Outlet::all();

        return view('Pelanggan.select', compact('users', 'outlets'));
    }
    public function postlogin(Request $request)
    {
        // dd($request->input());
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $data = $request->only('email', 'password');
        // dd($data);
        if (Auth::attempt($data)) {
            // $user = Auth::user();
            return redirect('laundry/dasbord');
        }

        return back()->with('pesan', 'Password Atau Email Anda Salah');
    }
    public function postregisteradmin(Request $request)
    {
        $request->validate([
            'foto' => 'required|image'
        ]);

        $nfile = $request->file('foto')->getClientOriginalName();

        $request->foto->move(public_path('foto'), $nfile);

        $data = [
            'nama' => $request->nama,
            'username' => $request->username,
            'id_outlet' => $request->id_outlet,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role,
            'foto' => $nfile,
        ];

        // dd($data);

        User::create($data);

        return back();
    }

    public function editpelanggan($id)
    {
        $users = User::where('id', $id)->first();

        $tokos = Outlet::all();

        // dd($users);
        return view('Pelanggan.update', compact('users', 'tokos'));
    }

    public function updatepelanggan(Request $request, $id)
    {
        if (isset($request->password)) {
            User::where('id', $id)->update([
                'nama' => $request->nama,
                'username' => $request->username,
                'id_outlet' => $request->id_outlet,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'role' => $request->role
            ]);
        } else {
            User::where('id', $id)->update([
                'nama' => $request->nama,
                'username' => $request->username,
                'id_outlet' => $request->id_outlet,
                'email' => $request->email,
                'role' => $request->role
            ]);
        }

        return redirect('laundry/selectpelanggan');
    }

    public function deletepelanggan($id)
    {
        $users = User::where('id', $id)->get();
        $role = User::where('role', $users[0]['role']);
        $jumlah = $role->count();

        if ($jumlah == 1) {
            session()->flash('pesan', 'Data Yang Di Hapus Hanya Ada Satu');
        } else {
            User::where('id', $id)->delete();
        }

        return back();
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Kos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pageData = 5;
        if(request('search')){
            $kosData = Kos::where([['adminId', Auth::user()->id], ['name', 'like', '%'.request('search').'%']])->paginate($pageData)->withQueryString();
        } else{
            $kosData = Kos::where('adminId', Auth::user()->id)->orderBy('name', 'asc')->paginate($pageData)->withQueryString();
        }
        return view('admin.kosManagement', compact('kosData'));
    }

    public function electricBill()
    {
        return view('admin.electricBill');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function userRequest()
    {
        return view('admin.userRequest');
    }

    public function profile(){
        return view('admin.adminProfile');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'namaKos' => 'required',
            'alamatKos' => 'required',
            'adminId' => 'required'
        ], [
            'namaKos.required' => 'Nama Kos wajib diisi',
            'alamatKos.required' => 'Alamat Kos wajib diisi',
            'adminId.required' => 'Anda harus login terlebih dahulu'
        ]);

        $kosData = [
            'name' => $request->input('namaKos'),
            'address' => $request->input('alamatKos'),
            'adminId' => $request->input('adminId')
        ];

        Kos::create($kosData);
        return redirect('/dashboard/admin/kos')->with('success', 'Berhasil Menambahkan Kos');
    }

    /**
     * Display the specified resource.
     */
    public function show(Kos $kos)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Kos $kos)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'namaKos' => 'required',
            'alamatKos' => 'required',
        ], [
            'namaKos.required' => 'Nama Kos wajib diisi',
            'alamatKos.required' => 'Alamat Kos wajib diisi',
        ]);

        $kosData = [
            'name' => $request->input('namaKos'),
            'address' => $request->input('alamatKos'),
        ];

        Kos::where('id', $id)->update($kosData);
        return redirect('/dashboard/admin/kos')->with('success', 'Berhasil Update Kos');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Kos::where('id', $id)->delete();
        return redirect('/dashboard/admin/kos')->with('success', 'Berhasil Hapus Kos');
    }
}

<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SettingPenggunaController extends BaseController
{
    protected $model = User::class;
    protected $form;
    protected $title;
    protected $breadcrumb;
    protected $route;
    protected $primaryKey = 'id';

    public function __construct()
    {
        $this->title = 'Setting Pengguna';
        $this->breadcrumb = 'Setting';
        $this->route = 'setting-pengguna';

        $this->form = array(
            array(
                'label' => 'Username',
                'field' => 'username',
                'type' => 'text',
                'placeholder' => '',
                'width' => 12,
                'disabled' => true
            ),
            array(
                'label' => 'Password Saat Ini',
                'field' => 'passwordSaatIni',
                'type' => 'password',
                'placeholder' => '',
                'width' => 12,
                'required' => true

            ),
            array(
                'label' => 'Password Baru',
                'field' => 'passwordBaru',
                'type' => 'password',
                'placeholder' => '',
                'width' => 12,
                'required' => true

            ),
            array(
                'label' => 'Konfirmasi Password Baru',
                'field' => 'passwordKonfirmasiBaru',
                'type' => 'text',
                'placeholder' => '',
                'width' => 12,
                'required' => true

            ),
            
        );
    }

    public function index()
    {
        if(!Auth::user()) {
            return redirect()->route('login');
        };

        $data = User::where('id', Auth::user()->id)->first();

        return view('setting.index', 
            [
                'data' => $data,
                'form' => $this->form, 
                'title' => $this->title,
                'breadcrumb' => $this->breadcrumb,
                'route' => $this->route,
                'primaryKey' => $this->primaryKey
        ]);
    }

    public function store(Request $request)
    {
        $rules = [];
        foreach ($this->form as $field) {
            if (isset($field['required']) && $field['required']) {
                $rules[$field['field']] = 'required';
            }
        }

        // Validasi input
        $validatedData = $request->validate($rules);

        $data = $request->only(array_column($this->form, 'field'));

        $user = User::find(Auth::id());

        if (!$user) {
            return response()->json(['error' => 'User tidak ditemukan'], 404);
        }

        // Validasi password lama
        if (!Hash::check($data['passwordSaatIni'], $user->password)) {
            return response()->json(['error' => 'Password saat ini salah', 'errors' => ['passwordSaatIni' => ['Password saat ini salah']]], 422);
        }

        // Validasi dan hash password baru
        if (!empty($data['passwordBaru'])) {
            if ($data['passwordBaru'] !== $data['passwordKonfirmasiBaru']) {
                return response()->json(['error' => 'Password tidak sesuai', 'errors' => ['passwordKonfirmasiBaru' => ['Password tidak sesuai dengan password baru']]], 422);
            }

            $data['password'] = Hash::make($data['passwordBaru']);
        }

        // Hapus field yang tidak diperlukan
        unset($data['passwordBaru'], $data['passwordKonfirmasiBaru'], $data['passwordSaatIni']);

        // Update user
        $user->update($data);

        return response()->json(['success' => 'Data berhasil diperbarui']);
    }

}

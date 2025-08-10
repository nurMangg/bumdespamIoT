<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Models\HistoryWeb;
use App\Models\Pelanggan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ResetPasswordController extends Controller
{
    protected $model = User::class;
    protected $grid;
    protected $title;
    protected $breadcrumb;
    protected $route;
    protected $primaryKey = 'id';

    public function __construct()
    {
        $this->title = 'Reset Password';
        $this->breadcrumb = 'Setting';
        $this->route = 'reset-password';

        $this->grid = array(
            array(
                'label' => 'Nama User',
                'field' => 'name',
            ),
        );
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = User::all();
            return datatables()::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row){
                        $userId = Crypt::encryptString($row->id);

                        return '<div class="btn-group" role="group" aria-label="Basic example">
                                    <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$userId.'" data-original-title="Reset Password" class="reset-password btn btn-warning btn-xs"><i class="fa-solid fa-key"></i> Reset Password</a>
                                </div>';
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }

        return view('setting.reset-password', 
            [
                'form' => $this->grid, 
                'title' => $this->title,
                'breadcrumb' => $this->breadcrumb,
                'route' => $this->route,
                'primaryKey' => $this->primaryKey
        ]);
    }

    public function resetPassword($id)
    {
        $model = app($this->model);
        $id = Crypt::decryptString($id);
        $data = $model->find($id);

        if (!$data) {
            return response()->json(['error' => 'Data not found'], 404);
        }

        $data->update([
            'password' => Hash::make('password'),
        ]);

        HistoryWeb::create([
            'riwayatUserId' => Auth::user()->id,
            'riwayatTable' => $this->model,
            'riwayatAksi' => 'reset password',
            'riwayatData' => json_encode($data),
        ]);

        return response()->json(['message' => 'Password reset successfully']);
    }
}

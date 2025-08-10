<?php

namespace App\Http\Controllers;

use App\Models\HistoryWeb;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class BaseController extends Controller
{
    protected $model;

    public function edit($id)
    {
        $decodeId = Crypt::decryptString($id);

        $model = app($this->model);
        $data = $model->find($decodeId);

        if (!$data) {
            return response()->json(['error' => 'Data not found'], 404);
        }

        // dd($data);
        return response()->json($data);
    }

    public function update(Request $request, $id)
    {
        $decodeId = Crypt::decryptString($id);
        
        $model = app($this->model);
        $data = $model->find($decodeId);

        if (!$data) {
            return response()->json(['error' => 'Data not found'], 404);
        }

        $data->update($request->all());

        HistoryWeb::create([
            'riwayatUserId' => Auth::user()->id,
            'riwayatTable' => $this->model,
            'riwayatAksi' => 'update',
            'riwayatData' => json_encode($data),
        ]);

        return response()->json(['message' => 'Data updated successfully', 'data' => $data]);
    }

    public function destroy($id)
    {
        $decodeId = Crypt::decryptString($id);

        $model = app($this->model);
        $data = $model->find($decodeId);

        if (!$data) {
            return response()->json(['error' => 'Data not found'], 404);
        }

        HistoryWeb::create([
            'riwayatUserId' => Auth::user()->id,
            'riwayatTable' => $this->model,
            'riwayatAksi' => 'delete',
            'riwayatData' => json_encode($data),
        ]);

        $data->delete();

        return response()->json(['message' => 'Data deleted successfully']);
    }
}

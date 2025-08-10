<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Models\SettingWeb;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class WebController extends BaseController
{
    protected $model = SettingWeb::class;
    protected $form;
    protected $title;
    protected $breadcrumb;
    protected $route;
    protected $primaryKey = 'menuId';

    public function __construct()
    {
        $this->title = 'Setting Web';
        $this->breadcrumb = 'Setting';
        $this->route = 'setting-web';

        $this->form = array(
            array(
                'label' => 'Nama Usaha',
                'field' => 'settingWebNama',
                'type' => 'text',
                'placeholder' => '',
                'width' => 6,
                'required' => true
            ),
            array(
                'label' => 'Logo Usaha',
                'field' => 'settingWebLogo',
                'type' => 'file',
                'placeholder' => '',
                'width' => 6,
                'required' => false

            ),
            array(
                'label' => 'Logo Usaha Landscape',
                'field' => 'settingWebLogoLandscape',
                'type' => 'file',
                'placeholder' => '',
                'width' => 6,
                'required' => false

            ),
            array(
                'label' => 'Alamat Usaha',
                'field' => 'settingWebAlamat',
                'type' => 'textarea',
                'placeholder' => '',
                'width' => 6,
                'required' => true
            ),
            array(
                'label' => 'Email Usaha',
                'field' => 'settingWebEmail',
                'type' => 'email',
                'placeholder' => '',
                'width' => 6,
                'required' => true
            ),
            array(
                'label' => 'Telepon Usaha',
                'field' => 'settingWebPhone',
                'type' => 'text',
                'placeholder' => '',
                'width' => 6,
                'required' => true
            ),
        );
    }

    public function index(Request $request)
    {

        $data = SettingWeb::first();
        if ($request->ajax()) {
            return response()->json($data);
        }

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
        $request->validate($rules);

        $data = $request->only(array_column($this->form, 'field'));

        $settingWeb = SettingWeb::first();

        if ($settingWeb) {
            // Menghapus dan mengupload logo jika ada di request
            if ($request->hasFile('settingWebLogo')) {
                if ($settingWeb->settingWebLogo) {
                    $oldLogoPath = str_replace(url('storage') . '/', '', $settingWeb->settingWebLogo);
                    // dd($oldLogoPath);
                    if (Storage::exists($oldLogoPath)) {
                        Storage::delete($oldLogoPath);
                    }
                }
                $data['settingWebLogo'] = 'storage/' . $request->file('settingWebLogo')->store('logos', 'public');
            }
    
            // Menghapus dan mengupload logo landscape jika ada di request
            if ($request->hasFile('settingWebLogoLandscape')) {
                if ($settingWeb->settingWebLogoLandscape) {
                    $oldLogoLandscapePath = str_replace(url('storage') . '/', '', $settingWeb->settingWebLogoLandscape);
                    if (Storage::exists($oldLogoLandscapePath)) {
                        Storage::delete($oldLogoLandscapePath);
                    }
                }
                $data['settingWebLogoLandscape'] = 'storage/' . $request->file('settingWebLogoLandscape')->store('logos', 'public');
            }
    
            // Update data setting web
            $settingWeb->update($data);
    
            return response()->json(['success' => 'Data Berhasil Diupdate']);
        }
    
        // Jika data setting web tidak ditemukan
        return response()->json(['error' => 'Data not found'], 404);
    }


}

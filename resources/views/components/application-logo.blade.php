<?php
    $settingLogo = \App\Models\SettingWeb::first();
?>
{{-- <img src="{{ asset($settingLogo->settingWebLogo) ?? asset('images/logo.svg') }}" {{ $attributes }} width="140" height="140"> --}}
<img src="{{ asset('images/logolandscape.png') }}"  width="350" height="140">




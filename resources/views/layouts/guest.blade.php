<?php
    $settingWeb = \App\Models\SettingWeb::first();

?>
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $settingWeb->settingWebNama ? $settingWeb->settingWebNama . ' | PDAM' : env('APP_NAME') }}</title>
        <link rel="shortcut icon" href="{{ $settingWeb->settingWebLogo ? asset($settingWeb->settingWebLogo) : asset('images/favicon.svg') }}" type="image/x-icon">
        <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">


        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            body {
                transition: background-image 0.5s ease-in-out;
            }

            @media only screen and (max-width: 767px) {
                .min-h-screen {
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    justify-content: center;
                }
            }
        </style>
    </head>
    <body class="font-sans text-gray-900 antialiased bg-cover bg-center bg-no-repeat">
        <div class="min-h-screen flex flex-col sm:justify-center items-center px-4 sm:px-6 lg:px-8">
            

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white bg-opacity-50 shadow-md overflow-hidden sm:rounded-lg" data-aos="fade-up" data-aos-duration="2000" style="border-radius: 0.5rem;">
                <div class="flex flex-col items-center justify-center mb-4 animate-fade-in-down">
                    <a href="/">
                        <x-application-logo class="fill-current text-gray-500 w-48 h-48 animate-spin-slow" />
                    </a>
                    <p class="text-center text-sm text-gray-600 mt-2">
                        {{ $settingWeb->settingWebAlamat ? $settingWeb->settingWebAlamat : '' }}
                    </p>
                </div>
                {{ $slot }}
            </div>
        </div>

        <!-- Random Background Script -->
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                // Daftar gambar background
                const backgrounds = [
                    "{{ asset('images/873.jpg') }}",
                    "{{ asset('images/9126.jpg') }}",
                    "{{ asset('images/24143.jpg') }}",
                    "{{ asset('images/46361.jpg') }}"
                ];

                // Pilih gambar secara acak
                const randomBackground = backgrounds[Math.floor(Math.random() * backgrounds.length)];

                // Terapkan gambar ke body
                document.body.style.backgroundImage = `url('${randomBackground}')`;
            });
        </script>
        <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
        <script>
            AOS.init();
          </script>
    </body>
</html>


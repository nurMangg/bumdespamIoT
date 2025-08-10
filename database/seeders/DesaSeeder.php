<?php

namespace Database\Seeders;

use App\Models\Desa;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DesaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Desa::insert([
            [
                'desaNama' => 'Srengseng'
            ],
            [
                'desaNama' => 'Rajegwesi'
            ],
            [
                'desaNama' => 'Sidamulya'
            ],
            [
                'desaNama' => 'Mulyoharjo'
            ],
            [
                'desaNama' => 'Semboja'
            ],
            [
                'desaNama' => 'Randusari'
            ],
            [
                'desaNama' => 'Jatiwangi'
            ],
            [
                'desaNama' => 'Pagerbarang'
            ],
            [
                'desaNama' => 'Karanganyar'
            ],
            [
                'desaNama' => 'Kertaharja'
            ],
            [
                'desaNama' => 'Kedungsugih'
            ],
            [
                'desaNama' => 'Surokidul'
            ],
            [
                'desaNama' => 'Pesarean'
            ]
        ]);
    }
}

<?php

namespace Database\Seeders;

use App\Models\Hotel;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class HotelSeeder extends Seeder
{
    public function run(): void
    {
            // Admin user
        User::firstOrCreate(
            ['email' => 'benmouelliachraf@gmail.com'],
            [
                'name'     => 'Achraf',
                'password' => Hash::make('admin123'),
                'role'     => 'admin',
            ]
        );

        // Hotels
        $hotels = [
            ['nom' => 'Hôtel Le Palace',      'categorie' => '5', 'adresse' => 'Avenue Habib Bourguiba, Tunis',       'email' => 'contact@lepalace.tn',          'photos' => ['https://images.unsplash.com/photo-1566073771259-6a8506099945?w=800'], 'prix_unitaire' => 350],
            ['nom' => 'Djerba Beach Resort',   'categorie' => '4', 'adresse' => 'Zone Touristique, Djerba',            'email' => 'info@djerbabeach.tn',           'photos' => ['https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=800'], 'prix_unitaire' => 220],
            ['nom' => 'Sousse Marina Hotel',   'categorie' => '4', 'adresse' => 'Port El Kantaoui, Sousse',            'email' => 'info@soussemarina.tn',          'photos' => ['https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=800'], 'prix_unitaire' => 180],
            ['nom' => 'Hammamet Garden',       'categorie' => '3', 'adresse' => 'Zone Touristique Yasmine, Hammamet',  'email' => 'contact@hammametgarden.tn',     'photos' => ['https://images.unsplash.com/photo-1582719508461-905c673771fd?w=800'], 'prix_unitaire' => 130],
            ['nom' => 'Carthage Prestige',     'categorie' => '5', 'adresse' => 'Rue de Carthage, Tunis',              'email' => 'reservation@carthageprestige.tn', 'photos' => ['https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?w=800'], 'prix_unitaire' => 420],
            ['nom' => 'Tabarka Lodge',         'categorie' => '3', 'adresse' => 'Avenue du Peuple, Tabarka',           'email' => 'tabarka.lodge@gmail.com',       'photos' => ['https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=800'], 'prix_unitaire' => 95],
            ['nom' => 'Monastir Pearl Hotel',  'categorie' => '4', 'adresse' => 'Route de la Corniche, Monastir',      'email' => 'contact@monastirpearl.tn',      'photos' => ['https://images.unsplash.com/photo-1564501049412-61c2a3083791?w=800'], 'prix_unitaire' => 160],
            ['nom' => 'Bizerte Bleu',          'categorie' => '3', 'adresse' => 'Port de Bizerte, Bizerte',            'email' => 'info@bizertebleu.tn',           'photos' => ['https://images.unsplash.com/photo-1568084680786-a84f91d1153c?w=800'], 'prix_unitaire' => 110],
            ['nom' => 'Nabeul Sun Resort',     'categorie' => '4', 'adresse' => 'Avenue Habib Thameur, Nabeul',        'email' => 'info@nabeulsun.tn',             'photos' => ['https://images.unsplash.com/photo-1573843981267-be1999ff37cd?w=800'], 'prix_unitaire' => 145],
            ['nom' => 'Sfax Business Hotel',   'categorie' => '3', 'adresse' => 'Avenue Farhat Hached, Sfax',          'email' => 'contact@sfaxbusiness.tn',       'photos' => ['https://images.unsplash.com/photo-1587213811864-c1efa0b5cb0b?w=800'], 'prix_unitaire' => 90],
            ['nom' => 'Mahdia Beach Club',     'categorie' => '4', 'adresse' => 'Zone Touristique, Mahdia',            'email' => 'info@mahdiabeach.tn',           'photos' => ['https://images.unsplash.com/photo-1590490360182-c33d57733427?w=800'], 'prix_unitaire' => 195],
            ['nom' => 'Gammarth Luxury Suites','categorie' => '5', 'adresse' => 'Avenue Taieb Mhiri, Gammarth',        'email' => 'reservation@gammarth.tn',       'photos' => ['https://images.unsplash.com/photo-1578683010236-d716f9a3f461?w=800'], 'prix_unitaire' => 480],
        ];

        foreach ($hotels as $hotel) {
            Hotel::firstOrCreate(['nom' => $hotel['nom']], $hotel);
        }
    }
}

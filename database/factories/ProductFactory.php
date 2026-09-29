<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $catalog = [
            'makanan' => [
                'Mie Instan Indomie Goreng', 'Mie Instan Mie Sedaap Soto', 'Sarden ABC 155 g',
                'Kornet Sapi Pronas 198 g', 'Sambal Goreng Bu Rudy 200 g', 'Kecap Manis Bango 135 ml',
                'Saus Sambal ABC 335 ml', 'Santan Kara 200 ml', 'Tepung Bumbu Sajiku tepung bumbu 200 g',
                'Makaroni La Fonte 200 g', 'Spaghetti La Fonte 225 g', 'Abon Sapi 100 g',
                'Selai Stroberi Morin 170 g', 'Selai Kacang Skippy 340 g', 'Keju Kraft Cheddar 165 g',
                'Sosis Ayam Champ 375 g', 'Nugget Ayam Fiesta 500 g', 'Bakso Sapi 500 g',
                'Tahu Putih 10 pcs', 'Tempe Daun 1 papan',
            ],
            'minuman' => [
                'Air Mineral Aqua 600 ml', 'Air Mineral Le Minerale 600 ml', 'Teh Botol Sosro 450 ml',
                'Teh Pucuk Harum 350 ml', 'Coca-Cola 390 ml', 'Sprite 390 ml',
                'Susu UHT Ultra Milk 250 ml', 'Susu Cokelat Milo 220 ml', 'Jus Buavita Jambu 250 ml',
                'Jus Buavita Jeruk 250 ml', 'Kopi Kapal Api Special Mix 10 sachet', 'Kopi Good Day Cappuccino 10 sachet',
                'Minuman Isotonik Pocari Sweat 500 ml', 'Minuman Energi Extra Joss 6 sachet', 'Sirup Marjan Cocopandan 460 ml',
                'Yogurt Cimory 250 ml', 'Susu Kental Manis Frisian Flag 370 g', 'Teh Celup Sariwangi 25 kantong',
                'Minuman Cokelat Beng-Beng 250 ml', 'Air Kelapa Hydro Coco 250 ml',
            ],
            'sembako' => [
                'Beras Ramos 5 kg', 'Beras Pandan Wangi 5 kg', 'Minyak Goreng Bimoli 2 L',
                'Minyak Goreng Sania 1 L', 'Gula Pasir Gulaku 1 kg', 'Garam Dapur Cap Kapal 500 g',
                'Tepung Terigu Segitiga Biru 1 kg', 'Telur Ayam Negeri 1 kg', 'Telur Ayam Kampung 10 butir',
                'Margarin Blue Band 200 g', 'Kacang Hijau 500 g', 'Kecap Asin ABC 133 ml',
                'Gas LPG 3 kg', 'Beras Merah 2 kg', 'Beras Ketan Putih 1 kg',
                'Tepung Beras Rose Brand 500 g', 'Tepung Tapioka Sagu Tani 500 g', 'Minyak Goreng Fortune 2 L',
                'Gula Merah 500 g', 'Susu Bubuk Dancow 400 g',
            ],
            'snack' => [
                'Chitato Sapi Panggang 68 g', 'Qtela Singkong Balado 60 g', 'Taro Net Seaweed 65 g',
                'Oreo Original 133 g', 'Biskuit Roma Kelapa 300 g', 'Wafer Tango Cokelat 130 g',
                'SilverQueen Cashew 62 g', 'Cokelat Delfi Dairy Milk 35 g', 'Pocky Cokelat 45 g',
                'Kacang Garuda Rosta 100 g', 'Kusuka Keripik Singkong 60 g', 'Pringles Original 107 g',
                'Nabati Wafer Keju 145 g', 'Good Time Cookies 72 g', 'Better Sandwich Biscuit (tb) 100 g',
                'Astor Wafer Stick 330 g', 'Pilus Garuda 95 g', 'Permen Kopiko 120 g',
                'Yupi Burger  net 45 g', 'Biskuit Monde Butter Cookies 150 g',
            ],
            'alat rumah tangga' => [
                'Detergen Rinso Anti Noda 800 g', 'Detergen Attack Jaz1 700 g', 'Sabun Cuci Piring Sunlight 755 ml',
                'Pembersih Lantai Wipol 780 ml', 'Pewangi Pakaian Molto 900 ml', 'Tisu Paseo 250 sheets',
                'Tisu Toilet Nice 10 rolls', 'Sabun Mandi Lifebuoy 110 g', 'Sampo Sunsilk 170 ml',
                'Pasta Gigi Pepsodent 190 g', 'Sikat Gigi Formula 1 pcs', 'Spons Cuci Piring Scotch-Brite 3 pcs',
                'Kantong Sampah Hitam 60 x 80 cm', 'Plastik Klip 15 x 20 cm', 'Aluminium Foil 30 cm x 5 m',
                'Baterai ABC AA 2 pcs', 'Lampu LED Philips 9 W', 'Sapu Lantai Plastik 1 pcs',
                'Pel Lantai Microfiber 1 pcs', 'Tempat Makan Plastik 1 L',
            ],
        ];

        $category = fake()->randomElement(array_keys($catalog));
        $stock = fake()->numberBetween(0, 100);
        $priceRange = match ($category) {
            'makanan' => [2_000, 100_000],
            'minuman' => [2_000, 75_000],
            'sembako' => [5_000, 150_000],
            'snack' => [2_000, 75_000],
            default => [3_000, 150_000],
        };

        return [
            'name' => fake()->randomElement($catalog[$category]),
            'category' => $category,
            'description' => fake()->optional()->sentence(),
            'price' => fake()->numberBetween(...$priceRange),
            'stock' => $stock,
            'image' => null,
            'is_active' => $stock > 0,
        ];
    }
}
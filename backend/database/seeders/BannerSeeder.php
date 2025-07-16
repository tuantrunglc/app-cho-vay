<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Banner;

class BannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Banner::create([
            'title' => 'Vay tiền nhanh - Lãi suất thấp',
            'description' => 'Giải pháp tài chính tối ưu cho mọi nhu cầu của bạn',
            'image_url' => '/images/banners/banner1.jpg',
            'link_url' => '/loans/apply',
            'position' => 'home_hero',
            'is_active' => true,
            'sort_order' => 1
        ]);

        Banner::create([
            'title' => 'Thủ tục đơn giản - Duyệt nhanh',
            'description' => 'Chỉ cần CMND và giấy tờ thu nhập, duyệt trong 30 phút',
            'image_url' => '/images/banners/banner2.jpg',
            'link_url' => '/loans/calculate',
            'position' => 'home_features',
            'is_active' => true,
            'sort_order' => 2
        ]);

        Banner::create([
            'title' => 'Hỗ trợ 24/7',
            'description' => 'Đội ngũ tư vấn chuyên nghiệp luôn sẵn sàng hỗ trợ bạn',
            'image_url' => '/images/banners/banner3.jpg',
            'link_url' => '/contact',
            'position' => 'home_support',
            'is_active' => true,
            'sort_order' => 3
        ]);
    }
}
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
            'subtitle' => 'Giải pháp tài chính tối ưu cho mọi nhu cầu của bạn',
            'image_url' => '/images/banners/banner1.jpg',
            'link_url' => '/loans/apply',
            'active' => true,
            'order' => 1
        ]);

        Banner::create([
            'title' => 'Thủ tục đơn giản - Duyệt nhanh',
            'subtitle' => 'Chỉ cần CMND và giấy tờ thu nhập, duyệt trong 30 phút',
            'image_url' => '/images/banners/banner2.jpg',
            'link_url' => '/loans/calculate',
            'active' => true,
            'order' => 2
        ]);

        Banner::create([
            'title' => 'Hỗ trợ 24/7',
            'subtitle' => 'Đội ngũ tư vấn chuyên nghiệp luôn sẵn sàng hỗ trợ bạn',
            'image_url' => '/images/banners/banner3.jpg',
            'link_url' => '/contact',
            'active' => true,
            'order' => 3
        ]);
    }
}
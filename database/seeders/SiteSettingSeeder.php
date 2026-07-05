<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            // Hero
            'hero_title'    => 'Your Campus Marketplace, All in One Place',
            'hero_subtitle' => 'Browse products, discover concessionaires, and stay connected with everything CvSU Trece Martires has to offer.',

            // Features section
            'features_title'   => 'Everything Your Office Needs, In One System',
            'feature_1_title'  => 'Partnership Applications',
            'feature_1_desc'   => 'Allow organizations and individuals to apply for partnerships directly through the platform, keeping requirements, progress, and review steps clear from day one.',
            'feature_2_title'  => 'Secure & Reliable',
            'feature_2_desc'   => 'Role-based access control ensures administrators, faculty, concessionaires, cashiers, and students each get the tools they need without exposing the rest.',
            'feature_3_title'  => 'Payments & Tracking',
            'feature_3_desc'   => 'Record payments, generate receipts, and monitor concessionaire transactions with a cleaner status view that supports both office staff and concessionaires.',

            // Concessionaires showcase (cards are generated from approved concessionaires)
            'showcase_title'    => 'Campus Concessionaires',
            'showcase_subtitle' => 'These are our listed concessionaires that are currently partnered with the campus. They provide quality food, services, and products directly to students and staff.',

            // Mission & Vision
            'vision'  => 'The premier university in historic Cavite globally recognized for excellence in character development, academics, research, innovation and sustainable community engagement.',
            'mission' => 'Cavite State University shall provide excellent, equitable and relevant educational opportunities in the arts, sciences and technology through quality instruction and responsive research and development activities. It shall produce professional, skilled and morally upright individuals for global competitiveness.',

            // Core Values
            'core_value_1' => 'Truth',
            'core_value_2' => 'Excellence',
            'core_value_3' => 'Service',
            'core_value_4' => 'Integrity',
            'core_value_5' => 'Innovation',

            // Frequently Asked Questions
            'faq_1_question' => 'How do I apply for a concessionaire partnership?',
            'faq_1_answer'   => 'Simply create an account on our platform. Once registered, navigate to the application portal where you can fill out the required information and upload necessary documents like your MOA, Contract, and Business Proposal securely.',
            'faq_2_question' => 'Who can view and review products in the marketplace?',
            'faq_2_answer'   => 'Anyone can browse the marketplace to check available products and concessionaires. However, only registered and approved students and faculty members can submit ratings and leave reviews to ensure feedback authenticity.',
            'faq_3_question' => 'How do concessionaires track their payment records?',
            'faq_3_answer'   => 'Approved concessionaires have access to an exclusive dashboard where they can view their payment history, upcoming fixed monthly deadlines, and any overdue balances synced directly from the Cashier module.',
            'faq_4_question' => 'Is the system restricted to just concessionaires?',
            'faq_4_answer'   => 'No! While concessionaires use it for managing store offerings and checking balances, the system is actively used by students to check marketplace availability and by campus staff to monitor uniform stocks safely and securely.',
        ];

        foreach ($defaults as $key => $value) {
            SiteSetting::firstOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}

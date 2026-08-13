<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        Page::firstOrCreate(['slug' => 'privacy-policy'], [
            'title' => 'Privacy Policy',
            'content' => '<h5>Privacy Policy</h5>
<p>This Privacy Policy describes how Lokabuy collects, uses, and shares information about you when you use our services.</p>

<h6>Information We Collect</h6>
<p>We collect information you provide directly to us, such as when you create an account, post an ad, or contact us for support.</p>

<h6>How We Use Your Information</h6>
<p>We use the information we collect to provide, maintain, and improve our services, process transactions, and communicate with you.</p>

<h6>Information Sharing</h6>
<p>We do not share your personal information with third parties except as described in this policy or with your consent.</p>

<h6>Contact Us</h6>
<p>If you have any questions about this Privacy Policy, please contact us at support@lokabuy.com.</p>',
        ]);

        Page::firstOrCreate(['slug' => 'terms-of-use'], [
            'title' => 'Terms of Use',
            'content' => '<h5>Terms of Use</h5>
<p>By accessing or using Lokabuy, you agree to be bound by these Terms of Use.</p>

<h6>Use of the Service</h6>
<p>You may use our service only for lawful purposes and in accordance with these Terms. You must not post any content that is illegal, fraudulent, or misleading.</p>

<h6>Listings</h6>
<p>You are solely responsible for the content of your listings. All listings are subject to review and approval by our team before becoming publicly visible.</p>

<h6>Account Responsibility</h6>
<p>You are responsible for maintaining the confidentiality of your account credentials and for all activities that occur under your account.</p>

<h6>Termination</h6>
<p>We reserve the right to suspend or terminate your account at any time if you violate these Terms.</p>

<h6>Contact Us</h6>
<p>If you have any questions about these Terms, please contact us at support@lokabuy.com.</p>',
        ]);
    }
}

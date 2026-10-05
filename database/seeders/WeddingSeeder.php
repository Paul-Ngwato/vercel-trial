<?php

namespace Database\Seeders;

use App\Models\Wedding;
use App\Models\Church;
use App\Models\PaymentSetting;
use App\Models\User;
use App\Models\Event;
use App\Models\InformationItem;
use App\Models\StoryMilestone;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class WeddingSeeder extends Seeder
{
    public function run(): void
    {
        // Create super admin
        User::create([
            'name' => 'Wedding Admin',
            'email' => 'admin@wedding.com',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
        ]);

        // Create wedding
        $wedding = Wedding::create([
            'bride_name' => 'Netta',
            'groom_name' => 'Jeff Orlando',
            'wedding_date' => now()->addMonths(3)->toDateString(),
            'wedding_time' => '09:00',
            'ceremony_venue' => 'All Saints Cathedral',
            'ceremony_address' => 'Upper Hill, Nairobi, Kenya',
            'ceremony_map_url' => 'https://maps.google.com/?q=All+Saints+Cathedral+Nairobi',
            'reception_venue' => 'Karen Country Club',
            'reception_address' => 'Karen, Nairobi, Kenya',
            'reception_map_url' => 'https://maps.google.com/?q=Karen+Country+Club+Nairobi',
            'theme' => 'Elegant Garden',
            'meta_title' => 'Netta & Jeff Orlando — Wedding Celebration',
            'meta_description' => 'Join us as we celebrate the wedding of Netta and Jeff Orlando.'
        ]);

        // Church (via relationship)
        $wedding->church()->create([
            'name' => 'All Saints Cathedral',
            'pastor_name' => 'Rev. Dr. James Mwangi',
            'address' => 'Upper Hill, Nairobi',
            'phone' => '+254 20 271 0000',
            'message_from_church' => 'We are blessed to host this beautiful union. May God shower His grace upon the couple as they begin this journey together.',
        ]);

        // Payment settings
        PaymentSetting::create([
            'method' => 'mpesa',
            'label' => 'M-Pesa',
            'details' => ['paybill' => '123456', 'account' => 'WEDDING', 'phone' => '0700000000'],
            'instructions' => "1. Go to M-Pesa Menu\n2. Lipa na M-Pesa > Pay Bill\n3. Business No: 123456\n4. Account: WEDDING\n5. Enter amount\n6. Enter your PIN\n7. Enter the transaction code below",
            'is_active' => true,
            'display_order' => 1,
        ]);

        PaymentSetting::create([
            'method' => 'bank',
            'label' => 'Bank Transfer',
            'details' => ['bank' => 'Equity Bank', 'account_name' => 'Wedding Fund', 'account_number' => '1234567890', 'branch' => 'Karen Branch', 'swift' => 'EQBLKENA'],
            'instructions' => "1. Transfer to the account above\n2. Use your name as reference\n3. Upload your receipt below",
            'is_active' => true,
            'display_order' => 2,
        ]);

        // Sample events (via relationship)
        $events = [
            ['title' => 'Guest Arrival', 'start_time' => '08:00', 'end_time' => '08:45', 'display_order' => 1],
            ['title' => 'Wedding Ceremony', 'start_time' => '09:00', 'end_time' => '11:00', 'display_order' => 2],
            ['title' => 'Photography Session', 'start_time' => '11:30', 'end_time' => '12:30', 'display_order' => 3],
            ['title' => 'Reception', 'start_time' => '13:00', 'end_time' => '13:30', 'display_order' => 4],
            ['title' => 'Lunch', 'start_time' => '13:30', 'end_time' => '14:30', 'display_order' => 5],
            ['title' => 'Speeches & Toasts', 'start_time' => '15:00', 'end_time' => '16:00', 'display_order' => 6],
            ['title' => 'Dancing & Celebration', 'start_time' => '16:00', 'end_time' => '19:00', 'display_order' => 7],
        ];
        foreach ($events as $event) {
            $wedding->events()->create($event + ['status' => 'active']);
        }

        // Information items (via relationship)
        $items = [
            ['title' => 'Dress Code', 'content' => 'Formal / Black Tie Optional. Ladies, please consider comfortable footwear for the garden ceremony.', 'icon' => '👔', 'display_order' => 1],
            ['title' => 'Parking', 'content' => 'Free parking is available at both venues. Please follow the signs upon arrival.', 'icon' => '🚗', 'display_order' => 2],
            ['title' => 'Transport', 'content' => 'Shuttle service will be available from the hotel to the ceremony and reception venues.', 'icon' => '🚌', 'display_order' => 3],
            ['title' => 'Accommodation', 'content' => 'A block of rooms has been reserved at Karen Country Club at a special rate. Quote "Kamau Wedding" when booking.', 'icon' => '🏨', 'display_order' => 4],
            ['title' => 'Photography', 'content' => 'Professional photography will be available. Please feel free to take your own photos but kindly switch off flash during the ceremony.', 'icon' => '📸', 'display_order' => 5],
            ['title' => 'Children', 'content' => 'A kids\' corner will be available during the reception. We love your little ones!', 'icon' => '👶', 'display_order' => 6],
            ['title' => 'Gifts', 'content' => 'Your presence is the greatest gift. If you wish to give, contributions toward our new home are welcome via M-Pesa or bank transfer.', 'icon' => '🎁', 'display_order' => 7],
            ['title' => 'Contact', 'content' => 'For any questions, please reach out to the wedding coordinator at +254 700 123 456.', 'icon' => '📞', 'display_order' => 8],
        ];
        foreach ($items as $item) {
            $wedding->informationItems()->create($item + ['is_published' => true]);
        }

        // Story milestones (via relationship)
        $milestones = [
            ['title' => 'How We Met', 'content' => 'It was a bright Saturday morning at a mutual friend\'s birthday party. Amidst the laughter and music, our eyes met across the room. What started as a simple conversation about shared interests turned into hours of talking, laughing, and discovering how much we had in common.', 'display_order' => 1],
            ['title' => 'Our Journey', 'content' => 'From that day, we became inseparable. We explored Nairobi together, cooked meals in our tiny apartments, supported each other through career changes, and traveled to new places. Every adventure strengthened our bond and deepened our love.', 'display_order' => 2],
            ['title' => 'The Proposal', 'content' => 'On a beautiful sunset evening at Diani Beach, with the waves gently touching the shore, Jeff got down on one knee. With trembling hands and a heart full of love, he asked the question that changed everything. And without hesitation, the answer was yes!', 'display_order' => 3],
            ['title' => 'The Big Day', 'content' => 'Now, we invite you to join us as we take the next step in our beautiful journey. With God\'s grace and your blessings, we look forward to a lifetime of love, laughter, and happiness together.', 'display_order' => 4],
        ];
        foreach ($milestones as $milestone) {
            $wedding->storyMilestones()->create($milestone);
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Investment;
use App\Models\Transaction;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\Kyc;
use App\Models\RoiLog;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Faker\Factory as Faker;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        // 1. Create a Primary Test User
        $mainUser = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'username' => 'testuser',
                'password' => Hash::make('password'),
                'role' => 'INVESTOR',
                'is_active' => true,
                'kyc_status' => 'VERIFIED',
                'wallet_balance' => 50000,
                'email_verified_at' => now(),
            ]
        );

        $users = [$mainUser];

        // 2. Create 50 Dummy Users, some under mainUser, some under others
        $this->command->info('Creating Users...');
        for ($i = 0; $i < 50; $i++) {
            $sponsor = $faker->boolean(70) ? $faker->randomElement($users) : null;
            
            $user = User::create([
                'name' => $faker->name,
                'username' => $faker->unique()->userName,
                'email' => $faker->unique()->safeEmail,
                'password' => Hash::make('password'),
                'sponsor_id' => $sponsor ? $sponsor->id : null,
                'role' => 'INVESTOR',
                'is_active' => $faker->boolean(90),
                'kyc_status' => $faker->randomElement(['UNVERIFIED', 'PENDING', 'VERIFIED', 'REJECTED']),
                'wallet_balance' => $faker->randomFloat(2, 0, 10000),
                'email_verified_at' => now(),
            ]);
            $users[] = $user;
        }

        // 3. Create Investments and Transactions
        $this->command->info('Creating Investments & Transactions...');
        $statuses = ['PENDING', 'ACTIVE', 'COMPLETED', 'CLOSED', 'CLOSE_REQUEST', 'REJECTED'];
        $trxTypes = ['commission', 'investment', 'withdrawal'];

        foreach ($users as $user) {
            // Investments
            $numInvestments = $faker->numberBetween(1, 5);
            for ($i = 0; $i < $numInvestments; $i++) {
                $status = $faker->randomElement($statuses);
                $createdAt = Carbon::now()->subDays($faker->numberBetween(1, 180));
                
                $investment = Investment::create([
                    'user_id' => $user->id,
                    'trx_id' => 'INV-' . strtoupper(Str::random(10)),
                    'amount' => $faker->randomElement([1000, 5000, 10000, 20000, 50000]),
                    'type' => 'manual',
                    'status' => $status,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);

                // Create ROI logs for this investment
                $numRoi = $faker->numberBetween(1, 4);
                for ($j = 0; $j < $numRoi; $j++) {
                    RoiLog::create([
                        'trx_id' => 'ROI-' . strtoupper(Str::random(10)),
                        'investment_id' => $investment->id,
                        'user_id' => $user->id,
                        'amount' => $investment->amount * 0.05, // 5% ROI
                        'rate' => 5.0,
                        'status' => $faker->randomElement(['pending', 'credited', 'rejected']),
                        'created_at' => $createdAt->copy()->addDays($j + 1),
                        'updated_at' => $createdAt->copy()->addDays($j + 1),
                    ]);
                }
            }

            // Transactions (For charts)
            $numTransactions = $faker->numberBetween(10, 30);
            for ($i = 0; $i < $numTransactions; $i++) {
                $createdAt = Carbon::now()->subDays($faker->numberBetween(1, 180));
                
                Transaction::create([
                    'user_id' => $user->id,
                    'amount' => $faker->randomFloat(2, 10, 1000),
                    'type' => $faker->randomElement($trxTypes),
                    'description' => $faker->sentence(3),
                    'reference_id' => 'TRX-' . strtoupper(Str::random(10)),
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);
            }

            // Tickets
            if ($faker->boolean(40)) {
                $ticket = Ticket::create([
                    'user_id' => $user->id,
                    'ticket_id' => 'TKT-' . strtoupper(Str::random(8)),
                    'subject' => $faker->sentence,
                    'status' => $faker->randomElement(['OPEN', 'CLOSED', 'ANSWERED']),
                    'priority' => $faker->randomElement(['LOW', 'MEDIUM', 'HIGH']),
                    'created_at' => Carbon::now()->subDays($faker->numberBetween(1, 30)),
                ]);

                TicketMessage::create([
                    'ticket_id' => $ticket->id,
                    'user_id' => $user->id,
                    'message' => $faker->paragraph,
                ]);
            }
            
            // KYC
            if ($user->kyc_status !== 'UNVERIFIED') {
                Kyc::create([
                    'user_id' => $user->id,
                    'document_type' => $faker->randomElement(['Passport', 'National ID', 'Driver License']),
                    'document_number' => $faker->numerify('##########'),
                    'document_front_proof' => 'proofs/dummy.jpg',
                    'document_back_proof' => 'proofs/dummy.jpg',
                    'status' => strtolower($user->kyc_status) === 'verified' ? 'approved' : strtolower($user->kyc_status),
                ]);
            }
        }

        $this->command->info('Dummy data seeded successfully!');
    }
}

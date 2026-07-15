<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Loan>
 */
class LoanFactory extends Factory
{
    protected $model = Loan::class;

    public function definition(): array
    {
        $borrowedAt = fake()->dateTimeBetween('-30 days', 'now');

        return [
            'user_id' => User::factory(),
            'book_id' => Book::factory(),
            'borrowed_at' => $borrowedAt,
            'due_at' => (clone $borrowedAt)->modify('+7 days'),
            'returned_at' => null,
            'status' => Loan::STATUS_APPROVED,
            'fine_amount' => 0,
            'rejection_reason' => null,
            'reviewed_by' => null,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn () => [
            'status' => Loan::STATUS_PENDING,
            'due_at' => null,
            'reviewed_by' => null,
            'rejection_reason' => null,
        ]);
    }
}

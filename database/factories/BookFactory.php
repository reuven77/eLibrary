<?php

namespace Database\Factories;

use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Book>
 */
class BookFactory extends Factory
{
    protected $model = Book::class;

    public function definition(): array
    {
        $format = fake()->randomElement(['fisik', 'digital', 'keduanya']);

        return [
            'title' => fake()->sentence(4),
            'isbn' => fake()->unique()->numerify('978-##########'),
            'author_id' => Author::factory(),
            'category_id' => Category::factory(),
            'cover_image_path' => null,
            'file_path' => in_array($format, ['digital', 'keduanya'], true) ? 'ebooks/sample.pdf' : null,
            'format' => $format,
            'stock' => $format === 'digital' ? 0 : fake()->numberBetween(0, 5),
            'synopsis' => fake()->paragraphs(2, true),
            'published_year' => fake()->numberBetween(1980, 2025),
            'call_number' => fake()->numerify('###.##') . ' ' . strtoupper(fake()->lexify('???')),
        ];
    }

    public function physical(int $stock = 1): static
    {
        return $this->state(fn () => [
            'format' => 'fisik',
            'stock' => $stock,
            'file_path' => null,
        ]);
    }

    public function digital(): static
    {
        return $this->state(fn () => [
            'format' => 'digital',
            'stock' => 0,
            'file_path' => 'ebooks/sample.pdf',
        ]);
    }
}

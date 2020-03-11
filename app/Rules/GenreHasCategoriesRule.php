<?php
declare(strict_types = 1);

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Collection;
use PhpParser\ErrorHandler\Collecting;

class GenreHasCategoriesRule implements Rule
{
    private $categoriesId;
    private $genresId;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(array $categoriesId)
    {
        $this->categoriesId = array_unique($categoriesId);
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $this->genresId = array_unique($value);
        if (!count($this->genresId) || !count($this->categoriesId)) {
            return false;
        }

        $categoriesFound = [];
        foreach ($this->genresId as $genreId) {
            $rows = $this->getRows($genreId);
            if(!$rows->count()){
                return false;
            }
            array_push($categoriesFound, ...$rows->pluck('category_id')->toArray());
        }

        return count($categoriesFound) === count($this->categoriesId);
    }

    protected function getRows($genreId): Collection
    {
        return \DB::table('category_genre')
            ->where('genre_id', $genreId)
            ->whereIn('category_id', $this->categoriesId)
            ->get();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'A genre ID must be related to at least a category ID';
    }
}

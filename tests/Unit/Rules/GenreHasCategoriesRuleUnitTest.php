<?php


namespace Tests\Unit\Rules;


use App\Rules\GenreHasCategoriesRule;
use Illuminate\Contracts\Validation\Rule as Rule;
use Mockery\MockInterface;
use Tests\TestCase;

class GenreHasCategoriesRuleUnitTest extends TestCase
{

    public function testInstanceOfRule()
    {
        $obj = new GenreHasCategoriesRule([1]);
        $this->assertInstanceOf(Rule::class, $obj);
    }

    public function testCategoriesIdField()
    {
        $rule = new GenreHasCategoriesRule([1, 1, 2, 2]);

        $reflectClass = new \ReflectionClass(GenreHasCategoriesRule::class);
        $reflectProp = $reflectClass->getProperty('categoriesId');
        $reflectProp->setAccessible(true);

        $categoriesId = $reflectProp->getValue($rule);
        $this->assertEqualsCanonicalizing([1, 2], $categoriesId);
    }

    public function testGenresIdField()
    {
        $rule = $this->createRuleMock([]);
        $rule->shouldReceive('getRows')
            ->withAnyArgs()
            ->andReturnNull();
        $rule->passes('', [1, 1, 2, 2]);

        $reflectClass = new \ReflectionClass(GenreHasCategoriesRule::class);
        $reflectProp = $reflectClass->getProperty('genresId');
        $reflectProp->setAccessible(true);

        $genresId = $reflectProp->getValue($rule);
        $this->assertEqualsCanonicalizing([1, 2], $genresId);
    }

    public function testReturnFalseWhenCategoriesOrGenreIDIsEmptyArray()
    {
        $rule = $this->createRuleMock([1]);
        $this->assertFalse($rule->passes('', []));

        $rule = $this->createRuleMock([]);
        $this->assertFalse($rule->passes('', [1]));
    }

    public function testReturnFalseWhenGetRowsIsEmpty()
    {
        $rule = $this->createRuleMock([1]);
        $rule->shouldReceive('getRows')
            ->withAnyArgs()
            ->andReturn(collect());
        $this->assertFalse($rule->passes('', [1]));
    }

    public function testReturnFalseWithCategoriesWithoutGenres()
    {
        $rule = $this->createRuleMock([1, 2]);
        $rule->shouldReceive('getRows')
            ->withAnyArgs()
            ->andReturn(collect(['category_id' => 1]));
        $this->assertFalse($rule->passes('', [1]));
    }

    public function testIfPassesIsValid()
    {
        $rule = $this->createRuleMock([1, 2]);
        $rule->shouldReceive('getRows')
            ->withAnyArgs()
            ->andReturn(collect([['category_id' => 1], ['category_id' => 2]]));
        $this->assertTrue($rule->passes('', [1]));

        $rule = $this->createRuleMock([1, 2]);
        $rule->shouldReceive('getRows')
            ->withAnyArgs()
            ->andReturn(collect([['category_id' => 1], ['category_id' => 2], ['category_id' => 1], ['category_id' => 2]]));
        $this->assertTrue($rule->passes('', [1]));
    }

    protected function createRuleMock(array $categoriesId): MockInterface
    {
        return \Mockery::mock(GenreHasCategoriesRule::class, [$categoriesId])
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
    }
}

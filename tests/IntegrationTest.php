<?php
use Tests\TestCase;
use Illuminate\Database\Eloquent\Model;
use Beestreams\LaravelCategories\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class IntegrationTest extends TestCase
{
    use DatabaseMigrations;

    private $exampleModel;

    public function setUp()
    {
        //  ../../../vendor/bin/phpunit
        parent::setUp();
        $this->exampleModel();
    }

    /** @test */
    public function new_categories_can_be_added_directly ()
    {
        $name = 'Cats';
        $this->exampleModel->addOrCreateCategory($name);
        $this->assertEquals($this->exampleModel->categories->first()->name, $name);
    }

    /** @test */
    public function multiple_new_categories_can_be_added_directly ()
    {
        $names = ['Cats', 'Dogs'];
        $this->exampleModel->addOrCreateCategories($names);
        $categoryCount = $this->exampleModel->categories->count();
        $this->assertEquals($categoryCount, 2);
    }

    /** @test */
    public function categories_can_be_cleared ()
    {
        $names = ['Cats', 'Dogs'];
        $this->exampleModel->addOrCreateCategories($names);
        $this->exampleModel->removeCategories();
        $categoryCount = $this->exampleModel->categories->count();
        $this->assertEquals($categoryCount, 0);
    }

    /** @test */
    public function categories_can_be_detached_by_name ()
    {
        $names = ['Cats', 'Dogs'];
        $this->exampleModel->addOrCreateCategories($names);
        $this->exampleModel->removeCategory($names[1]);
        $categoryCount = $this->exampleModel->categories->count();
        $this->assertEquals($categoryCount, 1);
    }

    /** @test */
    public function categories_can_be_synced ()
    {
        $oldCats = ['Cats', 'Dogs', 'Goats'];
        $newCats = ['Dogs', 'Goats'];
        $this->exampleModel->addOrCreateCategories($oldCats);
        $categoriesToSync = collect();
        foreach ($newCats as $name) {
            $properties = [
                'name' => $name,
                'slug' => str_slug($name, '-')
            ];
            $categoriesToSync->push(Category::firstOrCreate($properties));
        }
        $this->exampleModel->syncCategories($categoriesToSync);
        
        $idsToSync = $categoriesToSync->pluck('id')->toArray();
        $currentIds = $this->exampleModel->categories->pluck('id')->toArray();

        $this->assertEquals($idsToSync, $currentIds);
    }

    /** @test */
    public function categories_are_not_added_multiple_times ()
    {
        // Tests if existing categories are re-added when attached to model

        $names = ['Cats', 'Dogs'];
        $properties = [
            'name' => $names[0],
            'slug' => str_slug($names[0], '-')
        ];

        $existing = Category::create($properties);

        $this->exampleModel->addOrCreateCategories($names);
        $categoryCount = $this->exampleModel->categories->count();
        $allCategoryCount = Category::all()->count();
        
        $this->assertEquals($allCategoryCount, 2);
        $this->assertEquals($categoryCount, 2);
    }

    /**
     * Set up model that categories are attached to
     */
    private function exampleModel()
    {
        $this->exampleModel = new CatObject();
        $this->exampleModel->id = 1;
    }
}

/**
* Parent
*/
class CatObject extends Model
{
    use Beestreams\LaravelCategories\Traits\Categorizable;
}
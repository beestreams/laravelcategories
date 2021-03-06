<?php

namespace Beestreams\LaravelCategories\Traits;

use Beestreams\LaravelCategories\Models\Category;

trait Categorizable 
{
    /**
     * Add or create single category
     * @param String $name The Category name to be added
     */
    public function addOrCreateCategory($name)
    {
        if (!is_string($name)) {
            return false;
        }

        $properties = [
            'name' => $name,
            'slug' => str_slug($name, '-')
        ];

        $category = Category::firstOrCreate($properties);

        $this->addToCategory($category->id);

        return $this;
    }

    /**
     * Add or create multiple categories
     * @param Array $names Strings of names
     */
    public function addOrCreateCategories(Array $names)
    {
        if (!is_array($names)) {
            return false;
        }

        $categories = collect();
        foreach ($names as $name) {
            $properties = [
                'name' => $name,
                'slug' => str_slug($name, '-')
            ];
            $categories->push(Category::firstOrCreate($properties));
        }
        $this->addToCategories($categories->pluck('id')->toArray());

        return $this;
    }

    /**
    * Get all of the categories for the categorable model.
    */
    public function categories()
    {
        return $this->morphToMany(Category::class, 'categorizable');
    }

    /**
     * Add single category by ID
     * @param int $id The ID of the Category
     */
    public function addToCategory(int $id)
    {
        if (!$id) {
            return false;
        }

        $this->addToCategories([$id]);

        return $this;
    }

    /**
     * Add to multiple categories by ID array
     * @param Array $categoryIds Array of IDs
     */
    public function addToCategories(Array $categoryIds)
    {
        $this->categories()->attach($categoryIds);
        return $this;
    }

    /**
     * Adds existing categories to model
     * @param Collection $categoryIds Collection of Category models
     */
    public function syncCategories($categories)
    {
        if ($categories->isEmpty()) {
            return false;
        }

        $categoryIds = $categories->pluck('id')->toArray();

        $this->categories()->sync($categoryIds);

        return $this;
    }

    /**
     * Remove categories by IDs. Null value removes all
     * @param  Array or nothing $ids
     */
    public function removeCategories($ids = null)
    {
        $this->categories()->detach($ids);
        return $this;
    }

    /**
     * Remove category by ID or Name
     * @param  Int or String $category will be checked and handled
     */
    public function removeCategory($category)
    {
        if (is_int($category)) {
            $this->removeCategoryById($category);
        }
        if (is_string($category)) {
            $category = Category::where('name', $category)->firstOrFail();
        }

        $this->removeCategories([$category->id]);
        
        return $this;
    }
}
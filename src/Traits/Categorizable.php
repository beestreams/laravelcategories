<?php

namespace Beestreams\LaravelCategories\Traits;

use Beestreams\LaravelCategories\Models\Category;

trait Categorizable 
{
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
    }

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
    }

    /**
    * Get all of the categories for the categorable model.
    */
    public function categories()
    {
        return $this->morphToMany(Category::class, 'categorizable');
    }

    public function addToCategory(int $id)
    {
        if (!$id) {
            return false;
        }

        $this->addToCategories([$id]);

        return $this;
    }

    public function addToCategories(Array $categoryIds)
    {
        $this->categories()->attach($categoryIds);
    }

    /**
     * Adds existing categories to model
     * @param Array $categoryIds Array of existing Ids
     */
    public function syncCategories(Array $categoryIds)
    {
        if (empty($categoryIds)) {
            return false;
        }

        if (!is_array($categoryIds)) {
            $categoryIds = $categoryIds->pluck('id')->toArray();
        }

        $this->categories()->sync($categoryIds);

        return $this;
    }

    public function removeCategories($ids = null)
    {
        $this->categories()->detach($ids);
        return $this;
    }

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
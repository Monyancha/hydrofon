<?php

namespace Tests\Feature\Categories;

use Hydrofon\Category;
use Hydrofon\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Categories can be updated.
     *
     * @return void
     */
    public function testCategoriesCanBeUpdated()
    {
        $user     = factory(User::class)->create();
        $parents  = factory(Category::class, 2)->create();
        $category = factory(Category::class)->create([
            'parent_id' => $parents[0]->id
        ]);

        $response = $this->actingAs($user)->put('categories/' . $category->id, [
            'name'      => 'New Category Name',
            'parent_id' => $parents[1]->id,
        ]);

        $response->assertRedirect('/categories');
        $this->assertDatabaseHas('categories', [
            'name'      => 'New Category Name',
            'parent_id' => $parents[1]->id,
        ]);
    }

    /**
     * Categories must have a name.
     *
     * @return void
     */
    public function testCategoriesMustHaveAName()
    {
        $user     = factory(User::class)->create();
        $category = factory(Category::class)->create();

        $response = $this->actingAs($user)->put('categories/' . $category->id, [
            'name' => '',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('categories', [
            'name' => $category->name,
        ]);
    }

    /**
     * A parent category must exist in the database.
     *
     * @return void
     */
    public function testParentMustExist()
    {
        $user     = factory(User::class)->create();
        $category = factory(Category::class)->create([
            'parent_id' => factory(Category::class)->create()->id,
        ]);

        $response = $this->actingAs($user)->put('categories/' . $category->id, [
            'name'      => $category->name,
            'parent_id' => 100,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('categories', [
            'name'      => $category->name,
            'parent_id' => $category->parent_id
        ]);
    }

    /**
     * Category can't be its own parent.
     *
     * @return void
     */
    public function testCategoryMustNotBeItsOwnParent()
    {
        $user     = factory(User::class)->create();
        $category = factory(Category::class)->create([
            'parent_id' => factory(Category::class)->create()->id,
        ]);

        $response = $this->actingAs($user)->put('categories/' . $category->id, [
            'name'      => $category->name,
            'parent_id' => $category->id,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('categories', [
            'name'      => $category->name,
            'parent_id' => $category->parent_id
        ]);
    }
}
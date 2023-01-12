<?php

namespace App\Models;

use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\Category
 *
 * @property int $id
 * @property int|null $parent_category_id
 * @property string $name
 * @property string $alias
 * @property string $description
 * @property int $status
 * @property string|null $image_url
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Category|null $parentCategory
 * @method static CategoryFactory factory(...$parameters)
 * @method static Builder|Category newModelQuery()
 * @method static Builder|Category newQuery()
 * @method static Builder|Category query()
 * @method static Builder|Category whereAlias($value)
 * @method static Builder|Category whereCreatedAt($value)
 * @method static Builder|Category whereDescription($value)
 * @method static Builder|Category whereId($value)
 * @method static Builder|Category whereImageUrl($value)
 * @method static Builder|Category whereName($value)
 * @method static Builder|Category whereParentCategoryId($value)
 * @method static Builder|Category whereStatus($value)
 * @method static Builder|Category whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Category extends Model
{
    use HasFactory;

    public function parentCategory()
    {
        return $this->belongsTo(Category::class, 'parent_category_id');
    }


}

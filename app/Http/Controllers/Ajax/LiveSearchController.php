<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class LiveSearchController extends Controller
{
    protected array $allowedEntities = [
        'user'      => User::class,
        'category'  => Category::class,
        'product'   => Product::class,
    ];

    public function search(Request $request, string $locale, $entity): JsonResource
    {
        if (!in_array($entity, array_keys($this->allowedEntities))) {
            return new JsonResource([]);
        }

        $search = $request->post('search', '');

        return match ($entity) {
            'user' => new JsonResource(
                $this->allowedEntities[$entity]
                    ::select(['id as value', 'email as text'])
                    ->where('name', 'LIKE', $search . '%')
                    ->orWhere('name', 'LIKE', '%' . $search . '%')
                    ->orWhere('name', 'LIKE', '%' . $search)
                    ->get()->toArray()
            ),
            default => new JsonResource(
                $this->allowedEntities[$entity]
                    ::select(['id as value', "name->$locale as text"])
                    ->where("name->$locale", 'LIKE', $search . '%')
                    ->orWhere("name->$locale", 'LIKE', '%' . $search . '%')
                    ->orWhere("name->$locale", 'LIKE', '%' . $search)
                    ->get()->toArray()
            ),
        };
    }
}

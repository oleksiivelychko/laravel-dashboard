<?php

namespace App\Http\Controllers\AJAX;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;


class SearchController extends Controller
{
    protected array $searchEntities;

    public function __construct()
    {
        /**
         * Table name => Route name
         */
        $this->searchEntities = [
            'categories' => 'category.edit',
            'products' => 'product.edit'
        ];
    }

    public function search(Request $request, string $locale): JsonResource
    {
        $querySearch = $request->get('querySearch');
        if (!$querySearch) {
            return new JsonResource([]);
        }

        $queryBuilder = null;
        foreach ($this->searchEntities as $table=>$route) {
            $query = DB::table($table)
                ->select(['id', "name->$locale as text", DB::raw("'$route' as route_name")])
                ->where("name->$locale", 'LIKE', $querySearch . '%')
                ->orWhere("name->$locale", 'LIKE', '%' . $querySearch . '%')
                ->orWhere("name->$locale", 'LIKE', '%' . $querySearch);

            $queryBuilder = $queryBuilder ? $queryBuilder->union($query) : $query;
        }

        $links = [];
        foreach ($queryBuilder->get() as $row) {
            $links[$row->text] = route($row->route_name, ['locale' => $locale, $row->id]);
        }

        return new JsonResource($links);
    }
}

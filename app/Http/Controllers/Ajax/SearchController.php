<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

final class SearchController extends Controller
{
    /**
     * Table name => Route name
     */
    private const SEARCH_ENTITIES = [
        'categories'    => 'category.edit',
        'products'      => 'product.edit',
    ];

    public function search(Request $request, string $locale): JsonResource
    {
        $querySearch = strtolower($request->get('querySearch', ''));
        if (empty($querySearch)) {
            return new JsonResource([]);
        }

        $queryBuilder = null;
        foreach (self::SEARCH_ENTITIES as $table=>$route) {
            $raw = "lower(json_unquote(json_extract(`name`, '$.\"$locale\"'))) like ?";
            $query = DB::table($table)
                ->select(['id', "name->$locale as text", DB::raw("'$route' as route_name")])
                ->whereRaw($raw, ["%$querySearch%"])
                ->orWhereRaw($raw, ["%$querySearch"])
                ->orWhereRaw($raw, ["$querySearch%"]);

            $queryBuilder = $queryBuilder ? $queryBuilder->union($query) : $query;
        }

        $links = [];
        foreach ($queryBuilder->get() as $row) {
            $links[$row->text] = route($row->route_name, ['locale' => $locale, $row->id]);
        }

        return new JsonResource($links);
    }
}

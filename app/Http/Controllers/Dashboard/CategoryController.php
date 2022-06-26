<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\StoreCategoryRequest;
use App\Models\Category;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request, Category $categories, string $locale): Factory|View|Application
    {
        return view('dashboard.category.index', [
            'categories' => $categories->pagination($request, $locale)
        ]);
    }

    public function create(): Factory|View|Application
    {
        return view('dashboard.category.create')->with('category', new Category);
    }

    public function store(StoreCategoryRequest $request): Redirector|RedirectResponse|Application
    {
        $validatedData = $request->validated();
        if ($validatedData) {
            $category = new Category;
            if ($category->saveModel($validatedData)) {
                $request->session()->put('status', trans('dashboard.messages.model-create-success'));
                return redirect(route('category.edit', ['category' => $category, 'locale' => app()->getLocale()]));
            }
        }
    }

    public function edit(string $locale, Category $category): Factory|View|Application
    {
        return view('dashboard.category.edit')->with('category', $category);
    }

    public function update(StoreCategoryRequest $request, string $locale, Category $category): Redirector|Application|RedirectResponse
    {
        $validatedData = $request->validated();
        if ($validatedData) {
            if ($category->saveModel($validatedData)) {
                $request->session()->put('status', trans('dashboard.messages.model-update-success'));
            }
        }

        return redirect(route('category.edit', ['category' => $category, 'locale' => $locale]));
    }

    public function destroy(string $locale, Category $category): Redirector|Application|RedirectResponse
    {
        $ids = $category->selectUniqueProductIds();
        if ($category->delete()) {
            $category->deleteUnusedProducts($ids);
            Session::put('status', trans('dashboard.messages.model-delete-success'));
        }

        return redirect(route('dashboard.categories', $locale));
    }
}

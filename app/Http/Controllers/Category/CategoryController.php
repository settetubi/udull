<?php

namespace App\Http\Controllers\Category;

use App\Category;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use phpDocumentor\Reflection\Types\Self_;

class CategoryController extends ApiController
{

    const NAME_ARG = 'name';
    const DESCRIPTION_ARG = 'description';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $categories = Category::all();
        return $this->showAll($categories, 200);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            self::NAME_ARG => 'required|max:'. Category::MAX_LEN_NAME,
            self::DESCRIPTION_ARG => 'required|max:'. Category::MAX_LEN_DESCRIPTION
        ]);
        $category = new Category();
        $category->name = $request->name;
        $category->description = $request->description;
        $category->save();
        return $this->showOne($category, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param Category $category
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function show(Category $category)
    {
        return $this->showOne($category, 200);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Category $category
     * @return void
     */
    public function update(Request $request, Category $category)
    {
        $this->validate($request, [
            self::NAME_ARG => 'max:'. Category::MAX_LEN_NAME,
            self::DESCRIPTION_ARG => 'max:'. Category::MAX_LEN_DESCRIPTION
        ]);

        $category->name = $request->name ?? '';
        $category->description = $request->description ?? '';
        $category->save();

        return $this->showOne($category, 200);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}

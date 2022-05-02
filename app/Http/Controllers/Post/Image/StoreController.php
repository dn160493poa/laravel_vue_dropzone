<?php

namespace App\Http\Controllers\Post\Image;

use App\Http\Controllers\Controller;
use App\Http\Requests\Post\Image\StoreRequest;
use App\Models\Image;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StoreController extends Controller
{
    public function __invoke(StoreRequest $request)
    {
        $data = $request->validated();

        $image = $data['image'];
        $name = md5(Carbon::now() . '_' . $image->getClientOriginalName()) . '.' . $image->getClientOriginalExtension();
        $file_path = Storage::disk('public')->putFileAs('/images', $image, $name);



        return response()->json(['url' => url('/storage/' . $file_path)]);
    }
}

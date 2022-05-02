<?php

namespace App\Http\Controllers\Post;

use App\Http\Controllers\Controller;
use App\Http\Requests\Post\StoreRequest;
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
        $images = $data['images'];
        unset($data['images']);

        $post = Post::firstOrCreate($data);
        foreach ($images as $image){
            $name = md5(Carbon::now() . '_' . $image->getClientOriginalName()) . '.' . $image->getClientOriginalExtension();
            $file_path = Storage::disk('public')->putFileAs('/images', $image, $name);
            $preview_name = 'prev_' . $name;

            Image::create([
                'path' => $file_path,
                'url' => url('/storage/' . $file_path),
                'preview_url' => url('/storage/images/' . $preview_name),
                'post_id' => $post->id
            ]);

            \Intervention\Image\Facades\Image::make($image)->fit(100, 100)
                ->save(storage_path('app/public/images/' . $preview_name));
        }


        return response()->json(['message' => 'success']);
    }
}

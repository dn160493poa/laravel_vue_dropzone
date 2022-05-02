<?php

namespace App\Http\Controllers\Post;

use App\Http\Controllers\Controller;
use App\Http\Requests\Post\StoreRequest;
use App\Http\Requests\Post\UpdateRequest;
use App\Models\Image;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UpdateController extends Controller
{
    public function __invoke(UpdateRequest $request, Post $post)
    {
        $data = $request->validated();
        $images = isset($data['images']) ? $data['images'] : null;
        $image_ids_for_delete = isset($data['image_ids_for_delete']) ? $data['image_ids_for_delete'] : null;
        $image_urls_for_delete = isset($data['image_urls_for_delete']) ? $data['image_urls_for_delete'] : null;
        unset($data['images'], $data['image_ids_for_delete'], $data['image_urls_for_delete']);
        $post->update($data);

        $current_images = $post->images;
        if($image_ids_for_delete){
            foreach ($current_images as $current_image){
                if(in_array($current_image->id, $image_ids_for_delete)){
                    Storage::disk('public')->delete($current_image->path);
                    Storage::disk('public')->delete(str_replace('images/', 'images/prev_', $current_image->path));
                    $current_image->delete();
                }
            }
        }

        if($image_urls_for_delete){
            foreach ($image_urls_for_delete as $image_url_for_delete){
                $removeStr = $request->root() . '/storage/';
                $path = str_replace($removeStr, '', $image_url_for_delete);
                Storage::disk('public')->delete($path);
            }
        }


        if($images){
            foreach ($images as $image){
                $name = md5(Carbon::now() . '_' . $image->getClientOriginalName()) . '.' . $image->getClientOriginalExtension();
                $file_path = Storage::disk('public')->putFileAs('/images', $image, $name);
                $preview_name = 'prev_' . $name;

                Image::create([
                    'path' => $file_path,
                    'name' => $image->getClientOriginalName(),
                    'url' => url('/storage/' . $file_path),
                    'preview_url' => url('/storage/images/' . $preview_name),
                    'post_id' => $post->id
                ]);

                \Intervention\Image\Facades\Image::make($image)->fit(100, 100)
                    ->save(storage_path('app/public/images/' . $preview_name));
            }
        }



        return response()->json(['message' => 'success']);
    }
}

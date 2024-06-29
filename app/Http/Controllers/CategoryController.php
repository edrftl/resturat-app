<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class CategoryController extends Controller
{
    protected string $upload;
    protected array $sizes = [50, 150, 300, 600, 1200];

    public function __construct()
    {
        $this->upload = env('UPLOAD_DIR', 'uploads/categories/');
    }

    public function index()
    {
        $data = Category::all();
        return response()->json($data)
            ->header('Content-Type', 'application/json; charset=utf-8');
    }

    public function store(Request $request)
    {
        $dir = public_path($this->upload);
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }

        if ($request->hasFile('image') && $request->input("name") != "") {
            $file = $request->file("image");
            $fileName = $this->saveImage($file);
            $item = Category::create(['name' => $request->input("name"), 'image' => $fileName]);
            return response()->json($item, 201);
        }
        return response()->json("Bad request", 400);
    }


    public function update(Request $request, $id) {
        $item = Category::find($id);
        if($request->input("name")!="") {
            if($request->hasFile('image')) {
                $this->destroy($id);
                $file = $request->file("image");
                $item->image = $this->saveImage($file);
            }
            $item->name = $request->input("name");
            $item->save();
            return response()->json($item,200);
        }
        return response()->json("Bad request", 400);
    }


    protected function saveImage(UploadedFile $file) {
        $fileName = uniqid(). ".webp";
        $manager = new ImageManager(new Driver());
        foreach ($this->sizes as $size) {
            $imageSave = $manager->read($file);
            $imageSave->scale(width: $size);
            $path = public_path($this->upload.$size."_".$fileName);
            $imageSave->toWebp()->save($path);
        }
        return $fileName;
    }

//    public function update(Request $request, $id)
//    {
//        $request->validate([
//            'name' => 'required|max:255',
//            'image' => 'mimes:jpeg,jpg,png|max:2048'
//        ]);
//
//        $category = Category::findOrFail($id);
//
//        // Update the name if provided
//        if ($request->has('name')) {
//            $category->name = $request->input('name');
//        }
//
//        // Directory where images are stored
//        $dir = public_path($this->upload);
//
//        // Check if a new image file is provided
//        if ($request->hasFile('image')) {
//            // Delete old image files
//            foreach ($this->sizes as $size) {
//                $path = public_path($this->upload . $size . '_' . $category->image);
//                if (file_exists($path)) {
//                    unlink($path);
//                }
//            }
//
//            // Save new image files
//            $file = $request->file("image");
//            $fileName = uniqid() . ".webp";
//            $manager = new ImageManager(new Driver());
//
//            foreach ($this->sizes as $size) {
//                $imageSave = $manager->read($file);
//                $imageSave->scale(width: $size);
//                $path = public_path($this->upload . $size . "_" . $fileName);
//                $imageSave->toWebp()->save($path);
//            }
//
//            // Update the image filename in the database
//            $category->image = $fileName;
//        }
//
//        // Save the changes to the database
//        $category->save();
//
//        // Return the updated category as a JSON response
//        return response()->json($category);
//    }



//    public function update(Request $request, $id)
//    {
//        $category = Category::findOrFail($id);
//
//        // Update the name if provided
//        if ($request->has('name')) {
//            $category->name = $request->input('name');
//        }
//
//        // Directory where images are stored
//        $dir = public_path($this->upload);
//
//        // Check if a new image file is provided
//        if ($request->hasFile('image')) {
//            // Delete old image files
//            foreach ($this->sizes as $size) {
//                $path = public_path($this->upload . $size . '_' . $category->image);
//                if (file_exists($path)) {
//                    unlink($path);
//                }
//            }
//
//            // Save new image files
//            $file = $request->file("image");
//            $fileName = uniqid() . ".webp";
//            $manager = new ImageManager(new Driver());
//
//            foreach ($this->sizes as $size) {
//                $imageSave = $manager->read($file);
//                $imageSave->scale(width: $size);
//                $path = public_path($this->upload . $size . "_" . $fileName);
//                $imageSave->toWebp()->save($path);
//            }
//
//            // Update the image filename in the database
//            $category->image = $fileName;
//        }
//
//        // Save the changes to the database
//        $category->save();
//
//        // Return the updated category as a JSON response
//        return response()->json($category);
//    }






    public function destroy(int $id)
    {
        $category = Category::findOrFail($id);

        // Delete associated image files
        foreach ($this->sizes as $size) {
            $path = public_path($this->upload . $size . '_' . $category->image);
            if (file_exists($path)) {
                unlink($path);
            }
        }

        $category->delete();

        return response()->json(['message' => 'Category and associated images deleted successfully']);
    }
}

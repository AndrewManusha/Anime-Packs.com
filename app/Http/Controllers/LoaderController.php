<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pack;

class LoaderController extends Controller
{
    public function index()
    {
        return view('admin.pack-loader');
    }
    
    public function load(Request $request)
    {
        // Валидация
        $validated = $request->validate([
            'file' => ['required', 'file', 'mimes:zip', 'max:51200'],
            'type' => ['required', 'in:resource,mod,shader'],
            'title' => ['required', 'string', 'max:50'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.name' => ['required', 'string', 'max:255'],
            'items.*.item' => ['required', 'string', 'max:255'],
            'video' => ['required', 'url'],
            'franchise' => ['required', 'string', 'max:50'],
            'categories' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:1000'],
            'meta_description' => ['required', 'string', 'max:160'],
            'images' => ['required', 'array', 'min:1'],
            'images.*.file' => ['required', 'file', 'mimes:jpeg,png,webp', 'max:5120'],
            'images.*.desc' => ['required', 'string', 'max:255'],
        ]);
        
        if (!$request->hasFile('file')) {
            return response()->json(['error' => 'ZIP файл не загружен'], 400);
        }

        $zipFile = $request->file('file');

        // Формируем "чистое" имя для папки и файла
        $franchise = preg_replace('/[^a-z0-9\-]/', '', str_replace(' ', '-', strtolower($validated['franchise'])));
        $baseName = preg_replace('/[^a-z0-9\-]/', '', str_replace([' ', '_'], '-', strtolower(pathinfo($zipFile->getClientOriginalName(), PATHINFO_FILENAME))));
        $fileName = $baseName . '.' . $zipFile->getClientOriginalExtension();

        $path = 'public/resource-pack/' . $franchise . '/' . $baseName;

        // Сохраняем ZIP
        $zipFile->storeAs($path, $fileName, 'local');

        // Сохраняем изображения
        $images = $validated['images'];
        foreach ($images as &$image) {
            if (isset($image['file']) && $image['file']->isValid()) {
                $imgFilename = uniqid('img_') . '.' . $image['file']->getClientOriginalExtension();
                $image['file']->storeAs($path, $imgFilename, 'local');
                $image['file'] = $imgFilename; // заменяем объект файла на имя
            }
        }
        unset($image);

        // Записываем в базу
        $pack = new Pack();
        $pack->page_url = $path;
        $pack->type = $validated['type'];
        $pack->title = $validated['title'];
        $pack->file = $fileName;
        $pack->items = json_encode($validated['items']);
        $pack->video = parse_url($validated['video'], PHP_URL_QUERY) ? 
    (parse_str(parse_url($validated['video'], PHP_URL_QUERY), $q) ? $q['v'] ?? '' : '') : '';

        $pack->image = json_encode($images);
        $pack->franchise = $validated['franchise'];
        $pack->category = $validated['categories'];
        $pack->description = $validated['description'];
        $pack->min_desc = $validated['meta_description'];
        $pack->save();

        return response()->json(['message' => 'Пак успешно загружен']);
    }
}

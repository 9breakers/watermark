<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;

class ImageController extends Controller
{
    public function index()
    {
        return view('index');
    }


    public function upload(Request $request)
    {
        $images = $request->file('images');

        if ($images === null) {
            return redirect()->back()->withErrors(['images' => 'Please select at least one image to upload.']);
        }

        foreach ($images as $image) {
            $this->validate($request, [
                'images.*' => 'required|image', // Перевірка на тип файлу
            ]);

            $imageName = time() . '_' . $image->getClientOriginalName();
            $watermarkPath = public_path('watermark.png'); // Замість 'watermark.png' вкажіть шлях до свого водяного знаку

            $img = Image::make($image);
            $imgWidth = $img->width();
            $imgHeight = $img->height();

            // Завантажте водяний знак та отримайте його розмір
            $watermark = Image::make($watermarkPath);
            $watermarkWidth = $watermark->width();
            $watermarkHeight = $watermark->height();

            // Встановлюємо розмір водяного знаку пропорційно до розміру зображення
            $watermark->resize($imgWidth / 3, null, function ($constraint) {
                $constraint->aspectRatio();
            });

            // Налаштовуємо прозорість водяного знаку
            $watermark->opacity(45); // Змініть значення (0-100) для регулювання прозорості

            // Додаємо водяний знак
            $img->insert($watermark, 'top-left', 10, 10);

            // Зберігаємо зображення з водяним знаком
            $img->save(public_path('uploads/' . $imageName));
        }

        return redirect()->back()->with('success', 'Зображення були успішно оброблені та збережені.');
    }




}

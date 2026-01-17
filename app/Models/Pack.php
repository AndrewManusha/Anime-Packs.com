<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pack extends Model
{
    use HasFactory;
    
    protected $table = 'packs';
    protected $fillable = ['page_url', 'type', 'title', 'file', 'items', 'video', 'image', 'franchise', 'category', 'description', 'min_desc', 'created_at', 'updated_at', 'status']; // добавь все поля, которые реально используешь

    // Указываем, что primary key — это page_url
    protected $primaryKey = 'page_url';

    // Он не автоинкрементный (строковый)
    public $incrementing = false;

    // Тип ключа — строка
    protected $keyType = 'string';
}

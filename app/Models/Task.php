<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;


class Task extends Model
{
    use HasFactory;
    /**
     *
     * @mixin Builder
     */
    public $timestamps = false;
    protected $fillable = [
        'title',
        'deadline',
        'description',
        'is_ready'
    ];
    protected $casts = [
        'deadline' => 'datetime',
        'is_ready' => 'bool'
    ];
}

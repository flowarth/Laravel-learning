<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Submission extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'assigment_id',
        'student_id',
        'file_path',
        'score'
    ];

    public function assigment()
    {
        return $this->belongsTo(Assigment::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RelevantDocumentType extends Model
{
    use HasFactory;

    protected $table = 'relevant_document_types';

    protected $fillable = ['name', 'description', 'domain_id', 'lang_code', 'status'];

    protected $hidden = ['created_at', 'updated_at'];

    public function domain()
    {
        return $this->belongsTo(Domain::class, 'domain_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contact extends Model
{
    protected $table = "contacts";
    protected $keyType = "int";
    protected $primaryKey = "id";
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone'
    ];

    public function user():BelongsTo 
    {
        return $this->belongsTo(Contact::class, "user_id", "id");
    }
}

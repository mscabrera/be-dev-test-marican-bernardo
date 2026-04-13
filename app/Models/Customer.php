<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Customer.
 * 
 * Has all the data for the customers table.
 */ 
class Customer extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'customers';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'gender',
        'ip',
        'company',
        'city',
        'title',
        'website',
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];
}

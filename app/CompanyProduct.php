<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompanyProduct extends Model
{
    protected $fillable = [];
    protected $primaryKey = 'id';
    protected $table = 'company_products';
}

<?php
namespace App;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model {

    protected $fillable = [];
    protected $primaryKey = 'id';
    protected $table = 'customers';
}

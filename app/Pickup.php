<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pickup extends Model
{
    //
    protected $fillable = [];
    protected $primaryKey = 'id';
    protected $table = 'pickups';
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    public function pickupdocument()
    {
        return $this->hasMany(Pickup_Document::class, 'pickup_id', 'id')->select(array('pickup_id','document_name', 'question','sequence','comments'));
    }

}

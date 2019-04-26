<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PickupDocument extends Model
{
    //
    protected $fillable = [];
    protected $primaryKey = 'id';
    protected $table = 'pickup_documents';

    public function clientsdocument()
    {
        return $this->belongsTo('App\Pickup', 'pickup_id', 'id')->select('id');
    }

}

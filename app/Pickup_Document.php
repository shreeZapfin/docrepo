<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pickup_Document extends Model
{
    //
    protected $fillable = [];
    protected $primaryKey = 'id';
    protected $table = 'pickup_documents';

    public function clientsdocument()
    {
        return $this->belongsTo('App\Pickup', 'pickup_id', 'id')->select('id');
    }

    public function documentPictures()
    {
        return $this->hasMany(Pickup_document_Pictures::class, 'pickup_document_id', 'id')->select(array('pickup_document_id','filename', 'latitude','longitude'));
    }

}

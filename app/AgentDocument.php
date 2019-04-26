<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AgentDocument extends Model
{
    //Relation to Topics
    protected $fillable = [];
    protected $primaryKey = 'id';
    protected $table = 'agent_documents';
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AgentTransaction extends Model
{
    protected $fillable = [];
    protected $primaryKey = 'id';
    protected $table = 'agent_transactions';
}


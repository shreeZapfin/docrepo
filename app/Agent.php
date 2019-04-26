<?php
namespace App;
use Illuminate\Database\Eloquent\Model;

class Agent extends Model {

    protected $fillable = [];
    protected $primaryKey = 'id';
    protected $table = 'agents';

    public function agentdocument()
    {
        return $this->hasMany(AgentDocument::class, 'agent_id', 'id')->select(array('agent_id', 'type', 'filename'));
    }

    public function agenteducation()
    {
        return $this->hasMany(AgentEducation::class, 'agent_id', 'id')->select(array('agent_id', 'degree', 'college', 'year'));
    }


    public function  agentbankdetails(){
        return $this->hasMany(AgentBankdetails::class, 'agent_id', 'id')->select(array('agent_id', 'bank_name', 'ifsc_code', 'account_number','account_type'));
    }
}

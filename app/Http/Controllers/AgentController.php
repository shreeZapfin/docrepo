<?php

namespace App\Http\Controllers;

use App\Agent;
use App\Http\Requests;
use App\WebmasterSection;
use Auth;
use File;
use Helper;
use Illuminate\Config;
use Yajra\DataTables\DataTables;
use Illuminate\Http\Request;
use Redirect;

class AgentController extends Controller
{

    private $uploadPath = "uploads/contacts/";
    // Define Default Variables
    public function __construct()
    {
        $this->middleware('auth');
        // Check Permissions
        if (!@Auth::user()->permissionsGroup->newsletter_status) {
            return Redirect::to(route('NoPermission'))->send();
        }
    }

    //Agent Index Page
    public function index()
    {
        // General for all pages
        $GeneralWebmasterSections = WebmasterSection::where('status', '=', '1')->orderby('row_no', 'asc')->get();
        return view('backEnd.agents.index', compact('GeneralWebmasterSections'));
    }


    // Display Agent Data
    public function data(Request $request){
        $status = $request->get('status');
        if($status != -1 && $request->get('status')!=null){
            $agent_data = Agent::where('status',$status)
            ->get(array('id','name','address1','city','mobile','status','wallet_amt','email'));
        }else{
            $agent_data = Agent::get(array('id','name','address1','city','mobile','status','wallet_amt','email'));
        }
        return Datatables::of($agent_data)
            ->addColumn('checkbox',function($agent_data) {
                $checkbox= '<label class="ui-check m-a-0"><input type="checkbox" name="ids[]" class="checkBoxClass" value='.$agent_data->id.'><i class="dark-white"></i>
                                <input type="hidden" name="row_ids[]" value='.$agent_data->id.' /></label>';
                return $checkbox;
            })
            ->addColumn('actions',function($agent_data) {
                $actions =  ' <a class="btn btn-sm success" href='. route('agents.show', $agent_data->id) .'><small> View </small></a>';
                return $actions;
            })
            ->editColumn('wallet_amt',function($agent_data) {
                $price ='<i class="fa fa-inr fa-fw" aria-hidden="true"></i><span id="total">'.$agent_data->wallet_amt;
                return $price;
            })
            ->rawColumns(['actions', 'station','wallet_amt','checkbox'])
            ->make(true);
    }

    //display Agent Profile
    public function show($id){
        //Find Agent
        $agent_profile = Agent::with('agentdocument','agenteducation','agentbankdetails')->where('id',$id)->first();
        if($agent_profile !=null){
            // General for all pages
            $GeneralWebmasterSections = WebmasterSection::where('status', '=', '1')->orderby('row_no', 'asc')->get();

            return view('backEnd.agents.show',compact('GeneralWebmasterSections','agent_profile'));
        }else{
            return Redirect::to(route('agents'));
        }
    }

   public function Approve_agent(Request $request){
       $agent_id = $request->get('agent_id');
       //Find Agent
       $agent = Agent::where('id',$agent_id)->first();
       if($agent->status == 'Pending' || $agent->status == 'Inactive'){
           $agent->status = 'Approved';
           $agent->save();
           $data['agent_status'] = $agent->status;
           $data['status'] = 1;
           $data['message'] = 'Agent Approved Succesfully';
           return json_encode($data);
       }else{
           $agent->status = 'Inactive';
           $agent->save();
           $data['agent_status'] = $agent->status;
           $data['status'] = 0;
           $data['message'] = 'Agent Inactive Succesfully';
           return json_encode($data);
       }

    }
    public function updateStatus(Request $request){
        if($request->ids != "") {
            $status = $request->get('action');
            Agent::wherein('id', $request->ids)->update(['status' => $status]);
        }
        return redirect()->action('AgentController@index')->with('doneMessage', trans('backLang.saveDone'));

    }


}
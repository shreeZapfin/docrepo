<?php

namespace App\Http\Controllers;

use App\Contact;
use App\ContactsGroup;
use App\Country;
use App\Customer;
use App\Document;
use App\Document_type_details;
use App\Document_type_master;
use App\Http\Requests;
use App\Pickup;
use App\WebmasterSection;
use Auth;
use File;
use Helper;
use Illuminate\Config;
use Yajra\DataTables\DataTables;
use Illuminate\Http\Request;
use Redirect;
class JobController extends Controller
{

    // Define Default Variables
    public function __construct()
    {
        $this->middleware('auth');
        // Check Permissions
        if (!@Auth::user()->permissionsGroup->newsletter_status) {
            return Redirect::to(route('NoPermission'))->send();
        }
    }

    //Display Job Index Page
    public function index()
    {
        // General for all pages
        $GeneralWebmasterSections = WebmasterSection::where('status', '=', '1')->orderby('row_no', 'asc')->get();
        $jobs = Document_type_master::get();
        $companies = Customer::get();
        return view('backEnd.jobs.index', compact('GeneralWebmasterSections','jobs','companies'));
    }

    //Display Jobs Data
    public function data(Request $request){
        $job_id = $request->get('job_id');
        $company_id = $request->get('company_id');
        $query = Document_type_master::join('document_type_details','document_type_details.job_id','document_type_master.id')
            ->join('customers','customers.id','document_type_details.customer_id')->groupBy('document_type_details.job_id');
        if($job_id != -1 && $job_id !=null){
            $query->where('document_type_master.id',$job_id);
        }
        if($company_id != -1 && $company_id !=null){
            $query->where('customers.id',$company_id);
        }
        $jobs_data = $query->get(array('document_type_master.id as job_id','document_type_master.name as job','document_type_master.id as job_id','customers.id as customer_id','customers.company_name','customers.mobile','document_type_master.price','document_type_details.id as document_id'));

        return Datatables::of($jobs_data)

             ->editColumn('document',function($jobs_data) {
                 $item_list = '';
                 $records = Document_type_details::join('documents','documents.id','document_type_details.document_id')
                    ->where('document_type_details.job_id', $jobs_data->job_id)
                     ->get(array('documents.name as document_name'));
                 $item_list .=count($records);
                 return $item_list;
             })
            ->editColumn('price',function($jobs_data) {
                $price ='<i class="fa fa-inr fa-fw" aria-hidden="true"></i><span id="total">'.$jobs_data->price;
                return $price;
            })
            ->addColumn('actions',function( $jobs_data) {
                $actions =  ' <a class="btn btn-sm success" href='. route('jobs.show', $jobs_data->job_id) .'><small>View </small></a>';
                return $actions;
            })
            ->rawColumns(['actions', 'document','price'])
            ->make(true);
    }

    public function show($id){
        $job_profile = Document_type_master::join('document_type_details','document_type_details.job_id','document_type_master.id')
            ->join('customers','customers.id','document_type_details.customer_id')
            ->join('users','users.id','document_type_master.created_by')
            ->where('document_type_master.id',$id)->groupBy('document_type_details.job_id')->first(array('users.name as user_name','document_type_master.*','customers.name as customer_name'));
        $details =  Document_type_master::join('document_type_details','document_type_details.job_id','document_type_master.id')
            ->join('customers','customers.id','document_type_details.customer_id')
            ->leftjoin('documents','documents.id','document_type_details.document_id')
            ->where('document_type_master.id',$id)
             ->groupBy('document_type_details.question')
            ->orderBy('document_type_details.sequence','asc')
            ->get(array('customers.name as customer_name','document_type_details.question','documents.name as document_name','document_type_details.sequence'));
        $job_profile->job_details = $details;
        return view('backEnd.jobs.show',compact('job_profile'));
    }
}
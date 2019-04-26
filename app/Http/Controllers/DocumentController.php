<?php

namespace App\Http\Controllers;

use App\Contact;
use App\ContactsGroup;
use App\Country;
use App\Customer;
use App\Document;
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
class DocumentController extends Controller
{

    // Define Default Variables
    public function __construct()
    {
        $this->middleware('auth');
        // Check Permissions
        if (!@Auth::user()->permissionsGroup->newsletter_status) {
            return Redirect::to(route('admin.NoPermission'))->send();
        }
    }
    //Display Document Index Page
    public function index(){
        // General for all pages
        $GeneralWebmasterSections = WebmasterSection::where('status', '=', '1')->orderby('row_no', 'asc')->get();
        return view('backEnd.documents.index',compact('GeneralWebmasterSections'));
    }
    //Display Documents Data
    public function data(){
        $document_data = Document::join('users','users.id','documents.created_by')->get(array('documents.id','documents.name','users.name as user_name'));
        return Datatables::of($document_data)
            ->rawColumns(['actions', 'station'])
            ->make(true);
    }

    public  function import(){
        // General for all pages
        $GeneralWebmasterSections = WebmasterSection::where('status', '=', '1')->orderby('row_no', 'asc')->get();
        return view('backEnd.documents.import_document',compact('GeneralWebmasterSections'));
    }

}
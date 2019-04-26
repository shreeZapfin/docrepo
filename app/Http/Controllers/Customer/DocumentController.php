<?php

namespace App\Http\Controllers\Customer;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Document;

use Illuminate\Support\Facades\Redirect;
use Yajra\DataTables\DataTables;


class DocumentController extends Controller
{
    public function index(){
        return view('customer.documents.index');
    }
    public function data(){
        $document_data = Document::join('document_type_details','document_type_details.document_id','documents.id')
            ->join('document_type_master','document_type_master.id','document_type_details.job_id')
            ->join('customers','customers.id','document_type_details.customer_id')
            ->where('document_type_details.customer_id',session()->get('customer_id'))
            ->get(array('documents.id','documents.name as document_name','documents.created_by','customers.name as customer_name','document_type_master.name','document_type_master.price'));
        return Datatables::of($document_data)

            ->rawColumns(['actions', 'station'])
            ->make(true);
    }
    public function show(){
        return view('customer.documents.show');
    }
}
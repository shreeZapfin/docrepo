<?php

namespace App\Http\Controllers;

use App\Contact;
use App\ContactsGroup;
use App\Country;
use App\Customer;
use App\Http\Requests;
use App\Pickup;
use App\WebmasterSection;
use Auth;
use File;
use Helper;
use Illuminate\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Excel;

use Yajra\DataTables\DataTables;
use Illuminate\Http\Request;
use Redirect;

class CustomerController extends Controller
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
    //Show Customer Index Page
    public  function  index(){
        // General for all pages

        $GeneralWebmasterSections = WebmasterSection::where('status', '=', '1')->orderby('row_no', 'asc')->get();
        return view('backEnd.customers.index',compact('GeneralWebmasterSections'));
    }
    // Display Customer Data
    public function data(){
        $customer_data = Customer::get(array('id','name','company_name','email','status','mobile'));
        return Datatables::of($customer_data)
            ->addColumn('actions',function( Customer $customer_data) {
                $actions =  ' <a class="btn btn-sm success" href='. route('customers.show', $customer_data->id) .'><small>View </small></a>';
                return $actions;
            })
            ->rawColumns(['actions', 'station'])
            ->make(true);
    }
    // Display Customer Profile
    public function show($id){
        $customer_profile = Customer::where('id',$id)->first();
        //Find Customer
        if($customer_profile !=null){
            // General for all pages
            $GeneralWebmasterSections = WebmasterSection::where('status', '=', '1')->orderby('row_no', 'asc')->get();
            return view('backEnd.customers.show',compact('GeneralWebmasterSections','customer_profile'));
        }else{
            return Redirect::to(route('customers'));
        }
    }
    //Import Customer Excel
    public function import(){
        // General for all pages
        $excel_data['success'] = null;
        $excel_data['failed'] = null;
        $data = null;
        $GeneralWebmasterSections = WebmasterSection::where('status', '=', '1')->orderby('row_no', 'asc')->get();
        return view('backEnd.customers.import', compact('GeneralWebmasterSections','excel_data','data'));

    }

    //Save Customers Excel
    public function saveExcel(Requests\CustomerImportRequest $request)
    {
        $success_list = [];
        $failed_list = [];
        DB::beginTransaction();
        try{

            if(Input::hasFile('file')){
                $path = Input::file('file')->getRealPath();
                $data = Excel::load($path, function($reader) {
                })->get();
                $customer_record = array();
                $customers = array();
                $update_customers = array();
                $isHavingDuplicate = 0;

                if(!empty($data) && $data->count()){

                    if(isset($data[0]['email']))
                    {

                        foreach ($data as $key => $value) {

                            if($value->email != NULL)
                            {
                                unset($customer_record);
                                $email = $value->email;
                                $customer_record['name'] = $value->name;
                                $customer_record['mobile'] = $value->mobile;
                                $customer_record['photo'] = $value->photo;
                                $customer_record['email'] = $value->email;
                                $customer_record['company_name'] = $value->company_name;
                                $customer_record['password'] = $value->password;
                                $customer_record = (object)$customer_record;
                                $failed_length = count($failed_list);
                                if($this->isRecordExists($email, 'email'))
                                {
                                    $isHavingDuplicate = 1;
                                    $temp = array();
                                    $temp['record'] = $customer_record;
                                    $temp['type'] ='Record already exists with this email';
                                    $failed_list[$failed_length] = (object)$temp;
                                    $update_customers = $customer_record;
                                    continue;
                                }
                                $customers[] = $customer_record;
                            }
                        }
                    } else {

                        return Redirect::route('customers.import')->with('error', 'email_address field not included in file');
                    }

                    if($this->addCustomer($customers)){
                        $success_list = $customers;
                        DB::commit();
                    }

                } else {
                    return Redirect::route('customers.import')->with('error', 'Uploaded file is empty');
                }
            }
            $excel_data['success'] = count($customers);
            $excel_data['updated'] = count($update_customers);
            $excel_data['failed'] = count($failed_list);;

            $data['success'] = $this->excel_data['success'] = $success_list;
            $data['failed'] = $this->excel_data['failed'] = $failed_list;
            // General for all pages
            $GeneralWebmasterSections = WebmasterSection::where('status', '=', '1')->orderby('row_no', 'asc')->get();
            return view('backEnd.customers.import',compact('excel_data','data','GeneralWebmasterSections'))->with('success', 'Customers Uploaded Successfully');


        }
        catch( \Illuminate\Database\QueryException $e)
        {
            DB::rollBack();
            return Redirect::route('customers.import')->with('error', 'Customers Import Failed. Please try again.');
        }


    }

    public function isRecordExists($record_value, $type='email')
    {
        return Customer::where($type,'=',$record_value)->count();
    }

    public function addCustomer($customers)
    {

        foreach($customers as $request) {
            $customer  = new Customer();
            $customer->name = $request->name;
            $customer->photo = $request->photo;
            $customer->mobile = $request->mobile;
            $customer->company_name = $request->company_name;
            $customer->email = $request->email;
            $customer->password = $request->password;
            $customer->created_by =  Auth::user()->id;
            $customer->status ='Active';
            $customer->save();
        }
        return true;
    }
    public function downloadExcel(Request $request)
    {
        $Data = \GuzzleHttp\json_decode($request->get('data'));
        $formData= json_decode( json_encode($Data), true);
        return Excel::create('pickup_report', function ($excel) use ($formData) {
            $excel->sheet('Updated', function ($sheet) use ($formData) {
                $sheet->row(1, array('Reason','name','photo','email','company_name','mobile'));
                $cnt = 2;
                if(count($formData['failed'] >0)){
                    foreach ($formData['failed'] as $data_item) {
                        $item = $data_item['record'];
                        $sheet->appendRow($cnt++, array($data_item['type'],
                            $item['name'],$item['photo'],$item['email'],$item['company_name'],$item['mobile']));
                    }
                }
            });
            $excel->sheet('Success', function ($sheet) use ($formData) {
                $sheet->row(1, array('name','photo','email','company_name','mobile'));
                $cnt = 2;

                foreach ($formData['success'] as $data_item) {
                    $item = $data_item;
                    $sheet->appendRow($cnt++, array(
                        $item['name'],$item['photo'],$item['email'],$item['company_name'],$item['mobile']));
                }

            });

        })->download('xlsx');

    }



}

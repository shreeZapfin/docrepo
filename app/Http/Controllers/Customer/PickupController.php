<?php



namespace App\Http\Controllers\Customer;

use App\Customer;
use App\Http\Controllers\Controller;

use App\Http\Requests;

use App\Mail\CustomerPickupMail;
use App\Pickup;

use App\Pickup_document_Pictures;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;

use Illuminate\Http\Request;

use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\DataTables;





class PickupController extends Controller

{

    public function index(){
        $cities = Pickup::where('customer_id',session()->get('customer')->customer_id)->groupBy('city')->pluck('city');
        if($cities->isEmpty()){
            $cities = Pickup::groupBy('city')->pluck('city');
            $city_names = $cities;
        }else{
            $city_names = $cities;
        }
        $companies = Customer::where('id',session()->get('customer')->customer_id)->first(array('id','company_name','email','email_cc'));
        if($companies == null){
            $companies = Customer::get(array('id','company_name','email','email_cc'));
            if($companies!= null){
                $company_names = $companies;
            }
        }else{
            $company_names = $companies;
        }
        return view('customer.pickups.index',compact('city_names','company_names'));

    }



    public function data(Request $request){

        $query = Pickup::join('customers','customers.id','pickups.customer_id')

        ->leftjoin('agents','agents.id','pickups.agent_id')

        ->join('company_products','company_products.id','pickups.product_id');

        if(session()->get('customer')->customer_id != -1){
            $query->where('customer_id',session()->get('customer')->customer_id);
        }


//        filter by status code
        if($request->get('status') != null ){
            if($request->get('status') !=-1 ) {
                $query->where('pickups.status', $request->get('status'));
            }
        }

        if($request->get('city')!= null){
            if($request->get('city') !=-1 ) {
                $query->where('pickups.city', $request->get('city'));
            }
        }

        if($request->get('company')!= null){
            if($request->get('company') !=-1 ) {
                $query->where('pickups.customer_id', $request->get('company'));
            }
        }

        if ($request->get('daterange') != null) {
            $date = explode(' - ', $request->get('daterange'));
            $date1 = Carbon::parse($date[0])->format('Y-m-d');
            $date2 = Carbon::parse($date[1])->format('Y-m-d');
            $query->whereBetween('pickups.pickup_date', [$date1, $date2]);
        }



        $pickup_data = $query->orderBy('id','desc')->get(array('application_id','pickups.id','pickups.pickup_date','pickups.pincode','pickups.status','pickups.pickup_person', 'pickups.city',
            'pickups.price','customers.company_name','agents.name as agent_name','company_products.name as job_title','pickups.latitude'));

        return Datatables::of($pickup_data)
            ->editColumn('pickup_date',function ($pickup_data){
                $pickup_date = Carbon::parse($pickup_data->pickup_date)->format('d-m-Y');
                return  $pickup_date;
            })
            ->addColumn('actions',function($pickup_data) {
                $str = $pickup_data->company_name.'_'.$pickup_data->job_title.'_'.$pickup_data->application_id.'_'.$pickup_data->pickup_person;
                $pdf_file_name = str_replace(' ', '', $str);
                $pickup_pdf_url ="/public/uploads/pickups/pdf/".$pdf_file_name.".pdf";
                $actions = '';
                if ($pickup_data->status == 'Document Submited' || $pickup_data->status == 'Completed' )
                {
                    $actions .='<a class="btn btn-sm primary" title="Pdf" target="new" style="margin-left: 5px;" href="'.\url($pickup_pdf_url).'"><i class="fa fa-file-pdf-o"></i></a>';
                }
                return $actions;
            })
            ->rawColumns(['actions','pickup_date'])

            ->make(true);

    }

    public function allPickupExport (Request $request,$status,$city,$company,$date){

        $query = Pickup::join('customers','customers.id','pickups.customer_id')
            ->leftjoin('agents','agents.id','pickups.agent_id')
            ->join('company_products','company_products.id','pickups.product_id');

        if(session()->get('customer')->customer_id != -1){
            $query->where('customer_id',session()->get('customer')->customer_id);
        }

        if($status != -1 ){
            $query->where('pickups.status',$status);
        }

        if($city != -1 ){
            $query->where('pickups.city',$city);
        }

        if($company != -1 ){
            $query->where('pickups.customer_id',$company);
        }

        if($date != -1 ){
            $daterange = explode(' - ',$date);
            $date1 = Carbon::parse($daterange[0])->format('Y-m-d');
            $date2 = Carbon::parse($daterange[1])->format('Y-m-d');
            $query->whereBetween('pickups.pickup_date', [$date1, $date2]);
        }

        $pickups_details = $query->get(array('application_id','pickups.id','pickups.pickup_date','pickups.pincode','pickups.status','pickups.home_address as address','pickups.pickup_person','pickups.mobile','pickups.preferred_end_time','pickups.preferred_start_time','pickups.city','pickups.state','pickups.latitude',
                'pickups.price','customers.company_name','agents.name as agent_name','company_products.name as job_title','company_products.id as job_id'));

        $exportData = Excel::create('pickup_detail', function($excel) use ($pickups_details) {
            $excel->sheet('pickups', function ($sheet) use ($pickups_details) {
                $sheet->row(1, array('Category Id','Category Name','Application Id', 'Pickup Person', 'Address', 'City', 'State', 'Pincode','Preferred start time','Preferred end time','Mobile','Pickup Date','Cheque Amount','FC (Agent)','Status'));
                $records = $pickups_details;
                $cnt = 2;
                foreach ($records as $item) {
                    $sheet->appendRow($cnt,array($item->job_id,$item->job_title,$item->application_id, $item->pickup_person, $item->address,$item->city,$item->state,$item->pincode, $item->preferred_start_time,$item->preferred_end_time,$item->mobile,$item->pickup_date,$item->latitude,$item->agent_name,$item->status) );
                    $cnt++;
                }
            });
        });

        return $exportData->download('xlsx');
    }

    //PickupExcel Mail Send
    public function MailSend(Request $request){
        //Pickup excel Data For Mail Attachment
        $query = Pickup::join('customers','customers.id','pickups.customer_id')
            ->leftjoin('agents','agents.id','pickups.agent_id')
            ->join('company_products','company_products.id','pickups.product_id');

        if(session()->get('customer')->customer_id != -1){
            $query->where('customer_id',session()->get('customer')->customer_id);
        }


//        filter by status code
        if($request->get('filter_status') != null ){
            if($request->get('filter_status') !=-1 ) {
                $query->where('pickups.status', $request->get('filter_status'));
            }
        }

        if($request->get('filter_city')!= null){
            if($request->get('filter_city') !=-1 ) {
                $query->where('pickups.city', $request->get('filter_city'));
            }
        }

        if($request->get('filter_company')!= null){
            if($request->get('filter_company') !=-1 ) {
                $query->where('pickups.customer_id', $request->get('filter_company'));
            }
        }

        if ($request->get('filter_daterange') != null) {
            $date = explode(' - ', $request->get('filter_daterange'));
            $date1 = Carbon::parse($date[0])->format('Y-m-d');
            $date2 = Carbon::parse($date[1])->format('Y-m-d');
            $query->whereBetween('pickups.pickup_date', [$date1, $date2]);
        }

        $pickups_details = $query->get(array('customers.name as customer_name','application_id','pickups.id','pickups.pickup_date','pickups.pincode','pickups.status','pickups.home_address as address','pickups.pickup_person','pickups.mobile','pickups.preferred_end_time','pickups.preferred_start_time','pickups.city','pickups.state','pickups.latitude',
            'pickups.price','customers.company_name','agents.name as agent_name','company_products.id as job_id','company_products.name as job_title','pickups.latitude'));;

        $exportData = Excel::create('pickup_detail', function($excel) use ($pickups_details) {
            $excel->sheet('pickups', function ($sheet) use ($pickups_details) {
                $sheet->row(1, array('Category Id','category Name','Application Id','Pickup Person', 'Address', 'City', 'State', 'Pincode','Preferred start time','Preferred end time','Mobile','Pickup Date','Cheque Amount','FC (Agent)','Status'));
                $records = $pickups_details;
                $cnt = 2;
                foreach ($records as $item) {
                    $sheet->appendRow($cnt,array($item->job_id,$item->job_title,$item->application_id, $item->pickup_person, $item->address,$item->city,$item->state,$item->pincode, $item->preferred_start_time,$item->preferred_end_time,$item->mobile,$item->pickup_date,$item->latitude,$item->agent_name,$item->status) );
                    $cnt++;
                }
            });
        });



        $data = new \stdClass();
        if($request->get('to_cc') != null){
            $cc_ids =  preg_split ("/\,/",str_replace(' ', '', $request->get('to_cc')));
            foreach($cc_ids as $index =>$cc_id){
                if($cc_id!= Null){
                    $data->to_cc = $cc_id;
                }
            }
        }

        if($request->get('to_bcc') != null){
            $bcc_ids =  preg_split ("/\,/",str_replace(' ', '', $request->get('to_bcc')));
            foreach($bcc_ids as $index =>$bcc_id){
                if($bcc_id!= Null){
                    $data->to_bcc = $bcc_id;
                }
            }
        }
        if($request->get('title') != null) {
            $data->subject_name = $request->get('title');
        }else{
            $data->subject_name = 'Pickup_Excel';
        }
        if($request->get('details') != null){
            $data->details = $request->get('details');
        }
       $data->file = $exportData;

        //Mail To Customer

        $mail_ids = preg_split ("/\,/",str_replace(' ', '', $request->get('emailids')));

        foreach($mail_ids as $mail_id){
            Mail::to($mail_id)
                ->send(new CustomerPickupMail($data));
        }

        if (Mail::failures()) {
            return back()->with('message',' Mail Does Not Sent Succssfully!!');
        }else{
            return back()->with('message',' Mail Sent Succssfully!!');
        }
    }

    public function show(){
        return view('customer.pickups.show');

    }

    public function SetMailId(Request $request){
       $customer = Customer::where('id',$request->customer_id)->first();
        return json_encode($customer);
    }







}
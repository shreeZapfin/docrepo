<?php



namespace App\Http\Controllers;



use App\Contact;

use App\ContactsGroup;

use App\Country;

use App\Customer;

use App\Document;



use App\CompanyProduct;

use App\Http\Requests;

use App\Pickup;
use App\ProductQuestionLinks;
use App\ProductQuestions;

use App\WebmasterSection;

use Auth;

use File;

use Helper;

use Illuminate\Config;

use Yajra\DataTables\DataTables;

use Illuminate\Http\Request;

use Redirect;

class ProductController extends Controller

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

        $products = CompanyProduct::where('company_id',1)->get(array('id','name'));

        $companys = Customer::get(array('id','company_name'));

        return view('backEnd.products.index', compact('GeneralWebmasterSections','products','companys'));

    }



    //Display Jobs Data

    public function data(Request $request){

        $product_id = $request->get('product_id');

        $company_id = $request->get('company_id');

        $query = CompanyProduct::join('customers','customers.id','company_products.company_id');



        if($product_id != -1 && $product_id !=null){

            $query->where('company_products.id',$product_id);

        }

        if($company_id != -1 && $company_id !=null){

            $query->where('customers.id',$company_id);

        }

        $jobs_data = $query->get(array('company_products.id as job_id','customers.company_name','customers.id as company_id','company_products.name','company_products.price','company_products.product_code'));

        return Datatables::of($jobs_data)



             ->editColumn('question_count',function($jobs_data) {

                 $item_list = '';

                 $records = ProductQuestions::join('company_products','company_products.id','product_questions.company_product_id')

                    ->where('product_questions.company_product_id', $jobs_data->job_id)

                     ->get(array('product_questions.question as question_name'));

                 $item_list .=count($records);

                 return $item_list;

             })


            ->editColumn('price',function($jobs_data) {

                $price ='<i class="fa fa-inr fa-fw" aria-hidden="true"></i><span id="total">'.$jobs_data->price;

                return $price;

            })

            ->addColumn('actions',function( $jobs_data) {

                $actions =  ' <a class="btn btn-sm primary" href='. route('products.show_product',[$jobs_data->job_id,$jobs_data->company_id]) .'><small>View </small></a>
                    <a class="btn btn-sm success" href='. route('products.edit', $jobs_data->job_id) .'><small>Edit</small></a>
                    <a class="btn btn-sm warning" data-toggle="modal" title="Delete Product" value="" data-target="#delete_product" onclick="setId('.$jobs_data->job_id.')"><small>Delete</small></a>';
//                href='. route('products.destroy', $jobs_data->job_id) .'
                return $actions;

            })

            ->rawColumns(['actions', 'question_count','price'])

            ->make(true);

    }



    public function showProduct($id,$company_id){

        $job_profile = CompanyProduct::where('company_products.id',$id)
            ->where('company_products.company_id',$company_id)
            ->join('customers','customers.id','company_products.company_id')
            ->join('users','users.id','company_products.created_by')
            ->first(array('users.name as user_name','company_products.*','customers.name as customer_name','customers.company_name'));

        $details =  CompanyProduct::join('product_questions','product_questions.company_product_id','company_products.id')

            ->join('customers','customers.id','company_products.company_id')

            ->where('product_questions.company_product_id',$id)

            ->where('product_questions.company_id',$company_id)

            ->groupBy('product_questions.question')

            ->orderBy('product_questions.sequence','asc')

            ->get(array('customers.name as customer_name','product_questions.question','product_questions.sequence','product_questions.id as question_id','product_questions.question_image'));
       if(count($details) > 0){
           $job_profile->job_details = $details;
           foreach($details as $detail){
              $question_link = ProductQuestionLinks::where('question_id',$detail->question_id)->get();
              $detail->question_link = $question_link;
           }

       }

        return view('backEnd.products.show',compact('job_profile'));

    }
    public function show(){

    }
    public function create(){
        $companys = Customer::get(array('id','company_name'));
        return view('backEnd.products.create',compact('companys'));
    }

    public function store(Request $request){
        $new_product = new CompanyProduct();
        $new_product->name = $request->get('name');
        $new_product->product_code = $request->get('product_code');
        $new_product->company_id = $request->get('company');
        $new_product->price = $request->get('price');
        $new_product->created_by = Auth::user()->id;
        if($new_product->save()){
            return redirect(url('products'))->with('message','Product Save Succesfully');
        }else{
            return redirect(url('products'))->with('message','Product Does Save Succesfully');
        }
    }
    public function edit($id){
        $product = CompanyProduct::where('id',$id)->first();
        $companys = Customer::get(array('id','company_name'));
        return view('backEnd.products.edit',compact('companys','product'));
    }

    public function update($id,Request $request){
        $update_product = CompanyProduct::where('id',$id)->first();
        if($update_product){
            $update_product->name = $request->get('name');
            $update_product->product_code = $request->get('product_code');
            $update_product->company_id = $request->get('company');
            $update_product->price = $request->get('price');
            $update_product->updated_at = Auth::user()->id;
            $update_product->save();
            return redirect(url('products'))->with('message','Product Update Succesfully');
        }else{
            return redirect(url('products'))->with('message','Product Not Available');
        }
    }
    public function delete($id){
        CompanyProduct::where('id',$id)->delete();
        return redirect(url('products'))->with('message', 'Product Delete Successfully.');
    }

}
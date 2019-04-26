<?php

namespace App\Http\Controllers;

use App\CompanyProduct;
use App\Customer;
use App\Http\Requests;
use App\PickupColumn;
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
class QuestionController extends Controller
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

    public function index(Request $request){
        $company_id =  $request->company_id;
        $product_id = $request->product_id;
        $products = CompanyProduct::where('company_id',1)->get(array('id','name'));
        $companys = Customer::get(array('id','company_name'));
        return view('backEnd.questions.index',compact('products','companys','company_id','product_id'));
    }
    public function data(Request $request){
        $product = $request->get('product_id');
        $company_id = $request->get('company_id');
        $query = ProductQuestions::join('company_products','company_products.id','product_questions.company_product_id')
            ->join('customers','customers.id','product_questions.company_id');
            
        if($product !=null && $product != -1){
            $query->where('product_questions.company_product_id',$product);
        }else{
            $query->where('product_questions.company_product_id',$product);
        }
        if($company_id !=null && $company_id != -1){
            $query->where('product_questions.company_id',$company_id);
        }else{
            $query->where('product_questions.company_id',$company_id);
        }
        $query->orderBy('product_questions.sequence','asc');
        $product_data = $query->get(array('product_questions.question_image',
            'product_questions.sequence',
            'product_questions.question',
            'product_questions.company_id',
            'product_questions.id as product_id',
            'company_products.name as product_name',
            'customers.id as customer_id',
            'customers.company_name','customers.mobile','company_products.price'));
        return Datatables::of($product_data)
            ->editColumn('price',function($product_data) {
                $price ='<i class="fa fa-inr fa-fw" aria-hidden="true"></i><span id="total">'.$product_data->price;
                return $price;
            })
            ->editColumn('links',function($product_data) {
                $links = ProductQuestionLinks::where('question_id',$product_data->product_id)->count();
                return $links;
            })
            ->editColumn('question_image',function($product_data){
                if($product_data->question_image == 1){
                    $question_image ='<i class="fa fa-check text-success inline "></i>';
                }else{
                    $question_image ='<i class="fa fa-times text-danger inline"></i>';
                }
                return $question_image;

            })
            ->addColumn('actions',function( $product_data) {
                $actions =  '<a class="btn btn-sm success" id="edit_question" href='. route('questions.edit',$product_data->product_id) .'><small> Edit </small></a>
                 <a class="btn btn-sm warning" title="Delete Question" data-toggle="modal"  data-target="#delete_question"   onclick="setId('.$product_data->product_id.')"><small>Delete </small></a>';
                return $actions;
            })
            ->rawColumns(['actions', 'question','question_image'])
            ->make(true);
    }

    public function create (Request $request){
        
        $company_id = $request->company;
        $product_id = $request->product;
        $companys = Customer::get(array('company_name','id'));
        $products = CompanyProduct::get(array('name','id'));
        $pickup_columns = PickupColumn::get();
        $sequence = ProductQuestions::where('company_id',$company_id)
            ->where('company_product_id',$product_id)
            ->orderBy('sequence','desc')->first(array('sequence'));
$latest_sequence = 1;

         if($sequence!= null){
$latest_sequence = $sequence->sequence + 1;
}
       
        return view('backEnd.questions.create',compact('companys','products','pickup_columns','company_id','product_id','latest_sequence'));
    }

    public function store(Request $request){
        $name = $request->name;
        $link = $request->link;
        $check_sequence = ProductQuestions::where('company_product_id',$request->get('product_id'))
            ->where('company_id',$request->get('customer_id'))
            ->where('sequence',$request->get('sequence'))->first();
        if($check_sequence == null){
            $question = new ProductQuestions();
            $question->company_product_id = $request->get('product_id');
            $question->company_id = $request->get('customer_id');
            $question->question = $request->get('question');
            if(!$request->get('question_image')){
                $question->question_image = 0;
            }else{
                $question->question_image = $request->get('question_image');
            }
            $question->sequence = $request->get('sequence');
            $question->created_by = Auth::user()->id;
            $question->save();
            if(count($name) > 0){
                foreach($name as $key => $item){
                    $documentLinks = new ProductQuestionLinks();
                    $documentLinks->question_id = $question->id;
                    $documentLinks->name = $item;
                    $documentLinks->link = $link[$key];
                    $documentLinks->save();
                }
            }
            return \Illuminate\Support\Facades\Redirect::route('questions',['company_id'=>$request->get('customer_id'),'product_id'=>$request->get('product_id')])->with('success', 'Question Added Successfully');
//            return redirect(url('/questions'))->with('success','Question Add Succesfully');
        }else {
            //Increment Other Question Sequence
            $increment_questions = ProductQuestions::where('company_product_id', $request->get('product_id'))
                ->where('company_id', $request->get('customer_id'))
                ->where('sequence', '>=', $request->get('sequence'))->get();
            foreach ($increment_questions as $increment_question) {
                $increment_question->sequence = $increment_question->sequence + 1;
                $increment_question->save();
            }

            //Add New Question
            $question = new ProductQuestions();
            $question->company_product_id = $request->get('product_id');
            $question->company_id = $request->get('customer_id');
            $question->question = $request->get('question');
            if(!$request->get('question_image')){
                $question->question_image = 0;
            }else{
                $question->question_image = $request->get('question_image');
            }
            $question->sequence = $request->get('sequence');
            $question->created_by = Auth::user()->id;
            $question->save();
            if(count($name) > 0){
                foreach($name as $key => $item){
                    $documentLinks = new ProductQuestionLinks();
                    $documentLinks->question_id = $question->id;
                    $documentLinks->name = $item;
                    $documentLinks->link = $link[$key];
                    $documentLinks->save();
                }
            }
            return redirect(route('questions',['company_id'=>$request->get('customer_id'),'product_id'=>$request->get('product_id')]))->with('success','Question Add Succesfully');
        }

    }

    public function edit(Request $request,$id){

        //edit_question
        $question = ProductQuestions::where('id',$id)->first();
        $company = Customer::where('id',$question->company_id)->first(array('company_name','id'));
        $question->company =  $company;
        $products = CompanyProduct::where('id',$question->company_product_id)->first(array('name','id'));
        $question->product =  $products;
        $pickup_columns = PickupColumn::get();
        $pickup_links = ProductQuestionLinks::where('question_id',$question->id)->get();
        return view('backEnd.questions.edit',compact('question','companys','products','pickup_columns','pickup_links'));
    }
    public function update(Request $request,$id){
        $name = $request->name;
        $link = $request->link;

        $check_question = ProductQuestions::where('id',$id)->first();
        if($check_question!= null){
            $check_sequence = ProductQuestions::where('company_product_id',$request->get('product_id'))
                ->where('company_id',$request->get('customer_id'))
                ->where('sequence',$request->get('sequence'))
                ->where('id','!=',$id)->first();
            if($check_sequence != null){
                     //Increment Other Question Sequence
                     $increment_questions = ProductQuestions::where('company_product_id', $request->get('product_id'))
                         ->where('company_id', $request->get('customer_id'))
                         ->where('sequence', '>=', $request->get('sequence'))->get();
                     foreach ($increment_questions as $increment_question) {
                         $increment_question->sequence = $increment_question->sequence + 1;
                         $increment_question->save();
                     }
                //Update  Question
                $check_question->company_product_id = $request->get('product_id');
                $check_question->company_id = $request->get('customer_id');
                $check_question->question = $request->get('question');
                if(!$request->get('question_image')){
                    $check_question->question_image = 0;
                }else{
                    $check_question->question_image = $request->get('question_image');
                }

                $check_question->sequence = $request->get('sequence');
                $check_question->created_by = Auth::user()->id;
                if($check_question->save()){
                    $removeLinks = ProductQuestionLinks::where('question_id',$check_question->id)->delete();
                    if(count($name) > 0){
                        foreach($name as $key => $item){
                            $documentLinks = new ProductQuestionLinks();
                            $documentLinks->question_id = $check_question->id;
                            $documentLinks->name = $item;
                            $documentLinks->link = $link[$key];
                            $documentLinks->save();
                        }
                    }

                    return redirect(route('questions',['company_id'=>$request->get('customer_id'),'product_id'=>$request->get('product_id')]))->with('success','Question Update Succesfully');
                }else{
                    return redirect(route('questions',['company_id'=>$request->get('customer_id'),'product_id'=>$request->get('product_id')]))->with('error','Question Does Not Update Succesfully');
                }
            }else{
                $check_question->company_product_id = $request->get('product_id');
                $check_question->company_id = $request->get('customer_id');
                $check_question->question = $request->get('question');
                if(!$request->get('question_image')){
                    $check_question->question_image = 0;
                }else{
                    $check_question->question_image = $request->get('question_image');
                }

                $check_question->sequence = $request->get('sequence');
                $check_question->created_by = Auth::user()->id;
                if($check_question->save()){
                    $removeLinks = ProductQuestionLinks::where('question_id',$check_question->id)->delete();
                    if(count($name) > 0){
                        foreach($name as $key => $item){
                            $documentLinks = new ProductQuestionLinks();
                            $documentLinks->question_id = $check_question->id;
                            $documentLinks->name = $item;
                            $documentLinks->link = $link[$key];
                            $documentLinks->save();
                        }
                    }
                    return redirect(route('questions',['company_id'=>$request->get('customer_id'),'product_id'=>$request->get('product_id')]))->with('success','Question Update Succesfully');
                }else{
                    return redirect(route('questions',['company_id'=>$request->get('customer_id'),'product_id'=>$request->get('product_id')]))->with('error','Question Does Not Update Succesfully');
                }
            }
        }else{
            return redirect(route('questions',['company_id'=>$request->get('customer_id'),'product_id'=>$request->get('product_id')]))->with('error','Question Not Found');
        }

    }

    public function delete($id){
        ProductQuestions::where('id',$id)->delete();
        ProductQuestionLinks::where('question_id', $id)->delete();
        return redirect(url('/questions'))->with('success','Question Deleted Succesfully');
    }

    public function show($id){
        return redirect(url('/questions'))->with('success','Question Deleted Succesfully');
    }

    public function getProducts(Request $request){
        $getProduct = CompanyProduct::where('company_id', $request->company_id)->get(array('name','id'));
        return response()->json($getProduct);
    }


}
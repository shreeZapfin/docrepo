<?php

namespace App\Http\Controllers;

use App\Agent;
use App\CompanyProduct;
use App\Contact;
use App\ContactsGroup;
use App\Job;
use App\Mail\documentLinkMail;
use App\Mail\Pickuppdf;
use App\MobileNotification;
use App\Pickup_Document;
use App\PickupColumn;
use App\PickupDocumentLinks;
use App\PickupSchedule;
use App\ProductQuestionLinks;
use App\ProductQuestions;
use Illuminate\Contracts\Session\Session;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use LaravelFCM\Facades\FCM;
use App\Country;
use App\Customer;
use App\Document;
use App\Document_type_details;
use App\Document_type_master;
use App\Http\Requests;
use App\Mail\PickupMail;
use Illuminate\Support\Facades\Response;
use App\Notifications\PickupAddNotification;
use App\Pickup;
use App\PickupDocument;
use App\PickupDocumentPictures;
use App\User;
use App\WebmasterSection;
use Auth;
use Illuminate\Support\Facades\URL;
use Mail;
use Carbon\Carbon;
use File;
use Helper;
use Illuminate\Config;
use Illuminate\Support\Facades\DB;
use App\Notifications\PickupNotification;
use Illuminate\Support\Facades\Input;
use Excel;
use Psy\Util\Str;
use Yajra\DataTables\DataTables;
use Illuminate\Http\Request;
use Redirect;
use Barryvdh\DomPDF\Facade as PDF;
use Image;

class PickupController extends Controller {

    // Define Default Variables
    public function __construct() {
        $this->middleware('auth');
        // Check Permissions
        if (!@Auth::user()->permissionsGroup->newsletter_status) {
            return Redirect::to(route('NoPermission'))->send();
        }
    }

    //Display Active Pickup Page
    public function index(Request $request) {

        if ($request->has('status'))
            $status = $request->status;
        else
            $status = null;
        session(['document' => '0']);
        $agents = Agent::get();

// General for all pages
        $GeneralWebmasterSections = WebmasterSection::where('status', '=', '1')->orderby('row_no', 'asc')->get();
        $getCustomersList = Customer::select('id', 'name', 'company_name')->orderBy('id')->get()->toArray();
        $getProductList = CompanyProduct::select('id', 'name', 'company_id')->orderBy('id')->get()->toArray();
        return view('backEnd.pickups.index', compact('GeneralWebmasterSections', 'getProductList', 'getCustomersList', 'agents', 'status'));
    }

    //get Product List based on particular company name
    public function getProductListByCompanyId($customerDropDownId) {
        $getProductsList = CompanyProduct::all()->where('company_id', $customerDropDownId)->pluck('name', 'id')->toArray();
        return response($getProductsList);
    }

    public function data(Request $request) {
        $status = $request->get('status');
        $company_name = $request->get('company_name');
        $product_name = $request->get('product_name');
//        echo'comes<pre>';print_r($product_name);exit;
        $query = Pickup::join('customers', 'customers.id', 'pickups.customer_id')
                ->leftjoin('agents', 'agents.id', 'pickups.agent_id')
                ->leftjoin('customers as customer', function($join) {
                    $join->on('customer.id', '=', 'pickups.customer_id');
                })
                ->join('company_products', 'company_products.id', 'pickups.product_id')
                ->join('users', 'users.id', 'pickups.created_by');
//filter by status code
        if ($request->get('status') != null) {
            if ($request->get('status') != -1) {
                $query->where('pickups.status', $status);
            }
        }
        if ($request->get('company_name') != null) {
            if ($request->get('company_name') != -1) {
                $query->where('pickups.customer_id', $company_name);
            }
        }
        if ($request->get('product_name') != null) {
            if ($request->get('product_name') != -1) {
                $query->where('pickups.product_id', $product_name);
            }
        }

        if ($request->get(''))
            if ($request->get('status_type') != '') {
                $query->where('pickups.status', $request->get('status_type'));
            }

//Filter by DateRange
        if ($request->get('daterange') != null) {
            $date = explode(' - ', $request->get('daterange'));
            $date1 = Carbon::parse($date[0])->format('Y-m-d');
            $date2 = Carbon::parse($date[1])->format('Y-m-d');
            $query->whereBetween('pickups.pickup_date', [$date1, $date2]);
        }
        $active_pickups = $query->orderBy('id', 'desc')->get(array('pickups.price', 'pickups.agent_id', 'pickups.application_id',
            'pickups.pickup_person',
            'pickups.home_address as address', 'pickups.status',
            'pickups.pincode',
            'pickups.price', 'pickups.city',
            'pickups.pickup_date', 'pickups.published_at',
            'customers.name as customer', 'customers.company_name', 'agents.name as agent_name', 'company_products.name as job',
            'pickups.id', 'pickups.application_id'));


        return Datatables::of($active_pickups)
                        ->addColumn('actions', function($active_pickups) {
                            $str = $active_pickups->company_name . '_' . $active_pickups->job . '_' . $active_pickups->application_id . '_' . $active_pickups->pickup_person;
                            $pdf_file_name = str_replace(' ', '', $str);
                            $pickup_pdf_url = "/public/uploads/pickups/pdf/" . $pdf_file_name . ".pdf";
                            $actions = '<a class="btn btn-sm success " data-toggle="tooltip" title="Edit Pickup" href=' . route('pickups.edit', $active_pickups->id) . '><i class="fa fa-edit"></i></a>

                          <a class="btn btn-sm warning" title="Delete Pickup" data-toggle="modal"  data-target="#delete_pickup" onclick="setId(' . $active_pickups->id . ')"><i class="fa fa-trash-o"></i></a>';
                            if ($active_pickups->status == 'Document Submited' || $active_pickups->status == 'Completed') {
                                $actions .= '<a class="btn btn-sm primary" title="Pdf" target="new" style="margin-left: 5px;" href="' . \url($pickup_pdf_url) . '"><i class="fa fa-file-pdf-o"></i></a>';
                            }
                            if ($active_pickups->agent_id == null || $active_pickups->agent_id == '' || $active_pickups->agent_id == -1) {
                                $actions .= '<a class="btn btn-sm info"  style="margin-left: 5px;"data-toggle="modal" title="Assign FC" value="" data-target="#assign_agent" onclick="setId(' . $active_pickups->id . ')"><i class="material-icons"></i></a>';
                            }
                            return $actions;
                        })
                        ->editColumn('price', function($active_pickups) {
                            $price = '<i class="fa fa-inr fa-fw" aria-hidden="true"></i><span id="total">' . $active_pickups->price;
                            return $price;
                        })
                        ->editColumn('price', function($active_pickups) {
                            $price = '<i class="fa fa-inr fa-fw" aria-hidden="true"></i><span id="total">' . $active_pickups->price;
                            return $price;
                        })
                        ->editColumn('status', function($active_pickups) {
                            $reshedule_counts = PickupSchedule::where('pickup_id', $active_pickups->id)->count();
                            $reshedule_count = $reshedule_counts - 1;
                            $status = $active_pickups->status;

                            if ($reshedule_count > 1 && $reshedule_count < 5) {
                                $reshedule_count = '<label style="background-color: #0275d8;" class="label label-danger">Reschedule' . '(' . $reshedule_count . ')' . '</label>';
                                $status = $status . ' ' . $reshedule_count;
                            }
                            if ($reshedule_count >= 5) {
                                $reshedule_count = '<label style="background-color: #EF6F6C;" class="label label-danger">Reschedule' . '(' . $reshedule_count . ')' . '</label>';
                                $status = $status . ' ' . $reshedule_count;
                            }

                            return $status;
                        })
                        ->editColumn('pickup_date', function ($active_pickups) {
                            $pickup_date = Carbon::parse($active_pickups->pickup_date)->format('d-m-Y');
                            return $pickup_date;
                        })
                        ->addColumn('checkbox', function($active_pickups) {
                            $checkbox = '<label class="ui-check m-a-0"><input type="checkbox" name="ids[]" class="checkBoxClass" value=' . $active_pickups->id . '><i class="dark-white"></i>
                                <input type="hidden" name="row_ids[]" value=' . $active_pickups->id . ' /></label>';
                            return $checkbox;
                        })
                        ->rawColumns(['actions', 'checkbox', 'price', 'status'])
                        ->make(true);
    }

    //Create Pickup
    public function create() {
        $jobs = Document_type_master::get();
        $customers = Customer::get();
        // General for all pages
        $GeneralWebmasterSections = WebmasterSection::where('status', '=', '1')->orderby('row_no', 'asc')->get();
        return view('backEnd.pickups.create', compact('GeneralWebmasterSections', 'jobs', 'customers'));
    }

    public function edit($id) {

        //Submitted Documents on Edit Screen
        $submited_documents = Pickup::where('id', $id)->first();
        if ($submited_documents == null) {
            return redirect()->action('PickupController@index');
        }
        $pickupDocuments = PickupDocument::where('pickup_id', $submited_documents->id)
                ->orderby('sequence', 'asc')
                ->get(array('id', 'is_image', 'question', 'sequence', 'comments'));
        $submited_documents->documents = $pickupDocuments;

        //availability of document Link
        $available_documents = PickupDocument::where('pickup_id', $submited_documents->id)->pluck('id');
        $link_availble = PickupDocumentLinks::whereIn('pickup_document_id', $available_documents)
                ->count();

        if ($pickupDocuments != NULL) {

            foreach ($pickupDocuments as $pickupDocument) {
                $links = PickupDocumentLinks::where('pickup_document_id', $pickupDocument->id)
                        ->get(array('name', 'link', 'id'));
                $pickupDocument->links = $links;

                $pictures = PickupDocumentPictures::where('pickup_document_id', $pickupDocument->id)
                        ->get(array('id', 'filename', 'latitude', 'longitude'));
                $pickupDocument->pictures = $pictures;
            }
        }

        // General for all pages
        $GeneralWebmasterSections = WebmasterSection::where('status', '=', '1')->orderby('row_no', 'asc')->get();
        // General END
        $Pickups = Pickup::where('created_by', '=', Auth::user()->id)->find($id);
        $Pickupschedules = PickupSchedule::where('pickup_id', $id)->get();
        foreach ($Pickupschedules as $schedule) {
            if ($schedule->is_agent) {
                $agent = Agent::where('id', $schedule->created_by)->first(array('name'));
                $schedule->created_by = $agent->name;
            } else {
                $user = User::where('id', $schedule->created_by)->first(array('name'));
                $schedule->created_by = $user->name;
            }
        }
        $pickup_documents = PickupDocument::where('pickup_id', $id)->get();
        $document_submit_date = PickupDocument::where('pickup_id', $id)
                ->where('comments', '!=', null)
                ->first(array('created_at'));

        $document_submit_date = PickupDocument::join('pickup_document_pictures', 'pickup_document_pictures.pickup_document_id', 'pickup_documents.id')
                ->where('pickup_documents.pickup_id', $id)
                ->where('pickup_documents.comments', '!=', null)
                ->orWhere('pickup_document_pictures.filename', '!=', null)
                ->first(array('pickup_document_pictures.created_at'));

        if (!empty($Pickups)) {
            //Topic Topics Details
            $products = CompanyProduct::get();
            $customers = Customer::get();
            $agents = Agent::get();
            return view("backEnd.pickups.edit", compact("link_availble", "GeneralWebmasterSections", "Pickupschedules", "document_submit_date", "Docpictures", "Pickups", "submited_doc", "pickup_documents", "products", "customers", "agents", "submited_documents"));
        } else {
            return redirect()->action('PickupController@index');
        }
    }

    public function update($id, Request $request) {
        $update_pickup = Pickup::where('id', $id)->first();
        if ($request->get('product_id') != $update_pickup->product_id) {
            PickupDocument::where('pickup_id', $id)->delete();
            $documents = ProductQuestions::where('product_questions.company_product_id', $request->get('product'))
                    ->where('product_questions.company_id', $request->get('customer'))
                    ->orderBy('sequence')
                    ->get(array('question', 'sequence', 'question_image'));
            foreach ($documents as $document) {
                $new_document = new Pickup_Document();
                $new_document->pickup_id = $id;
                $new_document->question = $document->question;
                $new_document->is_image = $document->question_image;
                $new_document->sequence = $document->sequence;
                $new_document->save();
            }
        }

        session(['document' => '0']);
        if ($update_pickup != null) {
            $update_pickup->customer_id = $request->get('customer');
            $update_pickup->product_id = $request->get('product');
            if ($request->get('agent') != -1 && $request->get('agent') != null) {
                $update_pickup->agent_id = $request->get('agent');
            }
            if ($request->get('status') != "Published") {
                $update_pickup->status = $request->get('status');
            } else {
                $update_pickup->status = $request->get('status');
                $update_pickup->agent_id = null;
            }
            $update_pickup->price = $request->get('price');
            $update_pickup->pod_number = $request->get('pod_number');
            $update_pickup->delivery_number = $request->get('delivery_number');
            $update_pickup->application_id = $request->get('application_id');
            $update_pickup->pickup_person = $request->get('pickup_person');
            $update_pickup->pickup_email = $request->get('pickup_email');
            $update_pickup->home_address = $request->get('home_address');
            $update_pickup->city = $request->get('city');
            $update_pickup->state = $request->get('state');
            $update_pickup->pincode = $request->get('pincode');
            $update_pickup->office_address = $request->get('office_address');
            $update_pickup->office_city = $request->get('office_city');
            $update_pickup->office_state = $request->get('office_state');
            $update_pickup->office_pincode = $request->get('office_pincode');
            $update_pickup->mobile = $request->get('mobile');
            $update_pickup->cheque_amt = $request->get('cheque_amt');
            $update_pickup->loan_amt = $request->get('loan_amt');
            $update_pickup->created_by = Auth::user()->id;
            $update_pickup->save();
            return back()->with('message', 'Pickup Update Successfully.');
        } else {
            return redirect()->action('PickupController@index')->with('message', 'This Pickup Is Not Available!!');
        }
    }

    public function delete($id) {
        Pickup::where('id', $id)->delete();
        PickupSchedule::where('pickup_id', $id)->delete();
        PickupDocument::where('pickup_id', $id)->delete();
        return redirect()->action('PickupController@index')->with('message', 'Pickup Deleted Successfully.');
    }

    public function import() {
        $excel_data['success'] = null;
        $excel_data['failed'] = null;
        $data = null;
        // General for all pages
        $GeneralWebmasterSections = WebmasterSection::where('status', '=', '1')->orderby('row_no', 'asc')->get();
        return view('backEnd.pickups.import_pickup', compact('GeneralWebmasterSections', 'data', 'excel_data'));
    }

    public function getdocument(Request $request) {
        $job_id = $request->get('job_id');
        $customer_id = $request->get('customer_id');
        //get Document from documnt table
        $select_document = Document_type_details::join('documents', 'documents.id', 'document_type_details.document_id')
                ->where('document_type_details.job_id', $job_id)
                ->where('document_type_details.customer_id', $customer_id)
                ->get(array('documents.name as document_name', 'document_type_details.question', 'document_type_details.sequence'));
        if ($select_document->isEmpty()) {
            $data['status'] = 0;
            $data['message'] = 'Document Not Found';
        } else {
            $data['status'] = 1;
            $data['documents'] = $select_document;
        }
        return json_encode($data);
    }

    public function saveExcel(Request $request) {
        $success_list = [];
        $failed_list = [];
        $updated_list = [];
        $failed_pickups = [];
        DB::beginTransaction();
        try {

            if (Input::hasFile('file')) {
                $path = Input::file('file')->getRealPath();
                $data = Excel::load($path, function($reader) {
                            
                        })->get();

                //Cities For Pickup Notification
                $cities = Excel::load($path, function($reader) {
                            
                        })->get(array('city'));

                $result = array();
                foreach ($cities as $key => $value) {
                    $result[] = $value->city;
                }
                $tokens = Agent::whereIn('city', array_unique($result))
                                ->where('status', 'Approved')
                                ->whereNotNull('TokenID')
                                ->pluck('TokenID')->toArray();

                //Import Pickup
                $pickup_record = array();
                $pickups = array();
                $isHavingDuplicate = 0;

                if ($data->isEmpty() && $data->count() == 0) {
//                    \session()->put('cutomer_id');
                    return Redirect::route('pickups.import')->with('error', 'Uploaded File Is Empty Please Try Again.');
                } else {

                    foreach ($data as $key => $value) {


                        if ($value->pickup_person != NULL) {
                            unset($pickup_record);
                            $pickup_record['status'] = str_replace(',', ' ', $value->status);
                            $pickup_record['fc_id'] = str_replace(',', ' ', $value->fc_id);
                            $pickup_record['company_id'] = str_replace(',', ' ', $value->company_id);
                            $pickup_record['product_code'] = str_replace(',', ' ', $value->product_code);
                            $pickup_record['price'] = str_replace(',', ' ', $value->price);
                            $pickup_record['application_id'] = str_replace(',', ' ', $value->application_id);
                            $pickup_record['pickup_person'] = $value->pickup_person;
                            $pickup_record['pickup_email'] = $value->pickup_email;
                            $pickup_record['home_address'] = $value->home_address;
                            $pickup_record['city'] = $value->city;
                            $pickup_record['state'] = $value->state;
                            $pickup_record['pincode'] = str_replace(',', ' ', $value->pincode);
                            $pickup_record['office_address'] = $value->office_address;
                            $pickup_record['office_city'] = $value->office_city;
                            $pickup_record['office_address'] = $value->office_address;
                            $pickup_record['office_state'] = $value->office_state;
                            $pickup_record['office_pincode'] = str_replace(',', ' ', $value->office_pincode);
                            $pickup_record['cheque_amt'] = $value->cheque_amt;
                            $pickup_record['loan_amt'] = $value->loan_amt;
                            $pickup_record['preferred_start_time'] = $value->preferred_start_time;
                            $pickup_record['preferred_end_time'] = $value->preferred_end_time;
                            $pickup_record['mobile'] = str_replace(',', ' ', $value->mobile);
                            $pickup_record['pickup_date'] = $value->pickup_date;

                            //Value for Execel TextField
                            $pickups_columns = PickupColumn::get(array('column'));
                            foreach ($pickups_columns as $pickups_column) {
                                $column = 'col_' . strtolower($pickups_column->column);
                                $pickup_record['col_' . $pickups_column->column] = $value->$column;
                            }
                            $pickup_record = (object) $pickup_record;
                            $failed_length = count($failed_list);
                            $updated_length = count($updated_list);
                            if ($value->application_id == null) {
                                $isHavingDuplicate = 1;
                                $temp = array();
                                $temp['record'] = $pickup_record;
                                $temp['type'] = 'Appication Id Can Not Be Empty';
                                $failed_list[$failed_length] = (object) $temp;
                                $failed_pickups[] = $pickup_record;
                                continue;
                            }

                            if (!$this->isproductIDExists($value->product_code, $value->company_id)) {
                                $isHavingDuplicate = 1;
                                $temp = array();
                                $temp['record'] = $pickup_record;
                                $temp['type'] = 'Product Code entered does not exist';
                                $failed_list[$failed_length] = (object) $temp;
                                $failed_pickups[] = $pickup_record;
                                continue;
                            }

                            if (!$this->iscustomerIdExists($value->company_id, 'id')) {
                                $isHavingDuplicate = 1;
                                $temp = array();
                                $temp['record'] = $pickup_record;
                                $temp['type'] = 'Customer Id entered does not exist';
                                $failed_list[$failed_length] = (object) $temp;
                                $failed_pickups[] = $pickup_record;
                                continue;
                            }
                            $pickups[] = $pickup_record;
                        }
                    }

                    if ($this->addPickup($pickups)) {
                        $success_list = $pickups;
                        DB::commit();
                    }

                    $excel_data['success'] = count($pickups);
                    $excel_data['failed'] = count($failed_pickups);
                    $data['success'] = $this->excel_data['success'] = $success_list;
                    $data['failed'] = $this->excel_data['failed'] = $failed_list;

                    // General for all pages
                    $GeneralWebmasterSections = WebmasterSection::where('status', '=', '1')->orderby('row_no', 'asc')->get();
                    if (count($tokens) > 0) {
                        $optionBuilder = new OptionsBuilder();
                        $optionBuilder->setTimeToLive(60 * 20);
                        $notificationBuilder = new PayloadNotificationBuilder('Notification');
                        $notificationBuilder->setBody(['type' => 'New Pickups Added']);

                        //->setSound('default');
                        $dataBuilder = new PayloadDataBuilder();
                        $dataBuilder->addData(['type' => 'New Pickups Added']);

                        $option = $optionBuilder->build();
                        $notification = $notificationBuilder->build();
                        $notificationdata = $dataBuilder->build();

                        $downstreamResponse = FCM::sendTo($tokens, $option, $notification, $notificationdata);
                        $downstreamResponse->numberSuccess();
                        $downstreamResponse->numberFailure();
                        $downstreamResponse->numberModification();

                        //return Array - you must remove all this tokens in your database
                        $downstreamResponse->tokensToDelete();

                        //return Array (key : oldToken, value : new token - you must change the token in your database )
                        $downstreamResponse->tokensToModify();

                        //return Array - you should try to resend the message to the tokens in the array
                        $downstreamResponse->tokensToRetry();

                        // return Array (key:token, value:errror) - in production you should remove from your database the tokens present in this array
                        $downstreamResponse->tokensWithError();
                    }
                }
            }


            return view('backEnd.pickups.import_pickup', compact('excel_data', 'data', 'GeneralWebmasterSections'))->with('success', 'Pickups Uploaded Successfully');
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            return Redirect::route('pickups.import')->with('error', 'Pickups Import Failed. Please try again.');
        }
    }

    public function isproductIDExists($record_value, $company_id) {
        $product_code = $record_value;
        $query = CompanyProduct::where(function($query) use($product_code) {
                    $query->where('name', '=', $product_code)
                    ->orWhere('product_code', '=', $product_code);
                })
                ->where(function($query) use ($company_id) {
            $query->where('company_id', $company_id);
        });
        return $query->count();
    }

    public function iscustomerIdExists($record_value, $type = 'id') {
        return Customer::where($type, '=', $record_value)->count();
    }

    public function addPickup($pickups) {

        foreach ($pickups as $request) {

            $pickup = new Pickup();
            if ($request->status == 1) {
                $pickup->status = 'Published';
            } else {
                $pickup->status = 'UnPublished';
            }
            if ($request->fc_id != null && $request->fc_id != 0) {
                $pickup->status = 'Accepted';
            }

            $pickup->customer_id = $request->company_id;
            if ($request->fc_id != null && $request->fc_id != 0) {
                $pickup->agent_id = $request->fc_id;
            } else {
                $pickup->agent_id = Null;
            }

            $product_code = $request->product_code;
            $company_id = $request->company_id;
            $query = CompanyProduct::where(function($query) use($product_code) {
                        $query->where('name', '=', $product_code)
                        ->orWhere('product_code', '=', $product_code);
                    })
                    ->where(function($query) use ($company_id) {
                $query->where('company_id', $company_id);
            });
            $product_id = $query->first();

            $pickup->product_id = $product_id->id;

            $price = $request->price;
            if ($price != null || $price != 0) {
                $pickup->price = $request->price;
            } else {
                $product_price = CompanyProduct::where('id', $product_id->id)->first(array('price'));
                $pickup->price = $product_price->price;
            }

            $pickup->pickup_person = $request->pickup_person;
            $pickup->pickup_email = $request->pickup_email;

            $pickup->application_id = $request->application_id;
            $pickup->home_address = $request->home_address;
            $city = ucfirst(trim($request->city));
            $city = ucfirst(strtolower($city));
            $pickup->city = $city;
            $pickup->state = $request->state;
            $pickup->pincode = $request->pincode;
            $pickup->office_address = $request->office_address;
            $pickup->office_city = $request->office_city;
            $pickup->office_state = $request->office_state;
            $pickup->office_pincode = $request->office_pincode;
            $pickup->mobile = $request->mobile;
            $pickup->cheque_amt = $request->cheque_amt;
            $pickup->loan_amt = $request->loan_amt;
            $pickup->created_by = Auth::user()->id;
            $pickup->preferred_start_time = $request->preferred_start_time;
            $pickup->preferred_end_time = $request->preferred_end_time;
            $pickup_date = Carbon::parse($request->pickup_date)->format('Y-m-d');
            $pickup->pickup_date = $pickup_date;
            $pickup->save();

            //Add Pickup history To PickupSchedule Table
            $new_pickupschedule = new PickupSchedule();
            $new_pickupschedule->pickup_id = $pickup->id;
            $new_pickupschedule->pickup_date = $pickup_date;
            $new_pickupschedule->pickup_startime = $request->preferred_start_time;
            $new_pickupschedule->pickup_endtime = $request->preferred_end_time;
            $new_pickupschedule->created_by = Auth::user()->id;
            if ($request->fc_id != null && $request->fc_id != 0) {
                $new_pickupschedule->is_agent = 1;
            }
            $new_pickupschedule->save();

            $questions = ProductQuestions::where('company_product_id', $pickup->product_id)
                    ->where('company_id', $request->company_id)->orderBy('sequence')
                    ->get(array('id', 'question', 'sequence', 'question_image'));

            if (count($questions) > 0) {
                foreach ($questions as $question) {

                    $new_document = new Pickup_Document();
                    $new_document->pickup_id = $pickup->id;
                    $text = null;
                    while (strpos($question->question, '[[Text_') !== false) {
                        preg_match('#\[(.*?)\]#', $question->question, $text);
                        $col = str_replace('_', '', substr($text[1], -2));
                        $excel_column = 'col' . '_' . $col;
                        $question->question = str_replace('[[Text' . '_' . $col . ']]', $request->$excel_column, $question->question);
                    }
                    while (strpos($question->question, '[[Link_') !== false) {
                        preg_match('#\[(.*?)\]#', $question->question, $text);
                        $col = str_replace('_', '', substr($text[1], -2));
                        $excel_column = 'col' . '_' . $col;
                        $question->question = str_replace('[[Link' . '_' . $col . ']]', $request->$excel_column, $question->question);
                    }

                    $new_document->question = $question->question;
                    $new_document->sequence = $question->sequence;
                    $new_document->is_image = $question->question_image;
                    $new_document->save();


                    $links = ProductQuestionLinks::where('question_id', $question->id)->get();

                    foreach ($links as $link) {
                        preg_match('#\[(.*?)\]#', $link->link, $text);
                        $col = str_replace('_', '', substr($text[1], -2));
                        $excel_column = 'col' . '_' . $col;

                        $questionLink = new PickupDocumentLinks();

                        $questionLink->pickup_document_id = $new_document->id;
                        $questionLink->name = $link->name;
                        $questionLink->link = $request->$excel_column;
                        $questionLink->save();
                    }
                }
            }
        }

        if ($pickups) {
            $data = new \stdClass();
            $data->notification = Auth()->user()->name . count($pickups) . ' Pickups Imported  Succesfully';
            $user = User::where('id', Auth()->user()->id)->first();
            $user->notify(new PickupAddNotification($data));
        }
        return true;
    }

//    public function addPickup($pickups)
//    {
//
//        foreach($pickups as $request) {
//
//            $pickup = new Pickup();
//            if ($request->status == 1) {
//                $pickup->status = 'Published';
//            } else {
//                $pickup->status = 'UnPublished';
//            }
//            if ($request->fc_id != null && $request->fc_id != 0) {
//                $pickup->status = 'Published';
//            }
//            $pickup->customer_id = $request->company_id;
//            $pickup->agent_id = $request->fc_id;
//            $product_id = CompanyProduct::where('name', '=', $request->product_code)->orWhere('product_code', '=', $request->product_code)->first();
//            $pickup->product_id = $product_id->id;
//
//            $price = $request->price;
//            if ($price != null || $price != 0) {
//                $pickup->price = $request->price;
//            } else {
//                $product_price = CompanyProduct::where('id', $product_id->id)->first(array('price'));
//                $pickup->price = $product_price->price;
//            }
//            $pickup->pickup_person = $request->pickup_person;
//            $pickup->application_id = $request->application_id;
//            $pickup->home_address = $request->home_address;
//            $pickup->city = $request->city;
//            $pickup->state = $request->state;
//            $pickup->pincode = $request->pincode;
//            $pickup->office_address = $request->office_address;
//            $pickup->office_city = $request->office_city;
//            $pickup->office_state = $request->office_state;
//            $pickup->office_pincode = $request->office_pincode;
//            $pickup->mobile = $request->mobile;
//            $pickup->created_by = Auth::user()->id;
//            $pickup->preferred_start_time = $request->preferred_start_time;
//            $pickup->preferred_end_time = $request->preferred_end_time;
//            $pickup_date = Carbon::parse($request->pickup_date)->format('Y-m-d');
//            $pickup->pickup_date = $pickup_date;
//            $pickup->save();
//
//            $questions = ProductQuestions::where('company_product_id', $pickup->product_id)
//                ->where('company_id', $request->company_id)->orderBy('sequence')->get(array('id', 'question', 'sequence'));
//
//            if (count($questions) > 0) {
//                foreach ($questions as $question) {
//                    $new_document = new PickupDocument();
//                    $new_document->pickup_id = $pickup->id;
//                    $text = null;
//                    while (strpos($question->question,'[[Text_') !== false) {
//                        preg_match('#\[(.*?)\]#', $question->question, $text);
//                        $col = str_replace('_', '',substr($text[1], -2));
//                        $excel_column = 'col' . '_' . $col;
//                        $question->question = str_replace('[[Text' . '_' . $col . ']]', $request->$excel_column, $question->question);
//                    }
//                    $new_document->question = $question->question;
//                    $new_document->sequence = $question->sequence;
//                    $new_document->save();
//
//                    $links = ProductQuestionLinks::where('question_id', $question->id)->get();
//
//                    foreach($links as $link)
//                    {
//                        preg_match('#\[(.*?)\]#', $link->link, $text);
//                        $col = str_replace('_', '',substr($text[1], -2));
//                        $excel_column = 'col' . '_' . $col;
//
//                        $questionLink = new PickupDocumentLinks();
//                        $questionLink->pickup_document_id = $new_document->id;
//                        $questionLink->name = $link->name;
//                        $questionLink->link = $request->$excel_column;
//                        $questionLink->save();
//
//                    }
//                }
//            }
//        }
//
//        if($pickups){
//            $data = new \stdClass();
//            $data->notification = Auth()->user()->name . count($pickups).' Pickups Imported  Succesfully';
//            $user = User::where('id',Auth()->user()->id)->first();
//            $user->notify(new PickupAddNotification($data));
//        }
//        return true;
//    }

    public function downloadExcel(Request $request) {
        $Data = \GuzzleHttp\json_decode($request->get('data'));
        $formData = json_decode(json_encode($Data), true);

        return Excel::create('pickup_report', function ($excel) use ($formData) {
                    $excel->sheet('Failed', function ($sheet) use ($formData) {
                        $sheet->row(1, array('Reason', 'status', 'fc_id', 'company_id', 'product_code', 'price', 'application_id', 'pickup_person', 'home_address', 'city', 'state', 'pincode', 'office_address', 'office_city', 'office_state', 'office_pincode', 'mobile'));
                        $cnt = 2;
                        if (is_array($formData['failed']) ? count($formData['failed']) : 1) {
                            foreach ($formData['failed'] as $data_item) {
                                $item = $data_item['record'];
                                $sheet->appendRow($cnt++, array($data_item['type'], $item['status'], $item['fc_id'],
                                    $item['company_id'], $item['product_code'], $item['price'], $item['application_id'], $item['pickup_person'],
                                    $item['home_address'], $item['city'], $item['state'], $item['pincode'], $item['office_address'], $item['office_city'], $item['office_state'], $item['office_pincode'], $item['mobile']));
                            }
                        }
                    });
                    $excel->sheet('Success', function ($sheet) use ($formData) {
                        $sheet->row(1, array('status', 'fc_id', 'company_id', 'product_code', 'price', 'application_id', 'pickup_person', 'home_address', 'city', 'state', 'pincode', 'office_address', 'office_city', 'office_state', 'office_pincode', 'mobile'));
                        $cnt = 2;
                        foreach ($formData['success'] as $data_item) {
                            $item = $data_item;
                            $sheet->appendRow($cnt++, array(
                                $item['status'], $item['fc_id'],
                                $item['company_id'], $item['product_code'], $item['price'], $item['application_id'], $item['pickup_person'],
                                $item['home_address'], $item['city'], $item['state'], $item['pincode'], $item['office_address'], $item['office_city'], $item['office_state'], $item['office_pincode'], $item['mobile']));
                        }
                    });
                })->download('xlsx');
    }

    public function document_edit(Request $request) {
        $document_id = $request->get('document_id');
        $edit_documents = PickupDocument::where('id', $document_id)->first();
        $Documents_Pictures = PickupDocumentPictures::where('pickup_document_id', $edit_documents->id)->get(array('id', 'filename'));
        $edit_documents->pictures = $Documents_Pictures;
        $document_limks = PickupDocumentLinks::where('pickup_document_id', $edit_documents->id)->get(array('id', 'name', 'link'));
        $edit_documents->links = $document_limks;
        if ($edit_documents != null) {
            $data['edit_documents'] = $edit_documents;
            $data['status'] = 1;
            return json_encode($data);
        } else {
            $data['status'] = 0;
            return json_encode($data);
        }
    }

    public function updateDocument(Request $request) {
        $document_id = $request->get('document_id');
        $pickup_id = $request->get('pickup_id');
        session(['document' => '1']);
        $updateDocument = PickupDocument::where('id', $document_id)->first();
        $updateDocument->pickup_id = $pickup_id;
        $updateDocument->question = $request->get('question');
        $updateDocument->sequence = $request->get('sequence');
        $updateDocument->comments = $request->get('comment');
        $updateDocument->save();

        //update document Link
        if ($request->get('link_id') != null) {
            foreach ($request->get('link_id') as $link_id) {
                $updatelink = PickupDocumentLinks::where('id', $link_id)->first();
                $updatelink->link = $request->get('link_' . $link_id);
                $updatelink->save();
            }
        }

        return redirect()->action('PickupController@edit', $pickup_id)->with('message', 'Document Update Successfully.');
    }

    public function document_delete($document_id) {
        PickupDocument::where('id', $document_id)->delete();
        return redirect(route('pickups'))->with('message', 'Document Delete Successfully.');
    }

    //set Unpublish  Pickups to Publish Pickups
    public function setPublish() {
        $pickup_id = Input::get('pickup_id');
        //Find Pickup
        $pickup = Pickup::where('id', $pickup_id)->first();
        if ($pickup->status != 'UnPublished') {
            $pickup->status = "Published";
            $pickup->agent_id = null;
            $pickup->save();
            $data['pickup_status'] = $pickup->status;
            $data['status'] = 1;
            $data['message'] = 'Pickup Status Change Succesfully';
            return json_encode($data);
        } else {
            $pickup->status = 'Published';
            $pickup->save();
            $data['pickup_status'] = $pickup->status;
            $data['status'] = 0;
            $data['message'] = 'Pickup Published Succesfully';
            return json_encode($data);
        }
    }

    //Update Pickup status code
    public function updateStatus(Request $request) {
        if ($request->ids != "") {
            $status = $request->get('action');
            Pickup::wherein('id', $request->ids)->update(['status' => $status]);
        }
        return redirect()->action('PickupController@index')->with('doneMessage', trans('backLang.saveDone'));
    }

    //SAmple Pdfview code
    public function pdfview(Request $request) {

        $pickupId = $request->get('pickup_pdf_id');
        $action = $request->get('pickup_status');
        if ($pickupId != null) {
            if ($action) {
                $Pickup_status = Pickup::where('id', $pickupId)->first();
                $Pickup_status->status = $action;
                $Pickup_status->save();
            }
            $submited_documents = PickupDocument::where('pickup_id', $pickupId)
                            ->orderBy('sequence', 'asc')->get(array('id', 'question', 'sequence', 'comments'));

            if (count($submited_documents) > 0) {
                foreach ($submited_documents as $submited_document) {
                    $pictures = PickupDocumentPictures::where('pickup_document_id', $submited_document->id)
                            ->get(array('id', 'filename', 'latitude', 'longitude'));
                    $submited_document->pictures = $pictures;
                }
            }
            $Pickups = Pickup::where('id', $pickupId)->first(array('agent_id', 'application_id', 'product_id', 'completed_at', 'pod_number', 'pod_number', 'delivery_number', 'status', 'home_address', 'city', 'state', 'pincode', 'pickup_date', 'completed_at', 'pickup_person', 'id'));
            $document_submit_date = PickupDocument::join('pickup_document_pictures', 'pickup_document_pictures.pickup_document_id', 'pickup_documents.id')
                    ->where('pickup_documents.pickup_id', $pickupId)
                    ->first(array('pickup_document_pictures.created_at'));
            $agents = Agent::where('id', $Pickups->agent_id)->first(array('name', 'email', 'mobile', 'status', 'id'));
            $category = CompanyProduct::where('id', $Pickups->product_id)->first(array('name'));

            //mail to customer
            $CustomerId = Pickup::where('id', $pickupId)->first(array('customer_id'));
            $customer_mail = Customer::where('id', $CustomerId->customer_id)->first(array('id', 'email', 'name', 'company_name'));

            //pdf code
            $pdf = PDF::loadView('backEnd.pickups.pdf', compact('agents', 'Pickups', 'submited_documents', 'document_submit_date'));
            $pdf->setPaper('A4', 'portrait');
            $str = $customer_mail->company_name . '_' . $category->name . '_' . $Pickups->application_id . '_' . $Pickups->pickup_person;
            $pdf_file_name = str_replace(' ', '', $str);
            $pdf_name = trim($pdf_file_name) . '.pdf';
            $file = public_path() . '/uploads/pickups/pdf/' . $pdf_file_name . '.pdf';
            $pdf->save($file);
            file_put_contents($file, $pdf->output());
            //                $pickup_file =  URL::to('/downloads/' .$pdf_file_name. '.pdf');

            if ($request->get('check_customer_mail') == 1) {
                if ($customer_mail != null) {
                    $data = new \stdClass();
                    $data->name = $customer_mail->name;
                    $data->pickup_name = $pdf_name;
                    $data->subject_name = $category->name . ' ' . $Pickups->application_id . ' ' . $Pickups->pickup_person;
                    $data->pdf_link = url('/uploads/pickups/pdf/' . $pdf_file_name . '.pdf');
                    $data->agent_detail = $agents->name . '-' . $agents->id;

                    if ($customer_mail->email_cc != null) {
                        $customers = preg_split("/\,/", $customer_mail->email_cc);
                        foreach ($customers as $index => $cusomer_mail) {
                            if ($cusomer_mail != Null) {
                                $data->cc_1 = $customers;
                            }
                        }
                    }
                    Mail::to($customer_mail->email)
                            ->send(new Pickuppdf($file, $data));
                    return back()->with('message', 'Pdf Generate Succesfully!!');
                } else {
                    return back()->with('message', 'Pickup Mail not Send. Please try again');
                }
            }
            return back()->with('message', 'Pdf Generate Succesfully!!');
        }
    }

    public function show($id) {
        $submited_documents = PickupDocument::where('pickup_id', $id)
                        ->orderBy('sequence', 'asc')->get(array('id', 'question', 'sequence', 'comments'));
        if (count($submited_documents) > 0) {
            foreach ($submited_documents as $submited_document) {
                $pictures = PickupDocumentPictures::where('pickup_document_id', $submited_document->id)->get(array('id', 'filename', 'latitude', 'longitude'));
                $submited_document->pictures = $pictures;
            }
        }

        $Pickups = Pickup::where('created_by', '=', Auth::user()->id)
                        ->where('id', $id)->first(array('id', 'agent_id', 'completed_at', 'pod_number',
            'pod_number', 'delivery_number', 'status', 'home_address', 'city', 'state', 'pincode', 'pickup_date', 'completed_at', 'pickup_person'));
        $document_submit_date = PickupDocument::join('pickup_document_pictures', 'pickup_document_pictures.pickup_document_id', 'pickup_documents.id')
                        ->where('pickup_documents.pickup_id', $id)->first(array('pickup_document_pictures.created_at'));

        $agents = Agent::where('id', $Pickups->agent_id)->first(array('name', 'email', 'mobile', 'status'));

        $CustomerId = Pickup::where('id', $id)->first(array('customer_id'));
        $customer_mail = Customer::where('id', $CustomerId->customer_id)->first(array('id', 'email', 'name'));



        // General for all pages
        $GeneralWebmasterSections = WebmasterSection::where('status', '=', '1')->orderby('row_no', 'asc')->get();
        return view('backEnd.pickups.show', compact('Pickups', 'document_submit_date', 'pickupDocuments', 'document_submit_date', 'submited_documents', 'pickup_documents', 'GeneralWebmasterSections', 'agents'));
    }

    public function assignAgent(Request $request) {
        $agent_id = $request->get('assign_agent');
        $check_agent = Agent::where('id', $agent_id)->first();
        if ($check_agent->status == 'Approved') {
            $pickups = Pickup::where('id', $request->get('pickup_id'))->first();
            if ($pickups->agent_id != null && $pickups->agent_id != -1) {
                return back()->with('message', 'FC Already Assigned To This Pickup');
            } else {
                Pickup::where('id', $request->get('pickup_id'))->update(['agent_id' => $agent_id, 'status' => 'Accepted']);
                return back()->with('message', 'FC Assign Succesfully To ' . $pickups->pickup_person);
            }
        } else {
            return back()->with('message', 'Sorry!! FC is Not Approved');
        }
    }

    public function ReschedulePickup(Request $request) {
        $reshedule_pickup = Pickup::where('id', $request->pickup_id)->first();
        if ($reshedule_pickup != null) {
            $pickup_schedule = new PickupSchedule();
            $pickup_schedule->pickup_id = $reshedule_pickup->id;
            $pickup_schedule->pickup_date = Carbon::parse($request->pickup_date)->format('Y-m-d');
            $pickup_schedule->pickup_startime = Carbon::parse($request->start_time)->format('H:i');
            $pickup_schedule->pickup_endtime = Carbon::parse($request->end_time)->format('H:i');
            $pickup_schedule->comments = $request->comments;
            $pickup_schedule->created_by = $reshedule_pickup->created_by;
            $pickup_schedule->save();
            if ($pickup_schedule->save()) {
                $reshedule_pickup->preferred_start_time = Carbon::parse($request->start_time)->format('H:i');
                $reshedule_pickup->preferred_end_time = Carbon::parse($request->end_time)->format('H:i');
                $reshedule_pickup->pickup_date = Carbon::parse($request->pickup_date)->format('Y-m-d');
                $reshedule_pickup->created_by = Auth::user()->id;
                $reshedule_pickup->save();
            }
            return back()->with('message', 'Pickup Reschedule Succssfully!!');
        }
    }

    public function SendLinkMail(Request $request) {
        $link_ids = $request->get('ids');
        $pickup_id = $request->get('pickup_id');
        //mail to Pickup Person
        $person_details = Pickup::where('id', $pickup_id)->first(array('application_id', 'pickup_person', 'id', 'pickup_email'));
        if ($person_details->pickup_email != null) {
            $email = $person_details->pickup_email;
        } else {
            $email = 'poojakakde65@gmail.com';
        }
        //Data for Link Mail
        $data = new \stdClass();
        $data->name = $person_details->pickup_person;
        $data->subject_name = $person_details->application_id . ' ' . $person_details->pickup_person . ' ' . 'Links';
        $doc_links = PickupDocumentLinks::whereIn('id', $link_ids)->get();
        if (count($doc_links) > 0) {
            $data->links = $doc_links;

            Mail::to($email)
                    ->send(new documentLinkMail($data));

            if (Mail::failures()) {
                return back()->with('message', 'Link Mail Does Not  Succssfully!!');
            } else {
                return back()->with('message', 'Link Mail Send Succssfully!!');
            }
        } else {
            return back()->with('message', 'Link Not Available!!');
        }
    }

    //Get Status Related Pickups From Dashboard
    public function getPickup($status, Request $request) {
        return redirect()->action('PickupController@index');
    }

    public function generatepdf(Request $request) {

        $pickupId = 2223;

        $action = Input::get('action');
        if ($pickupId != null) {
            if ($action == 1) {
                $Pickup_status = Pickup::where('id', $pickupId)->first();
                $Pickup_status->status = 'Document Submited';
                $Pickup_status->save();
            }
            $submited_documents = PickupDocument::where('pickup_id', $pickupId)
                            ->orderBy('sequence', 'asc')->get(array('id', 'question', 'sequence', 'comments'));

            if (count($submited_documents) > 0) {
                foreach ($submited_documents as $submited_document) {
                    $pictures = PickupDocumentPictures::where('pickup_document_id', $submited_document->id)
                            ->get(array('id', 'filename', 'latitude', 'longitude'));
                    $submited_document->pictures = $pictures;
                }
            }

            $Pickups = Pickup::where('id', $pickupId)->first(array('agent_id', 'application_id', 'product_id', 'completed_at', 'pod_number', 'pod_number', 'delivery_number', 'status', 'home_address', 'city', 'state', 'pincode', 'pickup_date', 'completed_at', 'pickup_person', 'id'));
            $document_submit_date = PickupDocument::join('pickup_document_pictures', 'pickup_document_pictures.pickup_document_id', 'pickup_documents.id')
                    ->where('pickup_documents.pickup_id', $pickupId)
                    ->first(array('pickup_document_pictures.created_at'));
            $agents = Agent::where('id', $Pickups->agent_id)->first(array('name', 'email', 'mobile', 'status', 'id'));
            $category = CompanyProduct::where('id', $Pickups->product_id)->first(array('name'));

            //mail to customer
            $CustomerId = Pickup::where('id', $pickupId)->first(array('customer_id'));
            $customer_mail = Customer::where('id', $CustomerId->customer_id)->first(array('id', 'email', 'name', 'company_name'));

            //pdf code
            $pdf = PDF::loadView('backEnd.pickups.pdf', compact('agents', 'Pickups', 'submited_documents', 'document_submit_date'));
            $pdf->setPaper('A4', 'portrait');
            $str = $customer_mail->company_name . '_' . $category->name . '_' . $Pickups->application_id . '_' . $Pickups->pickup_person;
            $pdf_file_name = str_replace(' ', '', $str);
            $pdf_name = trim($pdf_file_name) . '.pdf';
            $file = public_path() . '/uploads/pickups/pdf/' . $pdf_file_name . '.pdf';

            $pdf->save($file);

            file_put_contents($file, $pdf->output());
            //                $pickup_file =  URL::to('/downloads/' .$pdf_file_name. '.pdf');
            if ($customer_mail != null) {
                $data = new \stdClass();
                $data->name = $customer_mail->name;
                $data->pickup_name = $pdf_name;
                $data->subject_name = $category->name . ' ' . $Pickups->application_id . ' ' . $Pickups->pickup_person;
                $data->pdf_link = url('/uploads/pickups/pdf/' . $pdf_file_name . '.pdf');
                $data->agent_detail = $agents->name . '-' . $agents->id;

                if ($customer_mail->email_cc != null) {
                    $customers = preg_split("/\,/", $customer_mail->email_cc);
                    foreach ($customers as $index => $cusomer_mail) {
                        if ($cusomer_mail != Null) {
                            $data->cc_1 = $customers;
                        }
                    }
                }
                Mail::to($customer_mail->email)
                        ->send(new Pickuppdf($file, $data));
                return $pdf->download($pdf_name);
            } else {
                return Response::json(array(
                            'error' => 1,
                            'message' => 'Pickup Mail not Send. Please try again'), 200
                );
            }
        } else {
            return Response::json(array(
                        'error' => 1,
                        'message' => 'Do Not Perform Any Action'), 200
            );
        }
    }

}

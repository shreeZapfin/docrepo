<?php

namespace App\Http\Controllers;
use App\AgentBankdetails;
use App\AgentDocument;
use App\AgentReedem;
use App\AgentTransaction;
use App\CompanyProduct;
use App\Customer;
use App\Document_type_master;
use App\Jobs\ProcessPodcast;
use App\Lookup;
use App\Mail\documentLinkMail;
use App\Mail\Pickuppdf;
use App\Mail\Reminder;
use App\AgentEducation;
use App\MobileNotification;
use App\MobileSetting;
use App\Pickup;
use App\PickupDecline;
use App\PickupDocument;
use App\PickupDocumentLinks;
use App\PickupDocumentPictures;
use App\PickupSchedule;
use Carbon\Carbon;
use Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rules\In;
use Mail;
use App\Agent;
use Queue;
use DB;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use LaravelFCM\Facades\FCM;
use Barryvdh\DomPDF\Facade as PDF;
use Image;

/**
 * Class ApiController
 *
 * @package App\Http\Controllers
 *
 * @SWG\Swagger(
 *     basePath="",
 *     host="localhost/docboyz/api/",
 *     schemes={"http"},
 *     @SWG\Info(
 *         version="1.0",
 *         title="Docboyz API",
 *         @SWG\Contact(name="Sanket Shah", url="https://www.google.com"),
 *     ),
 *     @SWG\Definition(
 *         definition="Error",
 *         required={"code", "message"},
 *         @SWG\Property(
 *             property="code",
 *             type="integer",
 *             format="int32"
 *         ),
 *         @SWG\Property(
 *             property="message",
 *             type="string"
 *         )
 *     )
 * )
 */
class ApiController extends Controller
{
    // Api for Agent Register
    public function register(Request $request)
    {
        $name = $request->get('name');
        $email = $request->get('email');
        $mobile = $request->get('mobile');
        $gender = $request->get('gender');
        $address1 = $request->get('address1');
        $address2 = $request->get('address2');
        $city = $request->get('city');
        $state = $request->get('state');
        $pincode = $request->get('pincode');
        $TokenID = $request->get('fcmTokenId');
        $dob = $request->get('dob');
        $password = $request->get('password');
        $degree  = $request->get('degree');
        $college = $request->get('college');
        $year = $request->get('year');
        $check = Agent::where('email', $email)->get(array('id', 'name'));

        //account info
        $bank_name =  $request->get('bank_name');
        $ifsc_code = $request->get('ifsc_code');
        $account_number = $request->get('account_number');
        $account_type = $request->get('account_type');
        if($check->isEmpty()) {
            //personal Details
            $agent = new Agent();
            $agent->name = $name;
            $agent->email = $email;
            $agent->mobile = $mobile;
            $agent->address1 = $address1;
            $agent->address2 = $address2;
            $agent->city = ucfirst($city);
            $agent->gender = $gender;
            $agent->state = $state;
            $agent->pincode = $pincode;
            $agent->TokenID = $TokenID;
            $agent->password = Hash::make($password);
            $agent->dob = Carbon::parse($dob)->format('Y-m-d');

            if($_FILES['profile_pic']['tmp_name'] != false){
                $image = $_FILES['profile_pic']['tmp_name'];
                $name = $_FILES['profile_pic']['name'];

                $destinationPath = public_path() . '/uploads/agents/';
                $img = Image::make($image);
                $img->resize(800, NULL, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })->save($destinationPath.'/'.$name);

                //$image = $_FILES['photo']['tmp_name'];
                //$name = $_FILES['photo']['name'];
                //$images = file_get_contents($image);
                //$image = base64_encode($image);
                //$file = public_path() .  '/uploads/pickups/' . $name;
                //file_put_contents($file,$images);

                //creating the upload url
                $upload_url = 'https://docboyz.in/admin/public/uploads/pickups/';
                //file url to store in the database
                $file_url = $upload_url.$name;
                //save File into Document table
                $agent->profile_pic = $name;


            }

            $saved = $agent->save();
            if($saved){
                //agent account
                $agent_bankdetail = new AgentBankdetails();
                $agent_bankdetail->bank_name = $bank_name;
                $agent_bankdetail->ifsc_code = $ifsc_code;
                $agent_bankdetail->account_number = $account_number;
                $agent_bankdetail->account_type = $account_type;
                $agent_bankdetail->agent_id = $agent->id;
                $agent_bankdetail->save();

                //agent education
                $agent_education = new AgentEducation();
                $agent_education->agent_id = $agent->id;
                $agent_education->degree = $degree;
                $agent_education->college = $college;
                $agent_education->year = $year;
                $agent_education->save();
                $agent_documents = new AgentDocument();
                $agent_documents->agent_id = $agent->id;
                if($_FILES['pancard']['tmp_name'] != false){
                    $image = $_FILES['pancard']['tmp_name'];
                    $name = $_FILES['pancard']['name'];

                    $destinationPath = public_path() . '/uploads/agents/Documents/';
                    $img = Image::make($image);
                    $img->resize(800, NULL, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    })->save($destinationPath.'/'.$name);

                    //$image = $_FILES['photo']['tmp_name'];
                    //$name = $_FILES['photo']['name'];
                    //$images = file_get_contents($image);
                    //$image = base64_encode($image);
                    //$file = public_path() .  '/uploads/pickups/' . $name;
                    //file_put_contents($file,$images);

                    //creating the upload url
                    $upload_url = 'https://docboyz.in/admin/public/uploads/pickups/';
                    //file url to store in the database
                    $file_url = $upload_url.$name;
                    //save File into Document table

                    $agent_documents->filename = $name;
                    $agent_documents->type = 'Pan';

                }

                $agent_documents->save();
                $agent_documents = new AgentDocument();
                $agent_documents->agent_id = $agent->id;

                if($_FILES['adharcard']['tmp_name'] != false){
                    $image = $_FILES['adharcard']['tmp_name'];
                    $name = $_FILES['adharcard']['name'];

                    $destinationPath = public_path() . '/uploads/agents/Documents/';
                    $img = Image::make($image);
                    $img->resize(800, NULL, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    })->save($destinationPath.'/'.$name);

                    //$image = $_FILES['photo']['tmp_name'];
                    //$name = $_FILES['photo']['name'];
                    //$images = file_get_contents($image);
                    //$image = base64_encode($image);
                    //$file = public_path() .  '/uploads/pickups/' . $name;
                    //file_put_contents($file,$images);

                    //creating the upload url
                    $upload_url = 'https://docboyz.in/admin/public/uploads/pickups/';
                    //file url to store in the database
                    $file_url = $upload_url.$name;
                    //save File into Document table

                    $agent_documents->filename = $name;
                    $agent_documents->type = 'Aadhar';

                }
                $agent_documents->save();

            }
            if($saved) {
                return Response::json(array(
                    'error' => 0,
                    'agentId' => $agent->id,
                    'message' => 'Agent Registered Successfully !'),
                    200
                );
            } else {
                return Response::json(array(
                    'error' => 1,
                    'message' => 'Agent Registration failed. Please try again !'),
                    200
                );
            }

        } else {
            return Response::json(array(
                'error' => 1,
                'message' => 'Email already Registered ! Please try again '),
                200
            );
        }
    }

    // Api for agent Login
    public function login()
    {
        $email = Input::get('email');
        $fcmTokenId = Input::get('fcmTokenId');
        $password = Input::get('password');
        $login_type = Input::get('login_type');
        $social_login_token = Input::get('social_login_token');
        $is_android = Input::get('is_android');
        $p = Agent::where('email', $email)->first(array('id', 'password', 'social_login_token','name','TokenID'));
        //0 - Normal, 1 - Facebook, 2 - Google+
        if($login_type == 0)
        {
            if($p != NULL) {
                if (Hash::check($password, $p->password))
                {
                    $p->login_type = 0;
                    $p->is_android = $is_android;
                    $p->TokenID = $fcmTokenId;
                    $p->save();
                    return Response::json(array(
                        'error' => 0,
                        'agentId' => $p->id,
                        'name'=>$p->name,
                        'password_set' => !is_null($p->password)),
                        200
                    );

                } else {
                    return Response::json(array(
                        'error' => 1,
                        'agentId' => 1,
                        'message' => 'Invalid Credentials ! Please try again.'),
                        200
                    );
                }
            } else {
                return Response::json(array(
                    'error' => 1,
                    'agentId' => 1,
                    'message' => 'Email Not Registered ! Please try again.'),
                    200
                );
            }
        } else {
            if($p != NULL) {

                if ($social_login_token != NULL && $social_login_token == $p->social_login_token)
                {
                    $p->login_type = $login_type;
                    $p->is_android = $is_android;
                    $p->TokenID = $fcmTokenId;
                    $p->save();
                    return Response::json(array(
                        'error' => 0,
                        'agentId' => $p->id,
                        'name'=>$p->name,
                        'message' => 'Social Login Token Registered',
                        'password_set' => !is_null($p->password)),
                        200
                    );

                } else {

                    $p->login_type = $login_type;
                    $p->social_login_token = $social_login_token;
                    $p->is_android = $is_android;
                    $p->TokenID = $fcmTokenId;
                    $p->save();
                    return Response::json(array(
                        'error' => 0,
                        'agentId' => $p->id,
                        'name'=> $p->name,
                        'message' => 'Social Login Token Registered',
                        'password_set' => !is_null($p->password)),
                        200
                    );
                }
            } else {

                $name = Input::get('name');
                $email = Input::get('email');
                $mobile = Input::get('mobile');
                $gender = Input::get('gender');
                $address = Input::get('address');
                $city = Input::get('city');
                $state = Input::get('state');
                $pincode = Input::get('pincode');
                $fcmTokenId = Input::get('fcmTokenId');
                $status = Input::get('status');
                $dob = Input::get('dob');
                $degree  = Input::get('degree');
                $college = Input::get('college');
                $year = Input::get('year');

                $agent = new Agent();
                $agent->name = $name;
                $agent->email = $email;
                if(Input::has('mobile'))
                    $agent->mobile = $mobile;
                if(Input::has('gender'))
                    $agent->gender = $gender;
                if(Input::has('dob'))
                    $agent->dob = $dob;
                if(Input::has('state'))
                    $agent->state = $state;
                if(Input::has('address'))
                    $agent->address = $address;
                if(Input::has('pincode'))
                    $agent->pincode = $pincode;
                if(Input::has('status'))
                    $agent->status = $status;
                if(Input::has('city'))
                    $agent->city = $city;

                $agent->TokenID = $fcmTokenId;
                $agent->login_type = $login_type;
                $agent->social_login_token = $social_login_token;
                $agent->is_android = $is_android;

                if (Input::has('profile_pic') )
                {
                    $img = Input::get('profile_pic');
                    if($img != NULL)
                    {
                        $name = uniqid();

                        $img = str_replace('data:image/png;base64,', '', $img);
                        $img = str_replace(' ', '+', $img);
                        $data = base64_decode($img);
                        $file = public_path() . '/public/uploads/agents/' . $name . '.png';
                        $success = file_put_contents($file, $data);
                        $agent->profile_pic = $name . '.png';
                    }
                }

                $saved = $agent->save();

                if($saved){
                    $agent_education = new AgentEducation();
                    $agent_education->agent_id = $agent->id;
                    $agent_education->degree = $degree;
                    $agent_education->college = $college;
                    $agent_education->year = $year;
                    $agent_education->save();
                    $agent_documents = new AgentDocument();
                    $agent_documents->agent_id = $agent->id;
                    if(Input::has('pancard'))
                    {
                        $pancard =  Input::get('pancard');
                        if($pancard != NULL)
                        {
                            $filename = uniqid();
                            $img = str_replace('data:image/png;base64,', '', $pancard);
                            $img = str_replace(' ', '+', $img);
                            $data = base64_decode($img);
                            $file = public_path() . '/uploads/agents/Documents/' . $filename . '.png';
                            $success = file_put_contents($file, $data);
                            $agent_documents->filename = $filename . '.png';
                            $agent_documents->type = 'Pan';
                        }
                    }
                    $agent_documents->save();
                    $agent_documents = new AgentDocument();
                    $agent_documents->agent_id = $agent->id;
                    if(Input::has('adharcard'))
                    {
                        $adhar =  Input::get('adharcard');
                        if($adhar != NULL)
                        {
                            $adharname = uniqid();
                            $img = str_replace('data:image/png;base64,', '', $adhar);
                            $img = str_replace(' ', '+', $img);
                            $data = base64_decode($img);
                            $file = public_path() . '/uploads/agents/Documents/' . $adharname . '.png';
                            $success = file_put_contents($file, $data);
                            $agent_documents->filename = $adharname . '.png';
                            $agent_documents->type = 'Aadhar';
                        }
                    }
                    $agent_documents->save();

                }
                if($saved) {
                    return Response::json(array(
                        'error' => 0,
                        'agentId' => $agent->id,
                        'password_set' => !is_null($agent->password),
                        'message' => 'Agent Registered Successfully !'),
                        200
                    );
                } else {
                    return Response::json(array(
                        'error' => 1,
                        'message' => 'Agent Registration failed. Please try again !'),
                        200
                    );
                }
            }
        }
    }

    //Api For Dashboard
    public  function  dashboard(){
        $agent_Id = Input::get('agentId');
        $fcmTokenId = Input::get('fcmTokenId');
        $data = array();
        //update fcntokenId
        $agent = Agent::where('id',$agent_Id)->first();
        if($agent != null){
            Agent::where('id', $agent_Id)->update(['TokenID' => $fcmTokenId]);
        }

        //fetch Version
        $version_details = MobileSetting::first();

        //agent_pickups
        $agent_pickups = Pickup::where('agent_id',$agent_Id)->where('status','!=','Completed')->count();

        //current_month
        $current_month_pickups = Pickup::where('agent_id',$agent_Id)
            ->where('status','Completed')
            ->whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::now()->month)
            ->count();

        $current_month_earned = Pickup::where('agent_id',$agent_Id)
            ->where('status','Completed')
            ->whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::now()->month)
            ->sum('price');

        //current year
        $current_year_pickups = Pickup::where('agent_id',$agent_Id)
             ->where('status','Completed')
             ->whereYear('created_at', Carbon::now()->year)
                    ->count();
        $current_year_earned = Pickup::where('agent_id',$agent_Id)
            ->where('status','Completed')
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('price');

        //last month
        $last_month_pickups = Pickup::where('agent_id',$agent_Id)
            ->where('status','Completed')
            ->whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::now()->subMonth()->month)
           ->count();
        $last_month_earned = Pickup::where('agent_id',$agent_Id)
            ->where('status','Completed')
            ->whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::now()->subMonth()->month)->sum('price');

        $data['agent_pickups'] = $agent_pickups;
        $data['current_month_pickups'] = $current_month_pickups;
        $data['current_month_earned'] = $current_month_earned;
        $data['last_month_pickups'] = $last_month_pickups;
        $data['last_month_earned'] = $last_month_earned;
        $data['current_year_pickups'] = $current_year_pickups;
        $data['current_year_earned'] = $current_year_earned;
        $data['version'] = $version_details->key.' '.$version_details->value;

        if($data != NULL) {
            return Response::json(array(
                'error' => 0,
                'pickups' => $data),
                200
            );
        } else {
            return Response::json(array(
                'error' => 1,
                'message' => 'Invalid Agent Id.'),
                200
            );
        }

    }

    // Active Pickup List For Display Active Pickups
    public function active_pickup_list(){
        $agent_Id = Input::get('agentId');
        $agent_city = Agent::where('id',$agent_Id)->first();
        if($agent_city != null){
            $active_pickups = Pickup::join('company_products','company_products.id','pickups.product_id')
                ->whereNull('pickups.agent_id')
                ->where('pickups.city',$agent_city->city)
                ->where('pickups.status','!=','UnPublished')
                 ->orderBy('pickups.id','desc')
                ->select(array(
                    'pickups.id as pickup_id',
                    'pickups.pickup_date',
                    'pickups.pickup_person',
                    'pickups.status as pickup_status',
                    'pickups.pincode as pincode',
                    'pickups.home_address as home_address',
                    'pickups.city as city',
                    'pickups.state as state',
                    'pickups.office_pincode as office_pincode',
                    'pickups.office_address as 	office_address',
                    'pickups.office_city as office_city',
                    'pickups.office_state as office_state',
                    'company_products.name as product_name',
                    'pickups.price as pickup_price',
                    'pickups.mobile',
                      'pickups.cheque_amt',
                    'pickups.loan_amt',
                    'pickups.preferred_start_time',
                    'pickups.preferred_end_time'))->paginate(10);

            if($active_pickups->isEmpty()) {
                return Response::json(array(
                    'error' => 1,
                    'message' => 'There is No Active Pickups Yet'),
                    200
                );
            } else {

                foreach($active_pickups as $pickup)
                {
                    $links = PickupDocument::join('pickup_document_links', 'pickup_documents.id', 'pickup_document_links.pickup_document_id')
                             ->where('pickup_documents.pickup_id', $pickup->pickup_id)
                             ->count('pickup_document_links.id');
                    $pickup->has_link = $links;
                    if($links > 0)
                        $pickup->has_link = 1;
                }
                return Response::json(array(
                    'error' => 0,
                    'pickups' => $active_pickups),
                    200
                );
            }
        }
    }



    //Past Pickup List of Perticular Agent
    public function past_pickup_list(){
        $agent_Id = Input::get('agentId');
        $past_pickups = Pickup::join('company_products','company_products.id','pickups.product_id')
            ->where('pickups.agent_id',$agent_Id)
            ->where('pickups.status','!=','UnPublished')
            ->select(array(
                'pickups.id as pickup_id',
                'pickups.pickup_date',
                'pickups.pickup_person',
                'pickups.status as pickup_status',
                'pickups.pincode as pincode',
                'pickups.home_address as home_address',
                'pickups.city as city',
                'pickups.state as state',
                'pickups.office_pincode as office_pincode',
                'pickups.office_address as 	office_address',
                'pickups.office_city as office_city',
                'pickups.office_state as office_state',
                'company_products.name as product_name',
                'pickups.price as pickup_price',
                'pickups.mobile',
                'pickups.preferred_start_time',
                'pickups.preferred_end_time'))->paginate(10);
        if($past_pickups->isEmpty()) {
            return Response::json(array(
                'error' => 1,
                'message' => 'There is No Past Pickups Yet'),
                200
            );
        } else {
            return Response::json(array(
                'error' => 0,
                'pickups' => $past_pickups),
                200
            );
        }
    }

    //Acecpt Pickup Api
    public function accept_pickup(){
        $agent_Id = Input::get('agentId');
        $pickupId = Input::get('pickupId');

        //check if Agent is Approved or not
        $checkAgent = Agent::where('id',$agent_Id)->where('status','Approved')->first();
        if($checkAgent != null){
            //check Pickup
            $pickup = Pickup::where('id',$pickupId)->first();
            if($pickup->agent_id != null){
                return Response::json(array(
                    'error' => 1,
                    'message' => 'This Pickups is Already Accepted.'),
                    200
                );
            }else{
                $pickup->agent_id = $agent_Id;
                $pickup->status = 'Accepted';
                if($pickup->save()){
                    $pickupDocuments = PickupDocument::where('pickup_id', $pickup->id)
                        ->orderby('sequence', 'asc')->get(array('id','question','is_image','sequence', 'comments'));
                    $pickup->documents = $pickupDocuments;
                    if($pickupDocuments != NULL)
                    {
                        foreach($pickupDocuments as $pickupDocument)
                        {
                            $document_links = PickupDocumentLinks::where('pickup_document_id',$pickupDocument->id)->get(array('id','name','link'));
                            $pictures = PickupDocumentPictures::where('pickup_document_id', $pickupDocument->id)->get(array('filename'));
                            if(count($pictures) > 0){
                                $pickupDocument->pictures = $pictures;
                            }
                            if(count($document_links) > 0){
                                $pickupDocument->links = $document_links;
                            }

                        }
                    }
                    return Response::json(array(
                        'error' => 0,
                        'pickup_detail' => $pickup),
                        200
                    );
                }
            }
        }else{
            return Response::json(array(
                'error' => 1,
                'message' => 'Sorry, Agent is Not Approved !!'),
                200
            );
        }

    }

    //Pickup Details List, After Accepting Pickups
    public function pickup_details(){
        $pickupId = Input::get('pickupId');
        $pickup = Pickup::where('id', $pickupId)->first();
        if($pickup == NULL) {
            return Response::json(array(
                'error' => 1,
                'message' => 'Pickup Id is Incorrect'),
                200

            );
        } else {
            $pickupDocuments = PickupDocument::where('pickup_id', $pickup->id)
                ->orderby('sequence', 'asc')->get(array('id','question', 'sequence', 'comments','is_image'));
            $pickup->documents = $pickupDocuments;
            if($pickupDocuments != NULL)
            {
                foreach($pickupDocuments as $pickupDocument)
                {
                    $document_links = PickupDocumentLinks::where('pickup_document_id',$pickupDocument->id)->get(array('name','link'));
                    $pictures = PickupDocumentPictures::where('pickup_document_id', $pickupDocument->id)->get(array('filename'));
                    if(count($pictures) > 0){
                        $pickupDocument->pictures = $pictures;
                    }
                    if(count($document_links) > 0){
                        $pickupDocument->links = $document_links;
                    }

                }
            }

            return Response::json(array(
                'error' => 0,
                'pickups' => $pickup),
                200
            );
        }
    }

    public function pickup_upload(){
        $pickupId = Input::get('pickupId');
        $documents = Input::get('documents');
        if($pickupId == NULL){
            return Response::json(array(
                'error' => 1,
                'message' => 'PickupId Is Null'),
                200

            );
        }
        if($documents == NULL){
            return Response::json(array(
                'error' => 1,
                'message' => 'Documents Is Null'),
                200

            );
        }
        if(Input::has('documents'))
            {
                $num_elements = 0;
                $sqlData = array();
                while($num_elements < count($documents)){

                    $item = PickupDocument::where('id', $documents[$num_elements]['id'])->first();


                    if ($item != NULL)
                    {
                        $filename = NULL;
                        if($documents[$num_elements]['photo'] != NULL)
                        {
                            $filename = uniqid();
                            $img = str_replace('data:image/jpg;base64,', '', $documents[$num_elements]['photo']);
                            $img = str_replace(' ', '+', $img);
                            $data = base64_decode($img);
                            $file = public_path() . '/uploads/pickups/' . $filename . '.jpg';
                            $success = file_put_contents($file, $data);
                            $filename = $filename . '.jpg';
                        }
                        if($documents[$num_elements]['comments'] != NULL){
                            $item->comments =  $documents[$num_elements]['comments'];
                            $item->save();

                        }
                        if($documents[$num_elements]['photo'] != NULL){
                            $sqlData[] = array(
                                'pickup_document_id'     => $documents[$num_elements]['id'],
                                'filename'               => $filename,
                                'latitude'               => $documents[$num_elements]['latitude'],
                                'longitude'              => $documents[$num_elements]['longitude']
                            );

                        }
                    }
                    $num_elements++;
                }

                $saved =  DB::table('pickup_document_pictures')->insert($sqlData);


            } else {
                return Response::json(array(
                    'error' => 1,
                    'message' => 'Please upload pickup documents.'),
                    200
                );
            }
            if($saved) {
                return Response::json(array(
                    'error' => 0,
                    'message' => 'Pickup documents saved successfully'),
                    200

                );
            } else {
                return Response::json(array(
                    'error' => 1,
                    'message' => 'Pickup documents not uploaded. Please try again'),
                    200
                );
            }
    }

    public function PickupPdf()
    {
        $pickupId = Input::get('pickupId');
        $action = Input::get('action');
        if($pickupId != null){
            if($action == 1) {
                $Pickup_status = Pickup::where('id', $pickupId)->first();
                $Pickup_status->status = 'Document Submited';
                $Pickup_status->save();
            }
            $submited_documents = PickupDocument::where('pickup_id',$pickupId)
                ->orderBy('sequence','asc')->get(array('id','question', 'sequence', 'comments'));

            if(count($submited_documents) > 0)
            {
                foreach($submited_documents as $submited_document)
                {
                    $pictures = PickupDocumentPictures::where('pickup_document_id', $submited_document->id)
                        ->get(array('id','filename', 'latitude', 'longitude'));
                        $submited_document->pictures = $pictures;
                }
            }

            $Pickups = Pickup::where('id',$pickupId)->first(array('agent_id','application_id','product_id','completed_at','pod_number','pod_number','delivery_number','status','home_address','city','state','pincode','pickup_date','completed_at','pickup_person','id'));
            $document_submit_date = PickupDocument::join('pickup_document_pictures','pickup_document_pictures.pickup_document_id','pickup_documents.id')
                ->where('pickup_documents.pickup_id',$pickupId)
                ->first(array('pickup_document_pictures.created_at'));
            $agents = Agent::where('id',$Pickups->agent_id)->first(array('name','email','mobile','status','id'));
            $category = CompanyProduct::where('id',$Pickups->product_id)->first(array('name'));

            //mail to customer
            $CustomerId= Pickup::where('id',$pickupId)->first(array('customer_id'));
            $customer_mail = Customer::where('id',$CustomerId->customer_id)->first(array('id','email','name','company_name'));

            //pdf code
            $pdf = PDF::loadView('backEnd.pickups.pdf', compact('agents','Pickups','submited_documents','document_submit_date'));
            $pdf->setPaper('A4', 'portrait');
            $str = $customer_mail->company_name.'_'.$category->name.'_'.$Pickups->application_id.'_'.$Pickups->pickup_person;
            $pdf_file_name = str_replace(' ', '', $str);
            $pdf_name = trim($pdf_file_name).'.pdf';
            $file = public_path() . '/uploads/pickups/pdf/'.$pdf_file_name. '.pdf';

            $pdf->save($file);

            file_put_contents($file, $pdf->output());
            //                $pickup_file =  URL::to('/downloads/' .$pdf_file_name. '.pdf');
            if ($customer_mail != null) {
                $data = new \stdClass();
                $data->name = $customer_mail->name;
                $data->pickup_name = $pdf_name;
                $data->subject_name = $category->name.' '.$Pickups->application_id.' '.$Pickups->pickup_person;
                $data->pdf_link = url('/uploads/pickups/pdf/' .$pdf_file_name. '.pdf');
                $data->agent_detail = $agents->name.'-'.$agents->id;

                if($customer_mail->email_cc != null){
                    $customers =  preg_split ("/\,/", $customer_mail->email_cc);
                       foreach($customers as $index =>$cusomer_mail){
                           if($cusomer_mail!= Null){
                               $data->cc_1 = $customers;
                           }
                       }
                   }
                Mail::to($customer_mail->email)
                    ->send(new Pickuppdf($file,$data));
                return Response::json(array(
                    'error' => 0,
                    'message' => 'Pickup Mail Send successfully'),
                    200
                );
            }else{
                return Response::json(array(
                    'error' => 1,
                    'message' => 'Pickup Mail not Send. Please try again'),
                    200
                );
            }
        }else{
            return Response::json(array(
                'error' => 1,
                'message' => 'Do Not Perform Any Action'),
                200
            );
        }
    }

    public function pickup_submit(){
        $pickupId = Input::get('pickupId');
        $delivery_number = Input::get('delivery_number');
        $Pickups = Pickup::where('id', $pickupId)->first();

        if(Input::has('pod_number'))
        {
            $pod_number =  Input::get('pod_number');
            if($pod_number != NULL)
            {
                $filename = uniqid();
                $img = str_replace('data:image/png;base64,', '', $pod_number);
                $img = str_replace(' ', '+', $img);
                $data = base64_decode($img);
                $file = public_path() . '/uploads/pickups/' . $filename . '.png';
                $success = file_put_contents($file, $data);
                $Pickups->pod_number = $filename . '.png';
            }
        }

        $Pickups->delivery_number = $delivery_number;
        $Pickups->status = 'Completed';
        $Pickups->completed_at = Carbon::now();
        $saved =  $Pickups->save();
        $transaction = new AgentTransaction();
        $transaction->agent_id = $Pickups->agent_id;
        $transaction->pickup_id = $pickupId;
        $transaction->type = 'Add';
        $transaction->amount = $Pickups->price;
        $transaction->save();

        $agent = Agent::where('id', $Pickups->agent_id)->first();
        $agent->wallet_amt = $agent->wallet_amt + $Pickups->price;
        $agent->save();
        if($saved) {
        //            PickupPdf($pickupId);
            return Response::json(array(
                'error' => 0,
                'message' => 'Pickup documents saved successfully'),
                200

            );
        } else {
            return Response::json(array(
                'error' => 1,
                'message' => 'Pickup documents not uploaded. Please try again'),
                200
            );
        }
    }



    public function document_collection_list(Request $request){
        $pickup_documents = new PickupDocument();
        if(Input::has('filename'))
        {
            $file_name =  Input::get('filename');
            if($file_name != NULL)
            {
                $name = uniqid();
                $img = str_replace('data:image/png;base64,', '', $file_name);
                $img = str_replace(' ', '+', $img);
                $data = base64_decode($img);
                $file = public_path() . '/uploads/agents/' . $name;
                $success = file_put_contents($file, $data);
                $pickup_documents->filename = $name;

            }
        }
        $pickup_documents->comments = Input::get('comments');
        $pickup_documents->sequence = Input::get('sequence');
        if($pickup_documents->save()) {
            return Response::json(array(
                'error' => 0,
                'pickups' => "Document Uploaded Successfully"),
                200
            );
        } else {
            return Response::json(array(
                'error' => 1,
                'message' => 'There error while Uploading.'),
                200
            );
        }
    }

    public function forgotpassword()
    {
        $agent = Agent::where('email', Input::get('email'))->first(array('id', 'name', 'TokenID', 'email'));
        if (!$agent) {
            return Response::json(array(
                'error' => 1,
                'message' => 'Email Address not registered'),
                200
            );
        }
        $data = new \stdClass();
        $data->name = $agent->name;
        $data->forgotPasswordUrl = URL::route('forgot-password-confirm', [$agent->id, $agent->TokenID]);
        // Send the activation code through email
        Mail::to($agent->email)
            ->send(new Reminder($data));
        if (Mail::failures()) {
            // return response showing failed emails
            return Response::json(array(
                'error' => 1,
                'message' => 'Email sending failed. Please try again.'),
                200
            );
        } else {
            return Response::json(array(
                'error' => 0,
                'message' => 'Password link is send to the registered email id.'),
                200
            );
        }

    }


    /**
     * Forgot Password Confirmation page.
     *
     * @param number $userId
     * @param  string $passwordResetCode
     * @return View
     */
    public function getForgotPasswordConfirm($userId,$token)
    {
        // Find the user using the password reset code
        if(!$agent = Agent::where('id', $userId)->first()) {
            dd('Account associated to link not found.');
        }
        $agent = Agent::where('id', $userId)->first();

        // Show the page
        return view('emails.forgotpwd-confirm',compact('userId', 'agent'));
    }
    public function postForgotPasswordUpdate($userId,Request $request)
    {
        $password = $request->get('password');
        $password_confirm = $request->get('password_confirm');

        if($password != $password_confirm)
            return Redirect::back()->with('error', 'Password & Confirm Password does not match');

        $agent = Agent::where('id', $userId)->first();
        $agent->password = Hash::make($password);
        $saved = $agent->save();
        if($saved) {
            return Redirect::back()->with('success', 'Password reset successful.');
        }else{
            return Redirect::back()->with('error', 'Password reset failed. Please try again');
        }
    }

    public function viewProfile(){
        $agent_Id = Input::get('agentId');
        $agent = Agent::where('id', $agent_Id)->first();

        if($agent != NULL) {

            $agentDocuments = AgentDocument::where('agent_id', $agent_Id)->get(array('type', 'filename'));
            $agent->documents = $agentDocuments;

            $agentEducation = AgentEducation::where('agent_id', $agent_Id)->get(array('degree', 'college', 'year'));
            $agent->education = $agentEducation;

            $agentAccountDetails = AgentBankdetails::where('agent_id', $agent_Id)->get(array('bank_name','account_type','ifsc_code','account_number'));
            $agent->bankdetails = $agentAccountDetails;

            return Response::json(array(
                'error' => 0,
                'user' => $agent->toArray()),
                200
            );
        } else {
            return Response::json(array(
                'error' => 1,
                'message' => 'Invalid Agent Id.'),
                200
            );
        }

    }

    public function requestRedeem(){
        $agent_Id = Input::get('agentId');
        $amount = Input::get('amount');
        $AgentWallet = Agent::where('id',$agent_Id)->first();
        if($AgentWallet->wallet_amt < $amount){
            return Response::json(array(
                'error' => 1,
                'message' => ' Redeem Amount is Less Than Agent Wallet Amount.'),
                200
            );
        }else{
            $redeem_amounts = new AgentReedem();
            $redeem_amounts->amount = $amount;
            $redeem_amounts->agent_id = $agent_Id;
            if($redeem_amounts->save()){
                $Redeem = AgentReedem::where('id', $redeem_amounts->id)->first();
                if($Redeem->status != 'Paid'){
                    $RedeemAmount = $AgentWallet->wallet_amt - $amount;

                    //Save Redeem Amount to Agent Wallet
                    $AgentWallet->wallet_amt = $RedeemAmount;
                    $AgentWallet->save();
                    //Save into Agent
                    $Redeem->wallet_amt = $RedeemAmount;
                    $Redeem->save();

                    //Save Status to Transaction
                    $Transaction = new  AgentTransaction();
                    $Transaction->agent_id = $Redeem->agent_id;
                    $Transaction->redeem_id = $Redeem->id;
                    $Transaction->type = 'Redeem';
                    $Transaction->amount = $Redeem->amount;
                    $Transaction->save();
                }
                return Response::json(array(
                    'error' => 0,
                    'message' => 'Redeem Amount Save Succesfully',
                    'wallet_amt' => $RedeemAmount),
                    200
                );
            }
        }

    }

     Public function redeemHistory(){
         $agent_Id = Input::get('agentId');
         $redeemHistory = AgentReedem::where('agent_id',$agent_Id)->get(array('agent_id','amount','status','completed_by','created_at'));
         if($redeemHistory->isEmpty()){
             return Response::json(array(
                 'error' => 1,
                 'message' => 'There is No Redeem History'),
                 200
             );
         }else{
             return Response::json(array(
                 'error' => 0,
                 'user' => $redeemHistory->toArray()),
                 200
             );
         }

     }



    public function updateProfile()
    {
        $agent_Id = Input::get('agentId');
        $profile = Agent::findOrFail($agent_Id);
        if ( Input::has('name') && (Input::get('name') != NULL) )
        {
            $profile->name = Input::get('name');
        }
        if ( Input::has('email') && (Input::get('email') != NULL) )
        {
            $profile->email = Input::get('email');
        }
        if ( Input::has('mobile') && (Input::get('mobile') != NULL) )
        {
            $profile->mobile = Input::get('mobile');
        }
        if ( Input::has('gender') && (Input::get('gender') != NULL) )
        {
            $profile->gender = Input::get('gender');
        }
        if ( Input::has('address1') && (Input::get('address1') != NULL) )
        {
            $profile->address1 = Input::get('address1');
        }
        if ( Input::has('address2') && (Input::get('address2') != NULL) )
        {
            $profile->address2 = Input::get('address2');
        }
        if ( Input::has('state') && (Input::get('state') != NULL) )
        {
            $profile->state = Input::get('state');
        }
        if ( Input::has('pincode') && (Input::get('pincode') != NULL) )
        {
            $profile->pincode = Input::get('pincode');
        }
        if ( Input::has('dob') && (Input::get('dob') != NULL) )
        {
            $dob = Input::get('dob');
            $profile->dob = Carbon::parse($dob)->format('Y-m-d');
        }

        if ( Input::has('password') && (Input::get('password') != NULL) )
        {
            $profile->password = Hash::make(Input::get('password'));
        }

        if (Input::has('profile_pic') && (Input::get('profile_pic') != NULL) )
        {
            $img = Input::get('profile_pic');
            if($img != NULL)
            {
                $name = uniqid();

                $img = str_replace('data:image/png;base64,', '', $img);
                $img = str_replace(' ', '+', $img);
                $data = base64_decode($img);
                $file = public_path() . '/uploads/agents/' . $name . '.png';
                $success = file_put_contents($file, $data);

                $profile->profile_pic = $name . '.png';
            }
        }

        $saved = $profile->save();
        $agentEducation = AgentEducation::where('agent_id',$agent_Id)->first();
        if($agentEducation != null){
            if ( Input::has('degree') && (Input::get('degree') != NULL) )
            {
                $profile->degree = Input::get('degree');
            }
            if ( Input::has('college') && (Input::get('college') != NULL) )
            {
                $profile->college = Input::get('college');
            }
            if ( Input::has('year') && (Input::get('year') != NULL) )
            {
                $profile->year = Input::get('year');
            }
            $agentEducation->save();
        }else{
            $agent_eduction = new AgentEducation();
            $agent_eduction->degree =  Input::get('degree');
            $agent_eduction->college = Input::get('college');
            $agent_eduction->year = Input::get('year');
            $agent_eduction->save();

        }

        //account info
        $agentbankdetail = AgentBankdetails::where('agent_id',$agent_Id)->first();
        if($agentbankdetail != null) {
            if ( Input::has('bank_name') && (Input::get('bank_name') != NULL) ){
                $agentbankdetail->bank_name = Input::get('bank_name');
            }
            if ( Input::has('ifsc_code') && (Input::get('ifsc_code') != NULL) ){
                $agentbankdetail->ifsc_code = Input::get('ifsc_code');
            }
            if ( Input::has('account_number') && (Input::get('account_number') != NULL) ){
                $agentbankdetail->account_number = Input::get('account_number');
            }
            if ( Input::has('account_type') && (Input::get('account_type') != NULL) ){
                $agentbankdetail->account_type = Input::get('account_type');
            }
            $agentbankdetail->save();
        }

        $AdharDocments = AgentDocument::where('agent_id',$agent_Id)->where('type','Aadhar')->first();
        if($AdharDocments != null){
            if(Input::has('adharcard') && (Input::get('adharcard') != NULL))
            {
                $adhar =  Input::get('adharcard');
                if($adhar != NULL)
                {
                    $adharname = uniqid();
                    $img = str_replace('data:image/png;base64,', '', $adhar);
                    $img = str_replace(' ', '+', $img);
                    $data = base64_decode($img);
                    $file = public_path() . '/uploads/agents/Documents/' . $adharname . '.png';
                    $success = file_put_contents($file, $data);
                    $AdharDocments->filename = $adharname . '.png';
                    $AdharDocments->type = 'Aadhar';
                    $AdharDocments->save();
                }
            }

        }else{
            $adharDocument = new AgentDocument();
            $adharDocument->agent_id = $profile->id;
            if(Input::has('adharcard'))
            {
                $pancard =  Input::get('adharcard');
                if($pancard != NULL)
                {
                    $filename = uniqid();
                    $img = str_replace('data:image/png;base64,', '', $pancard);
                    $img = str_replace(' ', '+', $img);
                    $data = base64_decode($img);
                    $file = public_path() . '/uploads/agents/Documents/' . $filename . '.png';
                    $success = file_put_contents($file, $data);
                    $adharDocument->filename = $filename . '.png';
                    $adharDocument->type = 'Aadhar';
                }
            }
            $adharDocument->save();
        }

        $Pandocuments = AgentDocument::where('agent_id',$agent_Id)->where('type','Pan')->first();
        if($Pandocuments != null){
            if(Input::has('pancard') && (Input::get('pancard') != NULL))
            {
                $adhar =  Input::get('pancard');
                if($adhar != NULL)
                {
                    $adharname = uniqid();
                    $img = str_replace('data:image/png;base64,', '', $adhar);
                    $img = str_replace(' ', '+', $img);
                    $data = base64_decode($img);
                    $file = public_path() . '/uploads/agents/Documents/' . $adharname . '.png';
                    $success = file_put_contents($file, $data);
                    $Pandocuments->filename = $adharname . '.png';
                    $Pandocuments->type = 'Pan';
                    $Pandocuments->save();
                }
            }


        }else{
            $pan_document = new AgentDocument();
            $pan_document->agent_id = $profile->id;
            if(Input::has('pancard'))
            {
                $adhar =  Input::get('pancard');
                if($adhar != NULL)
                {
                    $adharname = uniqid();
                    $img = str_replace('data:image/png;base64,', '', $adhar);
                    $img = str_replace(' ', '+', $img);
                    $data = base64_decode($img);
                    $file = public_path() . '/uploads/agents/Documents/' . $adharname . '.png';
                    $success = file_put_contents($file, $data);
                    $pan_document->filename = $adharname . '.png';
                    $pan_document->type = 'Pan';
                }
            }
            $pan_document->save();
        }

        if($saved) {
            return Response::json(array(
                'error' => 0,
                'message' => 'Agent Profile Updated Successfully !'),
                200
            );
        } else {
            return Response::json(array(
                'error' => 1,
                'message' => 'Agent Updation failed. Please try again !'),
                200
            );
        }
    }

    public function notificationHistory()
    {
        $agent_Id = Input::get('agentId');
        $notifications = MobileNotification::where('agent_id', $agent_Id)
            ->orderBy('id', 'DESC')
            ->select(array('id', 'agent_id','type','created_at'))
            ->paginate(10);
        if($notifications->isEmpty())
        {
            return Response::json(array(
                'error' => 1,
                'message' => 'No Alerts Found !'),
                200
            );
        } else {
            return Response::json(array(
                'error' => 0,
                'alert' => $notifications->toArray()),
                200
            );
        }
    }

    public function deleteNotification(){
        $notificationIds = Input::get('notificationId');

        foreach($notificationIds as $notificationId)
        {
            MobileNotification::where('id',$notificationId)->delete();
        }
        return Response::json(array(
            'error' => 0,
            'alert' => 'Alert has been deleted'),
            200
        );
    }


    public function fcm()
    {

       $token = Input::get('device_id');
       $optionBuiler = new OptionsBuilder();
       $optionBuiler->setTimeToLive(60*20);
       $notificationBuilder = new PayloadNotificationBuilder();
       $notificationBuilder->setBody('Please Approve !')
            ->setSound('default');

        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData(['title' => 'title', 'description' => 'Description', 'type' => 'news']);
        $option = $optionBuiler->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();

        $downstreamResponse = FCM::sendTo($token, $option, $notification, $data);

        $downstreamResponse->numberSuccess();
        $downstreamResponse->numberFailure();
        $downstreamResponse->numberModification();
        //return Array - you must remove all this tokens in your database
        $downstreamResponse->tokensToDelete();
        //return Array (key : oldToken, value : new token - you must change the token in your database )
        $downstreamResponse->tokensToModify();
        //return Array - you should try to resend the message to the tokens in the array
        $downstreamResponse->tokensToRetry();
        // return Array (key:token, value:errror) - in production you should remove from your database the tokens

        return Response::json(array(
            'error' => $downstreamResponse->tokensWithError(),
            'success' => $downstreamResponse->numberSuccess(),
            'failure' => $downstreamResponse->numberFailure(),
            'modify' => $downstreamResponse->numberModification(),
            'to_delete' => $downstreamResponse->tokensToDelete(),
            'to_modify' => $downstreamResponse->tokensToModify(),
            'to_retry' => $downstreamResponse->tokensToRetry(),
        ),
            200
        );
    }

     public function assignPickupList(){
        $agent_Id = Input::get('agentId');
        $pickuplist = Input::get('pickuplist');
        $deleted_pickups = Pickup::where('agent_id',$agent_Id)->wherein('id',$pickuplist)->orderBy('id','desc')->onlyTrashed()->get(array('id'));
        $assignpickups = Pickup::join('company_products','company_products.id','pickups.product_id')
		->where('pickups.agent_id',$agent_Id)->whereNotIn('pickups.id',$pickuplist)
            ->where('pickups.status','!=','Completed')->get(array('pickups.id',
                    'pickups.pickup_date',
                    'pickups.pickup_person',
                    'pickups.status',
                    'pickups.pincode',
                    'pickups.home_address',
                    'pickups.city',
                    'pickups.state',
                    'pickups.office_pincode',
                    'pickups.office_address',
                    'pickups.office_city',
                    'pickups.office_state',
                    'company_products.name as product_name',
                    'pickups.price',
                    'pickups.mobile',
                    'pickups.preferred_start_time',
                    'pickups.preferred_end_time'));
        foreach ($assignpickups as $assignpickup){
            $pickupDocuments = PickupDocument::where('pickup_id', $assignpickup->id)
                ->orderby('sequence', 'asc')->get(array('id', 'question', 'is_image', 'sequence', 'comments'));
            $assignpickup->has_link = 0;
            foreach($pickupDocuments as $pickupDocument)
            {
                $links = PickupDocumentLinks::where('pickup_document_id', $pickupDocument->id)->get(array('id', 'name', 'link'));
                $pickupDocument->links = $links;
                if(!$links->isEmpty())
                    $assignpickup->has_link = 1;
            }
            $assignpickup->documents = $pickupDocuments;
        }
        if($assignpickups->isEmpty() && $deleted_pickups->isEmpty())
        {
            return Response::json(array(
                'error' => 1,
                'message' => 'Pickups Not Found !'),
                200
            );
        }elseif(count($deleted_pickups) > 0 || count($assignpickups) > 0){
            return Response::json(array(
                'error' => 0,
                'deleted_pickups'=>$deleted_pickups,
                'assign_pickups' => $assignpickups),
                200
            );
        }else{
            return Response::json(array(
                'error' => 1,
                'message' => 'Something Went Wrong !'),
                200
            );
        }

    }

     public function pickup_upload2(Request $request){
        $pickupId = $request->get('pickupId');
        $documentID =  $request->get('documentId');
        $comments = Input::get('comments');
        if(!$pickupId || !$documentID){
            return Response::json(array(
                'error' => 1,
                'message' => 'PickupID Is Null'),
                200
            );
        }else{
            if($_FILES['photo']['tmp_name'] != false){

                $image = $_FILES['photo']['tmp_name'];
                $name = $_FILES['photo']['name'];

                $destinationPath = public_path() . '/uploads/pickups/';
                $img = Image::make($image);
                $img->resize(800, NULL, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })->save($destinationPath.'/'.$name);

                //$image = $_FILES['photo']['tmp_name'];
                //$name = $_FILES['photo']['name'];
                //$images = file_get_contents($image);
                //$image = base64_encode($image);
                //$file = public_path() .  '/uploads/pickups/' . $name;
                //file_put_contents($file,$images);

                //creating the upload url
                $upload_url = 'https://docboyz.in/admin/public/uploads/pickups/';
                //file url to store in the database
                $file_url = $upload_url.$name;
                //save File into Document table
                $pickup_document_image = new PickupDocumentPictures();
                $pickup_document_image->pickup_document_id = $documentID;
                $pickup_document_image->filename = $name;
                $save = $pickup_document_image->save();
            }
            $item = PickupDocument::where('id',$documentID)->first();
            if($item != null && $comments != null && $comments != ''){
                $item->comments = $comments;
                $item->save();

            }

            if($save){
                return Response::json(array(
                    'error' => 0,
                    'message' => 'Pickup documents saved successfully'),
                    200

                );
            } else {
                return Response::json(array(
                    'error' => 1,
                    'message' => 'Pickup documents not uploaded. Please try again'),
                    200
                );
            }

        }
    }

     public function pickup_submit2(Request $request){

        $pickupId = $request->get('pickupId');
        $delivery_number = $request->get('delivery_number');
        $Pickups = Pickup::where('id', $pickupId)->first();

         if($_FILES['pod_number']['tmp_name'] != false)
        {
            $pod_number = $_FILES['pod_number']['tmp_name'];
            if($pod_number != NULL)
            {
                $name = $_FILES['pod_number']['name'];
                $extension = explode(".", strtolower($_FILES['pod_number']['name']));
                $random_name =   $filename = uniqid();
                $destinationPath = public_path() . '/uploads/pickups/';
                $img = Image::make($pod_number);
                $img->resize(800, NULL, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })->save($destinationPath.'/'.$random_name.'.'.$extension[1]);
                $Pickups->pod_number = $random_name.'.'.$extension[1];

            }
        }
       

        $Pickups->delivery_number = $delivery_number;
        $Pickups->status = 'Completed';
        $Pickups->completed_at = Carbon::now();
        $saved =  $Pickups->save();
        
        $transaction = new AgentTransaction();
        $transaction->agent_id = $Pickups->agent_id;
        $transaction->pickup_id = $pickupId;
        $transaction->type = 'Add';
        $transaction->amount = $Pickups->price;
        $transaction->save();

        $agent = Agent::where('id', $Pickups->agent_id)->first();
        $agent->wallet_amt = $agent->wallet_amt + $Pickups->price;

        $agent->save();
        if($saved) {
            //            PickupPdf($pickupId);
            return Response::json(array(
                'error' => 0,
                'message' => 'Pickup documents saved successfully'),
                200

            );
        } else {
            return Response::json(array(
                'error' => 1,
                'message' => 'Pickup documents not uploaded. Please try again'),
                200
            );
        }
    }

    public function mail_links(Request $request){
        $link_ids = $request->get('linkId');
        $pickup_id = $request->get('pickupId');
        //mail to Pickup Person
        $email ='poojakakde65@gmail.com';
        $person_details = Pickup::where('id',$pickup_id)->first(array('application_id','pickup_person','id','pickup_email'));
        if($person_details != NULL)
        {
            if($person_details->pickup_email != null){
                $email = $person_details->pickup_email;
            }
        }
        //Data for Link Mail
        $data = new \stdClass();
        $data->name = $person_details->pickup_person;
        $data->subject_name = $person_details->application_id.' '.$person_details->pickup_person.' '.'Links';
        $doc_links = PickupDocumentLinks::whereIn('id',$link_ids)->get();
        if(count($doc_links) > 0){
            $data->links = $doc_links;

            Mail::to($email)
                ->send(new documentLinkMail($data));

            if (Mail::failures()) {
                return Response::json(array(
                    'error' => 1,
                    'message' => 'Mail could not be sent. Please try again'),
                    200
                );
            }else{
                return Response::json(array(
                    'error' => 0,
                    'message' => 'Mail send Successfully !'),
                    200
                );
            }
        }else{
            return Response::json(array(
                'error' => 1,
                'message' => 'No Links Available'),
                200
            );
        }



    }


    public function RegeneratePickupPdf()
    {
        $pickupId = Input::get('pickupId');
        if($pickupId != null){

            $submited_documents = PickupDocument::where('pickup_id',$pickupId)
                ->orderBy('sequence','asc')->get(array('id','question', 'sequence', 'comments'));

            if(count($submited_documents) > 0)
            {
                foreach($submited_documents as $submited_document)
                {
                    $pictures = PickupDocumentPictures::where('pickup_document_id', $submited_document->id)
                        ->get(array('id','filename', 'latitude', 'longitude'));
                    foreach($pictures as $picture)
                    {
                        $img = Image::make(public_path() . '/uploads/pickups/' . $picture->filename);
                        $img->resize(800, NULL, function ($constraint) {
                            $constraint->aspectRatio();
                            $constraint->upsize();
                        })->save(public_path() . '/uploads/pickups/' . $picture->filename);
                    }

                    $submited_document->pictures = $pictures;
                }
            }
            $Pickups = Pickup::where('id',$pickupId)->first(array('agent_id','application_id','job_id','completed_at','pod_number','pod_number','delivery_number','status','address','city','state','pincode','pickup_date','completed_at','pickup_person','id'));
            $document_submit_date = PickupDocument::join('pickup_document_pictures','pickup_document_pictures.pickup_document_id','pickup_documents.id')
                ->where('pickup_documents.pickup_id',$pickupId)
                ->first(array('pickup_document_pictures.created_at'));
            $agents = Agent::where('id',$Pickups->agent_id)->first(array('name','email','mobile','status','id'));
            $category = Document_type_master::where('id',$Pickups->job_id)->first(array('name'));

            //mail to customer
            $CustomerId= Pickup::where('id',$pickupId)->first(array('customer_id'));
            $customer_mail = Customer::where('id',$CustomerId->customer_id)->first(array('id','email','name','company_name','email_cc'));

            //pdf code
            $pdf = PDF::loadView('backEnd.pickups.pdf', compact('agents','Pickups','submited_documents','document_submit_date'));
            $pdf->setPaper('A4', 'portrait');
            $str = $customer_mail->company_name.'_'.$category->name.'_'.$Pickups->application_id.'_'.$Pickups->pickup_person;
            $pdf_file_name = str_replace(' ', '', $str);
            $pdf_name = trim($pdf_file_name).'.pdf';
            $file = public_path() . '/uploads/pickups/pdf/'.$pdf_file_name. '.pdf';
            $pdf->save($file);
            file_put_contents($file, $pdf->output());
            //                $pickup_file =  URL::to('/downloads/' .$pdf_file_name. '.pdf');

            if ($customer_mail != null) {
                $data = new \stdClass();
                $data->name = $customer_mail->name;
                $data->pickup_name = $pdf_name;
                $data->subject_name = $category->name.' '.$Pickups->application_id.' '.$Pickups->pickup_person;
                $data->pdf_link = url('/public/uploads/pickups/pdf/' .$pdf_file_name. '.pdf');
                $data->agent_detail = $agents->name.'-'.$agents->id;
                $customers =  preg_split ("/\,/", $customer_mail->email_cc);
                foreach($customers as $index =>$cusomer_mail){
                    $data->cc_1 = $customers;
                }
                Mail::to($customer_mail->email)
                    ->send(new Pickuppdf($file,$data));
                return Response::json(array(
                    'error' => 0,
                    'message' => 'Pickup Mail Send successfully'),
                    200
                );
            }else{
                return Response::json(array(
                    'error' => 1,
                    'message' => 'Pickup Mail not Send. Please try again'),
                    200
                );
            }
        }else{
            return Response::json(array(
                'error' => 1,
                'message' => 'Do Not Perform Any Action'),
                200
            );
        }
    }


    public function Reschedule(){
        $pickupId = Input::get('pickupId');
        if(!$pickupId){
            return Response::json(array(
                'error' => 1,
                'message' => 'Please Provide PickupId'),
                200
            );
        }else{
            $reshedule_pickup = Pickup::where('id',$pickupId)->first();
            if($reshedule_pickup != null){
                $pickup_schedule = new PickupSchedule();
                $pickup_schedule->pickup_id = $reshedule_pickup->id;
                $pickup_schedule->pickup_date = Carbon::parse(Input::get('pickup_date'))->format('Y-m-d');
                $pickup_schedule->pickup_startime = Carbon::parse(Input::get('start_time'))->format('H:i');
                $pickup_schedule->pickup_endtime = Carbon::parse(Input::get('end_time'))->format('H:i');
                $pickup_schedule->comments = Input::get('comments');
                $pickup_schedule->created_by = $reshedule_pickup->agent_id;
                $pickup_schedule->is_agent = 1;
                $pickup_schedule->created_by = $reshedule_pickup->created_by;
                $pickup_schedule->save();
                if($pickup_schedule->save()){
                    $reshedule_pickup->preferred_start_time = Carbon::parse(Input::get('start_time'))->format('H:i');
                    $reshedule_pickup->preferred_end_time = Carbon::parse(Input::get('end_time'))->format('H:i');
                    $reshedule_pickup->pickup_date = Carbon::parse(Input::get('pickup_date'))->format('Y-m-d');
                    $reshedule_pickup->save();
                }
                return Response::json(array(
                    'error' => 0,
                    'message' => 'Pickup Reschedule Sucessfully!!'),
                    200
                );
            }else{
                return Response::json(array(
                    'error' => 1,
                    'message' => 'Pickup Not Available!!'),
                    200
                );
            }
        }

    }

    public function pickup_decline(){
        $pickupId = Input::get('pickupId');
        $pickups = Pickup::where('id',$pickupId)->first();
        if(!$pickups){
            return Response::json(array(
                'error' => 1,
                'message' => 'Pickup Not Available!!'),
                200
            );
        }else{
            //save into pickup  decline table
            $pickup_decline = new PickupDecline();
            $pickup_decline->pickup_id = $pickups->id;
            $pickup_decline->agent_id = $pickups->agent_id;
            $pickup_decline->comments = Input::get('comments');
            $pickup_decline->save();

            //update Pickup Table Entry
            $pickups->status = 'Published';
            $pickups->agent_id = Null;
           if($pickups->save()){
               return Response::json(array(
                   'error' => 0,
                   'message' => 'Pickup Decline Succesfully!!'),
                   200
               );
           }
        }

    }
   

   public  function lookup_data(){
        $category = Input::get('Category');
        if($category == null){
            return Response::json(array(
                'error' => 1,
                'message' => 'Category is Empty!!'),
                200
            );
        }
        $parent_id = Input::get('parentID');
        if ($parent_id != null && $category == 'City') {
            $city = Lookup::where('category', $category)->where('parent_id', $parent_id)->get();
        } else {
            $city = Lookup::where('category', $category)->get();
        }
        if($city->isEmpty()){
            return Response::json(array(
                'error' => 1,
                'message' => 'Value Not Available!!'),
                200
            );
        }else{
            return Response::json(array(
                'error' => 0,
                'message' => $city),
                200
            );
        }
    }




}

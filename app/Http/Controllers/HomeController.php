<?php

namespace App\Http\Controllers;



use App\Agent;
use App\AgentReedem;
use App\AnalyticsVisitor;
use App\CompanyProduct;
use App\Contact;
use App\Customer;
use App\Document;
use App\Document_type_master;
use App\Event;
use App\Http\Requests;
use App\Mail\Pickuppdf;
use App\Notifications\PickupAddNotification;
use App\Pickup;
use App\Pickup_Document;
use App\PickupDecline;
use App\PickupDocument;
use App\PickupDocumentPictures;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use App\Section;
use App\Topic;
use App\User;
use App\Webmail;
use App\WebmasterSection;
use Mail;
use Auth;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Carbon\CarbonInterval;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notification;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */


    public function index()
    {
        // General for all pages
        $GeneralWebmasterSections = WebmasterSection::where('status', '=', '1')->orderby('row_no', 'asc')->get();

        // Company
        $Company = array();
        $companies= Customer::orderBy('id', 'desc')->limit(10)->get();
        $ix = 0;
        foreach ($companies as $company) {
            $company_name = $company->company_name;
            $company_email = $company->email;
            $company_id = $company->id;
            $newdata = array(
                'code'=>strtoupper($company_name[0]),
                'company_name' => $company_name,
                'company_email' => $company_email,
                'company_id'=>$company_id,
            );
            array_push($Company, $newdata);
            $ix++;
        }

        $DeclinePickups = array();
        $declinepickups = PickupDecline::join('pickups','pickups.id','pickup_decline.pickup_id')
        ->orderBy('pickup_decline.id', 'desc')->limit(10)->get(array('pickups.pickup_person','pickups.id','pickup_decline.comments'));
        $ix = 0;
        foreach ($declinepickups as $declinepickup) {
            $pickup_person = $declinepickup->pickup_person;
            $comments = $declinepickup->comments;
            $pickup_id = $declinepickup->id;
            $newdata = array(
                'code'=>strtoupper($pickup_person[0]),
                'company_name' => $pickup_person,
                'company_email' => $comments,
                'company_id'=>$pickup_id,
            );
            array_push($DeclinePickups, $newdata);
            $ix++;
        }

        //Agents
        $Agents = Agent::orderby('id', 'desc')->limit(10)->get();

        //Pickups
        $Pickups = Pickup::orderby('id', 'desc')->limit(10)->get();
        //Dashboard count
        $Pickup_count = Pickup::count();
        $Agent_count = Agent::count();
        $customer_count = Customer::count();
        $document_count = CompanyProduct::count();
        $assigned_pickups = Pickup::where('status', '=', 'Published')->count();
        $accepted_pickups = Pickup::where('status', '=', 'Accepted')->count();
        $active_agent = Agent::where('status', '=', 'Approved')->count();
        $Inactive_agent = Agent::where('status', '!=', 'Approved')->count();

        // Pickup For Last 7 day
        $PickupPublish = array();
        $Pickupcompleted = array();
        $daterangepicker_start = date('Y-m-d', strtotime('-6 day'));
        $start_date = Carbon::parse($daterangepicker_start);
        $daterangepicker_end = Carbon::now();
        $dates = $this->generateDateRange($start_date, $daterangepicker_end);
        $pickups_dates = array();
        foreach($dates as $date){
            $pickup_date = Carbon::parse($date)->format('d/m');
            array_push($pickups_dates,$pickup_date);

        }
        $dateInterval = $dates;
        $stat = "pickup_date";
        foreach ($dateInterval as $date){
            $Pickup_publish_count = Pickup::where('created_at', 'like', '%' . $date . '%')->where('status','Published')->count();
            $pickup_completed_count = Pickup::where('completed_at', 'like', '%' . $date . '%')->where('status','Completed')->count();
            array_push($Pickupcompleted,$pickup_completed_count);
            array_push($PickupPublish,$Pickup_publish_count);
        }
        return view('backEnd.home',
            compact("GeneralWebmasterSections", "Pickups","pickups_dates",  "Agents","Pickupcompleted","PickupPublish","active_agent","Inactive_agent",
                 "DeclinePickups", "Pickup_count","Agent_count","document_count","customer_count", "assigned_pickups", "accepted_pickups"));
    }
    private function generateDateRange(Carbon $start_date, Carbon $end_date)
    {
        $dates = [];
        for($date = $start_date; $date->lte($end_date); $date->addDay()) {
            $dates[] = $date->format('Y-m-d');
        }
        return $dates;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function search()
    {
        // General for all pages
        $GeneralWebmasterSections = WebmasterSection::where('status', '=', '1')->orderby('row_no', 'asc')->get();
        // General END
        $search_word = "";
        $active_tab = 0;
        return view('backEnd.search', compact("GeneralWebmasterSections", "search_word", "active_tab"));
    }


    /**
     * Search resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function find(Request $request)
    {
        //
        // General for all pages
        $GeneralWebmasterSections = WebmasterSection::where('status', '=', '1')->orderby('row_no', 'asc')->get();
        // General END

        $active_tab = 0;
        if ($request->q != "") {
            if (@Auth::user()->permissionsGroup->view_status) {


                //find Webmails
                $Webmails = Webmail::where('created_by', '=', Auth::user()->id)->where('title', 'like',
                    '%' . $request->q . '%')
                    ->orwhere('from_name', 'like', '%' . $request->q . '%')
                    ->orwhere('from_email', 'like', '%' . $request->q . '%')
                    ->orwhere('from_phone', 'like', '%' . $request->q . '%')
                    ->orwhere('to_email', 'like', '%' . $request->q . '%')
                    ->orwhere('to_name', 'like', '%' . $request->q . '%')
                    ->orderby('id', 'desc')->get();

                //find Events
                $Events = Event::where('created_by', '=', Auth::user()->id)->where('title', 'like',
                    '%' . $request->q . '%')
                    ->orwhere('details', 'like', '%' . $request->q . '%')
                    ->orderby('start_date', 'desc')->get();


                //find Sections
                $Sections = Section::where('created_by', '=', Auth::user()->id)->where('title_ar', 'like',
                    '%' . $request->q . '%')
                    ->orwhere('title_en', 'like', '%' . $request->q . '%')
                    ->orwhere('seo_title_ar', 'like', '%' . $request->q . '%')
                    ->orwhere('seo_title_en', 'like', '%' . $request->q . '%')
                    ->orderby('id', 'desc')->get();
            } else {


                //find Webmails
                $Webmails = Webmail::where('title', 'like', '%' . $request->q . '%')
                    ->orwhere('from_name', 'like', '%' . $request->q . '%')
                    ->orwhere('from_email', 'like', '%' . $request->q . '%')
                    ->orwhere('from_phone', 'like', '%' . $request->q . '%')
                    ->orwhere('to_email', 'like', '%' . $request->q . '%')
                    ->orwhere('to_name', 'like', '%' . $request->q . '%')
                    ->orderby('id', 'desc')->get();

                //find Events
                $Events = Event::where('title', 'like', '%' . $request->q . '%')
                    ->orwhere('details', 'like', '%' . $request->q . '%')
                    ->orderby('start_date', 'desc')->get();



                //find Sections
                $Sections = Section::where('title_ar', 'like', '%' . $request->q . '%')
                    ->orwhere('title_en', 'like', '%' . $request->q . '%')
                    ->orwhere('seo_title_ar', 'like', '%' . $request->q . '%')
                    ->orwhere('seo_title_en', 'like', '%' . $request->q . '%')
                    ->orderby('id', 'desc')->get();

            }
            if (count($Webmails) > 0) {
                $active_tab = 5;
            }
            if (count($Events) > 0) {
                $active_tab = 4;
            }

            if (count($Sections) > 0) {
                $active_tab = 2;
            }


        } else {
            return redirect()->action('HomeController@search');
        }
        $search_word = $request->q;

        return view("backEnd.search",
            compact("GeneralWebmasterSections", "search_word", "Webmails", "Contacts", "Events",  "Sections",
                "active_tab"));
    }

    /*Notification*/

    public function viewNotification($id){

        $user = User::find(\Illuminate\Support\Facades\Auth::getUser()->id);
        $notification = $user->notifications()->where('id',str_replace('"','',$id))->first();
        $notification->markAsRead();

        return redirect()->route('pickups');
    }

}

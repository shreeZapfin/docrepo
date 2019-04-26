<?php

namespace App\Http\Controllers\Customer;

use App\AnalyticsPage;
use App\AnalyticsVisitor;

use App\Http\Controllers\Controller;
use App\Contact;
use App\Event;
use App\Http\Requests;
use App\Pickup;
use App\Section;
use App\Topic;
use App\Webmail;
use App\WebmasterSection;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HomeController extends Controller
{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        // General for all pages
        $GeneralWebmasterSections = WebmasterSection::where('status', '=', '1')->orderby('row_no', 'asc')->get();
        // General END

        if (@Auth::user()->permissionsGroup->view_status) {
            //List of all Webmails
            $Webmails = Webmail::where('created_by', '=', Auth::user()->id)->orderby('id', 'desc')
                ->where('cat_id', '=', 0)->limit(4)->get();





        } else {
            //List of all Webmails
            $Webmails = Webmail::orderby('id', 'desc')
                ->where('cat_id', '=', 0)->limit(4)->get();




        }



        // Last 7 Days
        $daterangepicker_start = date('Y-m-d', strtotime('-6 day'));
        $daterangepicker_end = date('Y-m-d');
        $stat = "date";


        // Today By Country
        $date_today = date('Y-m-d');
        $stat = "country";

        $TodayByCountry = array();


        $ix = 0;

        usort($TodayByCountry, function ($a, $b) {
            return $b['visits'] - $a['visits'];
        });

        // Today By Browser
        $date_today = date('Y-m-d');
        $stat = "browser";

        $TodayByBrowsers = array();
        usort($TodayByBrowsers, function ($a, $b) {
            return $b['visits'] - $a['visits'];
        });
        $TodayByBrowser1 = "";
        $TodayByBrowser1_val = 0;
        $TodayByBrowser2 = "Other Browsers";
        $TodayByBrowser2_val = 0;
        $ix = 0;
        $emptyB = 0;
        foreach ($TodayByBrowsers as $TodayByBrowser) {
            $emptyBi = 0;
            if ($emptyB == 0) {
                $emptyBi = $ix;
            }
            if ($ix == $emptyBi) {
                $ix2 = 0;
                foreach ($TodayByBrowser as $key => $val) {
                    if ($ix2 == 0) {
                        $TodayByBrowser1 = $val;
                        if ($TodayByBrowser1 != "") {
                            $emptyB = 1;
                        }
                    }
                    if ($ix2 == 1) {
                        $TodayByBrowser1_val = $val;
                    }
                    $ix2++;
                }
            } else {
                $ixx2 = 0;
                foreach ($TodayByBrowser as $key => $val) {
                    if ($ixx2 == 1) {
                        $TodayByBrowser2_val += $val;
                    }
                    $ixx2++;
                }
            }
            $ix++;
        }
        return view('customer.home',
            compact("GeneralWebmasterSections", "Webmails",  "Contacts",
                "TodayByCountry", "TodayByBrowser1", "TodayByBrowser1_val", "TodayByBrowser2",
                "TodayByBrowser2_val", "TodayVisitorsRate"));
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
        return view('customer.search', compact("GeneralWebmasterSections", "search_word", "active_tab"));
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



                //find Topics
                $Topics = Topic::where('created_by', '=', Auth::user()->id)->where('title_ar', 'like',
                    '%' . $request->q . '%')
                    ->orwhere('title_en', 'like', '%' . $request->q . '%')
                    ->orwhere('seo_title_ar', 'like', '%' . $request->q . '%')
                    ->orwhere('seo_title_en', 'like', '%' . $request->q . '%')
                    ->orderby('id', 'desc')->get();

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



                //find Topics
                $Topics = Topic::where('title_ar', 'like', '%' . $request->q . '%')
                    ->orwhere('title_en', 'like', '%' . $request->q . '%')
                    ->orwhere('seo_title_ar', 'like', '%' . $request->q . '%')
                    ->orwhere('seo_title_en', 'like', '%' . $request->q . '%')
                    ->orderby('id', 'desc')->get();

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


            if (count($Sections) > 0) {
                $active_tab = 2;
            }
            if (count($Topics) > 0) {
                $active_tab = 1;
            }

        } else {
            return redirect()->action('HomeController@search');
        }
        $search_word = $request->q;

        return view("backEnd.search",
            compact("GeneralWebmasterSections", "search_word", "Webmails", "Contacts", "Events", "Topics", "Sections",
                "active_tab"));
    }

}

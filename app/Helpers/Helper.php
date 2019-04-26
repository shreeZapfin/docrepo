<?php

// This class file to define all general functions

namespace App\Helpers;

use App\AnalyticsPage;
use App\AnalyticsVisitor;
use App\Banner;
use App\Country;
use App\Event;
use App\Menu;
use App\Section;
use App\Setting;
use App\Topic;
use App\Webmail;
use App\WebmasterSection;
use App\WebmasterSetting;
use Auth;

class Helper
{


    static function GeneralWebmasterSettings($var)
    {
        $WebmasterSetting = WebmasterSetting::find(1);
        return $WebmasterSetting->$var;
    }

    static function GeneralSiteSettings($var)
    {

        $Setting = Setting::find(1);
        return $Setting->$var;
    }

    // Get Events Alerts
    static function eventsAlerts()
    {
        if (@Auth::user()->permissionsGroup->view_status) {
            $Events = Event::where('created_by', '=', Auth::user()->id)->where('start_date', '>=',
                "'" . date('Y-m-d H:i:s') . "'")->orderby('start_date', 'asc')->limit(10)->get();
        } else {
            $Events = Event::where('start_date', '>=',
                "'" . date('Y-m-d H:i:s') . "'")->orderby('start_date', 'asc')->limit(10)->get();
        }
        return $Events;
    }

    // Get Webmails Alerts
    static function webmailsAlerts()
    {

        //List of all Webmails
        if (@Auth::user()->permissionsGroup->view_status) {
            $Webmails = Webmail::where('created_by', '=', Auth::user()->id)->orderby('id', 'desc')->where('status', '=',
                0)
                ->where('cat_id', '=', 0)->limit(4)->get();
        } else {
            $Webmails = Webmail::orderby('id', 'desc')->where('status', '=', 0)
                ->where('cat_id', '=', 0)->limit(4)->get();
        }

        return $Webmails;
    }

    // Get Webmails Alerts
    static function webmailsNewCount()
    {
        //List of all Webmails
        if (@Auth::user()->permissionsGroup->view_status) {
            $Webmails = Webmail::where('created_by', '=', Auth::user()->id)->orderby('id', 'desc')->where('status', '=',
                0)->where('cat_id', '=', 0)->get();
        } else {
            $Webmails = Webmail::orderby('id', 'desc')->where('status', '=', 0)->where('cat_id', '=', 0)->get();
        }
        return count($Webmails);
    }


    // Banners array List
    static function BannersList($BannersSettingsId)
    {
        return Banner::where('section_id', $BannersSettingsId)->where('status', 1)->orderby('row_no', 'asc')->get();
    }

    // Menu array List
    static function MenuList($GroupId)
    {
        return Menu::where('father_id', $GroupId)->where('status', 1)->orderby('row_no', 'asc')->get();
    }



    // Videos Check Functions

    static function Get_youtube_video_id($url)
    {
        if (preg_match('/youtu\.be/i', $url) || preg_match('/youtube\.com\/watch/i', $url)) {
            $pattern = '/^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#\&\?]*).*/';
            preg_match($pattern, $url, $matches);
            if (count($matches) && strlen($matches[7]) == 11) {
                return $matches[7];
            }
        }

        return '';
    }

    static function Get_vimeo_video_id($url)
    {
        if (preg_match('/vimeo\.com/i', $url)) {
            $pattern = '/\/\/(www\.)?vimeo.com\/(\d+)($|\/)/';
            preg_match($pattern, $url, $matches);
            if (count($matches)) {
                return $matches[2];
            }
        }

        return '';
    }


    // Social Share links
    static function SocialShare($social, $title)
    {
        $shareLink = "";
        $URL = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        switch ($social) {
            case "facebook":
                $shareLink = "https://www.facebook.com/sharer/sharer.php?u=" . urlencode($URL);
                break;
            case "twitter":
                $shareLink = "https://twitter.com/intent/tweet?text=$title&url=" . urlencode($URL);
                break;
            case "google":
                $shareLink = "https://plus.google.com/share?url=" . urlencode($URL);
                break;
            case "linkedin":
                $shareLink = "http://www.linkedin.com/shareArticle?mini=true&url=" . urlencode($URL) . "&title=$title";
                break;
            case "tumblr":
                $shareLink = "http://www.tumblr.com/share/link?url=" . urlencode($URL);
                break;
        }

        Return $shareLink;
    }


    static function GetIcon($path, $file)
    {
        $ext = strrchr($file, ".");
        $ext = strtolower($ext);
        $icon = "<i class=\"fa fa-file-o\"></i>";
        if ($ext == ".pdf") {
            $icon = "<i class=\"fa fa-file-pdf-o\" style='color: red;font-size: 20px'></i>";
        }
        if ($ext == '.png' or $ext == '.jpg' or $ext == '.jpeg' or $ext == '.gif') {
            $icon = "<img src='$path/$file' style='width: auto;height: 20px' title=''>";
        }
        if ($ext == ".xls" or $ext == '.xlsx') {
            $icon = "<i class=\"fa fa-file-excel-o\" style='color: green;font-size: 20px'></i>";
        }
        if ($ext == ".ppt" or $ext == '.pptx' or $ext == '.pptm') {
            $icon = "<i class=\"fa fa-file-powerpoint-o\" style='color: #1066E7;font-size: 20px'></i>";
        }
        if ($ext == ".doc" or $ext == '.docx') {
            $icon = "<i class=\"fa fa-file-word-o\" style='color: #0EA8DD;font-size: 20px'></i>";
        }
        if ($ext == ".zip" or $ext == '.rar') {
            $icon = "<i class=\"fa fa-file-zip-o\" style='color: #C8841D;font-size: 20px'></i>";
        }
        if ($ext == ".txt" or $ext == '.rtf') {
            $icon = "<i class=\"fa fa-file-text-o\" style='color: #7573AA;font-size: 20px'></i>";
        }
        if ($ext == ".mp3" or $ext == '.wav') {
            $icon = "<i class=\"fa fa-file-audio-o\" style='color: #8EA657;font-size: 20px'></i>";
        }
        if ($ext == ".mp4" or $ext == '.avi') {
            $icon = "<i class=\"fa fa-file-video-o\" style='color: #D30789;font-size: 20px'></i>";
        }
        return $icon;

    }

    static function URLSlug($url_ar, $url_en, $type = "", $id = 0)
    {
        $Check_SEO_st_ar = true;
        $Check_SEO_st_en = true;

        $seo_url_slug_ar = str_slug($url_ar, '-');
        $seo_url_slug_en = str_slug($url_en, '-');

        $ReservedURLs = array(
            "home",
            "about",
            "privacy",
            "terms",
            "contact",
            "search",
            "comment",
            "order",
            "sitemap"
        );


        if ($type == "section" && $id > 0) {
            // .. ..  Webmaster Sections
            $check_WebmasterSection = WebmasterSection::where([['seo_url_slug_ar', $seo_url_slug_ar], ['id', '!=', $id]])->orWhere([['seo_url_slug_en', $seo_url_slug_ar], ['id', '!=', $id]])->get();
            if (count($check_WebmasterSection) > 0) {
                $Check_SEO_st_ar = false;
            }
            $check_WebmasterSection = WebmasterSection::where([['seo_url_slug_ar', $seo_url_slug_en], ['id', '!=', $id]])->orWhere([['seo_url_slug_en', $seo_url_slug_en], ['id', '!=', $id]])->get();
            if (count($check_WebmasterSection) > 0) {
                $Check_SEO_st_en = false;
            }
        } else {
            // .. ..  Webmaster Sections
            $check_WebmasterSection = WebmasterSection::where('seo_url_slug_ar', $seo_url_slug_ar)->orWhere('seo_url_slug_en', $seo_url_slug_ar)->get();
            if (count($check_WebmasterSection) > 0) {
                $Check_SEO_st_ar = false;
            }
            $check_WebmasterSection = WebmasterSection::where('seo_url_slug_ar', $seo_url_slug_en)->orWhere('seo_url_slug_en', $seo_url_slug_en)->get();
            if (count($check_WebmasterSection) > 0) {
                $Check_SEO_st_en = false;
            }
        }

        if ($type == "category" && $id > 0) {
            // .. ..  Sections
            $check_Section = Section::where([['seo_url_slug_ar', $seo_url_slug_ar], ['id', '!=', $id]])->orWhere([['seo_url_slug_en', $seo_url_slug_ar], ['id', '!=', $id]])->get();
            if (count($check_Section) > 0) {
                $Check_SEO_st_ar = false;
            }
            $check_Section = Section::where([['seo_url_slug_ar', $seo_url_slug_en], ['id', '!=', $id]])->orWhere([['seo_url_slug_en', $seo_url_slug_en], ['id', '!=', $id]])->get();
            if (count($check_Section) > 0) {
                $Check_SEO_st_en = false;
            }
        } else {
            // .. ..  Sections
            $check_Section = Section::where('seo_url_slug_ar', $seo_url_slug_ar)->orWhere('seo_url_slug_en', $seo_url_slug_ar)->get();
            if (count($check_Section) > 0) {
                $Check_SEO_st_ar = false;
            }
            $check_Section = Section::where('seo_url_slug_ar', $seo_url_slug_en)->orWhere('seo_url_slug_en', $seo_url_slug_en)->get();
            if (count($check_Section) > 0) {
                $Check_SEO_st_en = false;
            }
        }

        if ($type == "topic" && $id > 0) {
            // .. ..  Topics
            $check_Topic = Topic::where([['seo_url_slug_ar', $seo_url_slug_ar], ['id', '!=', $id]])->orWhere([['seo_url_slug_en', $seo_url_slug_ar], ['id', '!=', $id]])->get();
            if (count($check_Topic) > 0) {
                $Check_SEO_st_ar = false;
            }
            $check_Topic = Topic::where([['seo_url_slug_ar', $seo_url_slug_en], ['id', '!=', $id]])->orWhere([['seo_url_slug_en', $seo_url_slug_en], ['id', '!=', $id]])->get();
            if (count($check_Topic) > 0) {
                $Check_SEO_st_en = false;
            }
        } else {
            // .. ..  Topics
            $check_Topic = Topic::where('seo_url_slug_ar', $seo_url_slug_ar)->orWhere('seo_url_slug_en', $seo_url_slug_ar)->get();
            if (count($check_Topic) > 0) {
                $Check_SEO_st_ar = false;
            }
            $check_Topic = Topic::where('seo_url_slug_ar', $seo_url_slug_en)->orWhere('seo_url_slug_en', $seo_url_slug_en)->get();
            if (count($check_Topic) > 0) {
                $Check_SEO_st_en = false;
            }
        }

        if (in_array($seo_url_slug_ar, $ReservedURLs)) {
            $Check_SEO_st_ar = false;
        }
        if (in_array($seo_url_slug_en, $ReservedURLs)) {
            $Check_SEO_st_en = false;
        }
        if ($seo_url_slug_ar == "") {
            $Check_SEO_st_ar = true;
        }
        if ($seo_url_slug_en == "") {
            $Check_SEO_st_en = true;
        }

        $ar_slug = "";
        if ($Check_SEO_st_ar) {
            $ar_slug = $seo_url_slug_ar;
        }
        $en_slug = "";
        if ($Check_SEO_st_en) {
            $en_slug = $seo_url_slug_en;
        }
        return array("slug_ar" => $ar_slug, "slug_en" => $en_slug);
    }

}


?>
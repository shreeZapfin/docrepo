<?php

namespace App\Http\Controllers;

use App\Agent;
use App\AgentReedem;
use App\AgentTransaction;
use App\Http\Requests;
use App\Menu;
use App\MobileNotification;
use App\WebmasterSection;
use Auth;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Redirect;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use LaravelFCM\Facades\FCM;

class RedeemController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');

        // Check Permissions
        if (@Auth::user()->permissions != 0 && Auth::user()->permissions != 1) {
            return Redirect::to(route('NoPermission'))->send();
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        // General for all pages
        $GeneralWebmasterSections = WebmasterSection::where('status', '=', '1')->orderby('row_no', 'asc')->get();
        // General END
        return view("backEnd.redeem.index", compact("Menus", "GeneralWebmasterSections", "ParentMenus", "EditedMenu"));
    }

    public function data(Request $request){
        $status_id  = $request->get('status_id');
        $query = AgentReedem::join('agents','agents.id','agent_redeem.agent_id');
        if($status_id !=null){
            $query->where('agent_redeem.status',$status_id);
        }
        $redeem_data = $query->get(array('agent_redeem.id','agent_redeem.amount','agent_redeem.wallet_amt','agent_redeem.status','agents.name'));
        return Datatables::of($redeem_data)
            ->addColumn('checkbox',function($redeem_data) {
                $checkbox= '<label class="ui-check m-a-0"><input type="checkbox" name="ids[]" class="checkBoxClass" value='.$redeem_data->id.'><i class="dark-white"></i>
                                <input type="hidden" name="row_ids[]" value='.$redeem_data->id.' /></label>';
                return $checkbox;
            })
            ->editColumn('amount',function($redeem_data) {
                $amount ='<i class="fa fa-inr fa-fw" aria-hidden="true"></i><span id="total">'.$redeem_data->amount;
                return $amount;
            })
            ->editColumn('wallet_amt',function($redeem_data) {
                $wallet_amt ='<i class="fa fa-inr fa-fw" aria-hidden="true"></i><span id="total">'.$redeem_data->wallet_amt;
                return $wallet_amt;
            })
            ->rawColumns(['actions', 'document', 'checkbox','amount','wallet_amt'])
            ->make(true);
    }

    public function updateStatus(Request $request){

        if($request->ids != "") {
            $status = $request->get('action');
            $redeem_ids = $request->ids;
                foreach ($redeem_ids as $redeem_id){
                    $Redeem = AgentReedem::where('id', $redeem_id)->first();
                  if($Redeem != null && $Redeem->status == 'Pending' ){
                      if($status == 'Paid'){
                          AgentReedem::where('id', $redeem_id)->update(['status' => 'Paid']);
                      }elseif ($status == 'Rejected'){
                          $AgentWallet = Agent::where('status', 'Approved')
                              ->where('id', $Redeem->agent_id)->first();
                          if ($AgentWallet != null) {
                              $RedeemAmount = $AgentWallet->wallet_amt + $Redeem->amount;

                              //Save Redeem Amount to Agent Wallet
                              $AgentWallet->wallet_amt = $RedeemAmount;
                              $AgentWallet->save();
                              //Save Status To Redeem
                              AgentReedem::where('id', $redeem_id)->update(['status' => $status]);

                              //Save Status to Transaction
                              $Transaction = new  AgentTransaction();
                              $Transaction->agent_id = $Redeem->agent_id;
                              $Transaction->redeem_id = $Redeem->id;
                              $Transaction->type = 'Redeem Rejected';
                              $Transaction->amount = $Redeem->amount;
                              $Transaction->save();
                          } else {
                              return redirect()->action('RedeemController@index')->with('doneMessage', 'Sorry !! Agent Status is Not Approved ');
                          }
                      }else{
                          return redirect()->action('RedeemController@index')->with('doneMessage', trans('backLang.saveDone'));
                      }

                      // Pickup Mobile Notification
                      $mobile_notification = new MobileNotification();
                      $mobile_notification->type = 'Redeem '.$status;
                      $mobile_notification->agent_id = $Redeem->agent_id;
                      $mobile_notification->save();

                      //Push Notification
                      $agent_id = AgentReedem::whereIn('id',$redeem_ids)
                          ->pluck('agent_id')->toArray();

                      $tokens = Agent::whereIn('id',$agent_id)
                          ->where('status','Approved')
                          ->whereNotNull('TokenID')
                          ->pluck('TokenID')->toArray();

                      $optionBuilder = new OptionsBuilder();
                      $optionBuilder->setTimeToLive(60*20);
                      $notificationBuilder = new PayloadNotificationBuilder('Notification');
                      $notificationBuilder->setBody(['type' => $status]);

                      //->setSound('default');
                      $dataBuilder = new PayloadDataBuilder();
                      $dataBuilder->addData(['type' => $status]);

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

                  }else{
                      return redirect()->action('RedeemController@index')->with('doneMessage', 'This Redeem Status Does Not Change Anymore!!');
                  }

                }

        }
        return redirect()->action('RedeemController@index')->with('doneMessage', trans('backLang.saveDone'));
    }
}
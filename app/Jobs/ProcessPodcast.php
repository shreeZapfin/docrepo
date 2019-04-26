<?php

namespace App\Jobs;

use App\Agent;
use App\Customer;
use App\Mail\Pickuppdf;
use App\Mail\Reminder;
use App\Pickup;
use App\PickupDocument;
use App\PickupDocumentPictures;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessPodcast implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $pickupId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($pickupId)
    {
        $this->pickupId = $pickupId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $submited_documents = PickupDocument::where('pickup_id',$this->pickupId)
            ->orderBy('sequence','asc')->get(array('id','question', 'sequence', 'comments'));
        if(count($submited_documents) > 0)
        {
            foreach($submited_documents as $submited_document)
            {
                $pictures = PickupDocumentPictures::where('pickup_document_id', $submited_document->id)->get(array('id','filename', 'latitude', 'longitude'));
                $submited_document->pictures = $pictures;
            }
        }
        $Pickups = Pickup::where('id',$this->pickupId)->first(array('agent_id','completed_at','pod_number',
                'pod_number','delivery_number','status','address','city','state','pincode','pickup_date','completed_at','pickup_person','id'));
        if($Pickups->agent_id != null) {
        $agents = Agent::where('id',$Pickups->agent_id)->first(array('name','email','mobile','status'));
        }
        $pdf = PDF::loadView('backEnd.pickups.pdf',compact('Pickups','agents','pickup_documents','submited_documents'));
        $pdf->save(public_path() . '/downloads/' . 'Pickup'.$this->pickupId . '.pdf');
        $pdf->setPaper('A4', 'landscape');
        //file_put_contents($file, $pdf->output());
      $file = public_path() . '/downloads/' . 'Pickup'.$this->pickupId . '.pdf';
       $pickup_file =  URL::to('/downloads/'. 'Pickup'.$this->pickupId . '.pdf');
       //$pickup_file = str_replace('/', '\\', $file);
//
            //mail to customer
            $CustomerId= Pickup::where('id',$this->pickupId)->first(array('customer_id'));
            $customer_mail = Customer::where('id',$CustomerId->customer_id)->first(array('id','email','name'));

            if ($customer_mail != null) {
               $data = new \stdClass();
                $data->name = $customer_mail->name;
                $data->url = URL::route('password.reset', [$customer_mail->id]);
                $data->pickup_name = 'Pickup'.$this->pickupId;
//                // Send the activation code through email
                Mail::to($customer_mail->email)
                    ->send(new Pickuppdf($pickup_file));
            }
//        $mail = "poojakakde65@gmail.com";
//        $data = new \stdClass();
//        $data->name = "pooja";
//        $data->forgotPasswordUrl = URL::route('forgot-password-confirm', [1, 1]);
//        // Send the activation code through email
//        Mail::to($mail)
//            ->send(new Reminder($data));

    }
}

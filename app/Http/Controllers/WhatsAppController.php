<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Log;
use Twilio\TwiML\MessagingResponse;


// use Netflie\WhatsAppCloudApi\Message\ButtonReply\Button;
// use Netflie\WhatsAppCloudApi\Message\ButtonReply\ButtonAction;
// use Netflie\WhatsAppCloudApi\Message\Contact\ContactName;
// use Netflie\WhatsAppCloudApi\Message\Contact\Phone;
// use Netflie\WhatsAppCloudApi\Message\Contact\PhoneType;
// use Netflie\WhatsAppCloudApi\Message\CtaUrl\TitleHeader;
// use Netflie\WhatsAppCloudApi\Message\Media\LinkID;
// use Netflie\WhatsAppCloudApi\Message\Media\MediaObjectID;
// use Netflie\WhatsAppCloudApi\Message\OptionsList\Action;
// use Netflie\WhatsAppCloudApi\Message\OptionsList\Row;
// use Netflie\WhatsAppCloudApi\Message\OptionsList\Section;
// use Netflie\WhatsAppCloudApi\Message\Template\Component;

use Netflie\WhatsAppCloudApi\WhatsAppCloudApi;
// use Netflie\WhatsAppCloudApi\Tests\Integration\Controller;

// require_once 'vendor/autoload.php';
const GOOD_BOY_URL = "https://images.unsplash.com/photo-1518717758536-85ae29035b6d?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=1350&q=80";

// Loads the library
require_once '../vendor/autoload.php';

use Netflie\WhatsAppCloudApi\WebHook;

class WhatsAppController extends Controller
{
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function index()
    {
        return view('whatsapp');
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function store(Request $request)
    {
        $twilioSid = env('TWILIO_SID');
        $twilioToken = env('TWILIO_AUTH_TOKEN');
        $twilioWhatsAppNumber = env('TWILIO_WHATSAPP_NUMBER');
        $recipientNumber = 'whatsapp:' . $request->phone;
        $message = $request->message;

        $twilio = new Client($twilioSid, $twilioToken);

        try {
            $twilio->messages->create(
                $recipientNumber,
                [
                    "from" => 'whatsapp:' . $twilioWhatsAppNumber,
                    "body" => $message,
                ]
            );

            return back()->with('success', 'whatsapp message sent success');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function webhook(Request $request)
    {
        $numMedia = (int) $request->input("NumMedia");

        Log::debug("Media files received: {$numMedia}");

        $response = new MessagingResponse();
        if ($numMedia === 0) {
            $message = $response->message("Send us an image!");
        } else {
            $message = $response->message("Thanks for the image! Here's one for you!");
            $message->media(GOOD_BOY_URL);
        }

        return $response;
    }
    public function response(Request $request)
    {
        // require_once '../vendor/autoload.php';
        // $response = new MessagingResponse();
        // $response->redirect('http://www.example.com/nextInstructions');
        // echo $response;

        // Loads the library
        // $response = new MessagingResponse();
        // $response->message("The Robots are coming! Head for the hills!");
        // return $response;

        // $response = new MessagingResponse;
        // $message = $response->message("");
        // $message->body("The Robots are coming! Head for the hills!");
        // $message->media("https://farm8.staticflickr.com/7090/6941316406_80b4d6d50e_z_d.jpg");
        // return $response;


        // $response = new MessagingResponse();
        // $message = $response->message('');
        // $message->body('Hello World!');
        // $response->redirect('https://demo.twilio.com/welcome/sms/');

        // echo $response;


        // $response = new MessagingResponse();
        // $body = $_REQUEST['Body'];
        // $default = "I just wanna tell you how I'm feeling - Gotta make you understand";
        // $options = [
        //     "give you up",
        //     "let you down",
        //     "run around and desert you",
        //     "make you cry",
        //     "say goodbye",
        //     "tell a lie, and hurt you"
        // ];

        // if (strtolower($body) == 'never gonna') {
        //     $response->message($options[array_rand($options)]);
        // } else {
        //     $response->message($default);
        // }
        // print $response;


        require_once('../vendor/autoload.php'); // Loads the library

        // $body = $_REQUEST['Body'];

        $response = new MessagingResponse;
        $message = $response->message("");
        $message->body("The Robots are coming! Head for the hills!");
        $message->media("https://farm8.staticflickr.com/7090/6941316406_80b4d6d50e_z_d.jpg");
        print $response;
    }

    // public function handleIncomingMessage(Request $request)
    // {
    //     $from = $request->input('From');
    //     $body = $request->input('Body');

    //     // Process the incoming message (e.g., store in database, send a reply, etc.)

    //     // Example: Log the message
    //     \Log::info("Message from $from: $body");

    //     // Example: Send a reply
    //     $twilio_number = 'your_twilio_number'; // Your Twilio WhatsApp number
    //     $twilio = new Client(config('services.twilio.sid'), config('services.twilio.token'));
    //     $twilio->messages->create("whatsapp:$from", [
    //         "from" => "whatsapp:$twilio_number",
    //         "body" => "Hello! You sent: $body"
    //     ]);

    //     return response('Message received', 200);
    // }



    public function handleIncomingMessage(Request $request)
    {
        // Ensure the 'Body' key exists in the request
        if (!$request->has('Body')) {
            \Log::error("Missing 'Body' parameter in incoming Twilio request: " . json_encode($request->all()));
            return response('Missing parameter', 400);
        }

        // Retrieve the sender's WhatsApp number and the message body
        $from = $request->input('From');
        $body = $request->input('Body');

        // Log the incoming message
        \Log::info("Received WhatsApp message from $from: $body");

        // Process the message further as needed
        // Example: Sending a reply
        $twilio = new Client(config('services.twilio.sid'), config('services.twilio.token'));
        $twilio->messages->create("whatsapp:$from", [
            "from" => "whatsapp:" . config('services.twilio.whatsapp_number'),
            "body" => "Hello! You sent: $body"
        ]);

        return response('Message received', 200);
    }


    // public function handleIncomingMessage(Request $request)
    // {
    //     // Validate request (optional, but recommended)
    //     $validated = $request->validate([
    //         'From' => 'required',
    //         'Body' => 'required',
    //     ]);

    //     // Retrieve the sender's WhatsApp number and the message body
    //     $from = $request->input('From');
    //     $body = $request->input('Body');

    //     // Log the incoming message (optional)
    //     \Log::info("Message from $from: $body");

    //     // You can perform further processing here, such as saving the message to a database,
    //     // triggering actions based on the message content, etc.

    //     // Example: Sending a reply (optional)
    //     $twilio = new Client(config('services.twilio.sid'), config('services.twilio.token'));
    //     $twilio->messages->create("whatsapp:$from", [
    //         "from" => "whatsapp:" . config('services.twilio.whatsapp_number'),
    //         "body" => "Hello! You sent: $body"
    //     ]);

    //     return response('Message received', 200);
    // }

    public function cloudapi_send_message(Request $request)
    {

        // Require the Composer autoloader.
        require '../vendor/autoload.php';

        // Instantiate the WhatsAppCloudApi super class.
        $whatsapp_cloud_api = new WhatsAppCloudApi([
            'from_phone_number_id' => '362928926898740',
            'access_token' => 'EAAGPrf5RO84BO8fKI5TKpxF5YUJPU8bvrbPXHU9GQc9cIN9J9dy5UQrXUgZACZCF1mnm8N1C6wjzkpAVFMUnMS83jHVXfpP0hEahPpEKaO5ScrJ4SS55zGzj2aiJHA2ZCy20Wt6LPt9gPsFjZBOQ3xWNldQuuB9kN1X7juy59KZBShRoVbX1yoGZCJK6t7b73in3322YtwBOyqwUbDWln9Vv1hmXUZD',
        ]);

        $message = $request->message;
        $phone = $request->phone;
        // $whatsapp_cloud_api->sendTextMessage($message, $phone);
        $whatsapp_cloud_api->sendTextMessage($phone, $message);
    }

    // public function sendmessage()
    // {
    //     $phone_number = "+918220037436"; // The phone number of the recipient in international format
    //     $message_text = "Hello, this is a test message from the WhatsApp Business API!"; // The message text

    //     $api_endpoint = 'https://graph.facebook.com/v18.0/<WHATSAPP_BUSINESS_ACCOUNT_ID>/messages';
    //     // for more details https://developers.facebook.com/docs/whatsapp/business-management-api/get-started/

    //     $api_token = "YOUR_API_TOKEN"; // Replace with your own API token
    //     $data = [
    //         'phone' => $phone_number,
    //         'body' => $message_text
    //     ];
    //     $ch = curl_init($api_endpoint);
    //     curl_setopt($ch, CURLOPT_POST, 1);
    //     curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    //     curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json', "Authorization: Bearer $api_token"));
    //     $result = curl_exec($ch);
    //     curl_close($ch);
    // }

    public function message()
    {
        $whatsapp_cloud_api = new WhatsAppCloudApi([
            'from_phone_number_id' => '+15556280861',
            'access_token' => 'EAAGPrf5RO84BO8fKI5TKpxF5YUJPU8bvrbPXHU9GQc9cIN9J9dy5UQrXUgZACZCF1mnm8N1C6wjzkpAVFMUnMS83jHVXfpP0hEahPpEKaO5ScrJ4SS55zGzj2aiJHA2ZCy20Wt6LPt9gPsFjZBOQ3xWNldQuuB9kN1X7juy59KZBShRoVbX1yoGZCJK6t7b73in3322YtwBOyqwUbDWln9Vv1hmXUZD',
        ]);

        $whatsapp_cloud_api->sendTextMessage('+918220037436', 'Hey there! I\'m using WhatsApp Cloud API. Visit https://www.netflie.es');
        $component_header = [];

        $component_body = [
            [
                'type' => 'text',
                'text' => '*Mr Jones*',
            ],
        ];

        $component_buttons = [
            [
                'type' => 'button',
                'sub_type' => 'quick_reply',
                'index' => 0,
                'parameters' => [
                    [
                        'type' => 'text',
                        'text' => 'Yes',
                    ]
                ]
            ],
            [
                'type' => 'button',
                'sub_type' => 'quick_reply',
                'index' => 1,
                'parameters' => [
                    [
                        'type' => 'text',
                        'text' => 'No',
                    ]
                ]
            ]
        ];

        $components = new Component($component_header, $component_body, $component_buttons);
        $whatsapp_cloud_api->sendTemplate('', 'sample_issue_resolution', 'en_US', $components);
    }

    public function webhookverificaion()
    {
        // Instantiate the WhatsAppCloudApi super class.
        $webhook = new WebHook();
        echo $webhook->verify($_GET, "<the-verify-token-defined-in-your-app-dashboard>");
    }

    public function Webhook_notifications()
    {
        define('STDOUT', fopen('php://stdout', 'w'));

        $payload = file_get_contents('php://input');
        fwrite(STDOUT, print_r($payload, true) . "\n");

        // Instantiate the Webhook super class.
        $webhook = new WebHook();

        // Read the first message
        fwrite(STDOUT, print_r($webhook->read(json_decode($payload, true)), true) . "\n");

        //Read all messages in case Meta decided to batch them
        fwrite(STDOUT, print_r($webhook->readAll(json_decode($payload, true)), true) . "\n");
    }
}

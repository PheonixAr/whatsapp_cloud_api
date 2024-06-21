<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Twilio\Rest\Client;
use Twilio\TwiML\MessagingResponse;
use App\Http\Controllers\MovieTicketPaid;

use App\Http\Controllers\WhatsAppController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [WhatsAppController::class, 'index']);
Route::post('whatsapp', [WhatsAppController::class, 'store']);
Route::get('/webhook', [WhatsAppController::class, 'webhook']);
Route::get('response', [WhatsAppController::class, 'response']);
Route::post('/cloudapi_send_message', [WhatsAppController::class, 'cloudapi_send_message'])->name('whatsapp.post');
Route::get('sendmessage', [WhatsAppController::class, 'sendmessage']);
Route::get('toWhatsapp', [MovieTicketPaid::class, 'toWhatsapp']);
Route::get('message', [WhatsAppController::class, 'message']);
Route::get('webhookverificaion', [WhatsAppController::class, 'webhookverificaion']);

Route::get('Webhook_notifications', [WhatsAppController::class, 'Webhook_notifications']);



// routes/web.php

Route::post('/whatsapp/receive', function (Request $request) {
    $body = $request->input('Body'); // Message body
    $from = $request->input('From'); // Sender's WhatsApp number

    $twilio_number = "your_twilio_whatsapp_number";

    // Initialize Twilio client
    $twilio = new Client(env('TWILIO_ACCOUNT_SID'), env('TWILIO_AUTH_TOKEN'));

    // Example response with media
    $response = $twilio->messages
        ->create(
            "whatsapp:" . $from, // to
            [
                "from" => "whatsapp:" . $twilio_number,
                "body" => "Here is your media message.",
                "mediaUrl" => ["https://example.com/media.jpg"] // Replace with actual media URL
            ]
        );

    return 'Message sent!';
});



Route::post('/handleIncomingMessage', [WhatsAppController::class, 'handleIncomingMessage']);

Route::post('/webhook/twilio', function (Request $request) {
    $body = $request->input('Body'); // Message body
    $from = $request->input('From'); // Sender's phone number

    // Handle the incoming message
    // You can process the message here, e.g., log it, store it in a database, or trigger further actions

    // Prepare TwiML response (optional)
    $response = new MessagingResponse();
    $response->message('Received your message!'); // Example response

    // Return TwiML response
    return $response;
});

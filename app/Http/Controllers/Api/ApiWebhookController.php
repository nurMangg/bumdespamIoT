<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

class ApiWebhookController extends Controller
{
    // public function webhook(Request $request)
    // {
    //     try {
    //         // Verify webhook secret
    //         $expectedSecret = 'mejiku';
    //         $receivedSecret = $request->header('X-Webhook-Secret') ?? $request->input('secret');
            
    //         if ($receivedSecret !== $expectedSecret) {
    //             Log::warning('Invalid webhook secret received', [
    //                 'ip' => $request->ip(),
    //                 'received_secret' => $receivedSecret
    //             ]);
    //             return response()->json(['error' => 'Unauthorized'], 401);
    //         }

    //         $data = $request->all();
            
    //         Log::info('WhatsApp webhook received', [
    //             'event_type' => $data['event_type'] ?? 'unknown',
    //             'message_type' => $data['message_type'] ?? 'unknown',
    //             'sender_phone' => $data['sender']['phone_number'] ?? $data['from'] ?? 'unknown',
    //             'sender_name' => $data['sender']['contact_name'] ?? $data['sender']['profile_name'] ?? 'Unknown'
    //         ]);

    //         // Skip test messages
    //         if (isset($data['test']) && $data['test'] === true) {
    //             Log::info('Test webhook received successfully');
    //             return response()->json([
    //                 'status' => 'success',
    //                 'message' => 'Test webhook received successfully'
    //             ]);
    //         }

    //         // Extract sender information
    //         $senderInfo = $this->extractSenderInfo($data);
            
    //         // Extract message information
    //         $messageInfo = $this->extractMessageInfo($data);
            
    //         // Extract chat information
    //         $chatInfo = $this->extractChatInfo($data);

    //         // Process different message types
    //         switch ($data['message_type']) {
    //             case 'text':
    //                 $this->handleTextMessage($data, $senderInfo, $messageInfo, $chatInfo);
    //                 break;
                    
    //             case 'image':
    //                 $this->handleImageMessage($data, $senderInfo, $messageInfo, $chatInfo);
    //                 break;
                    
    //             case 'document':
    //                 if (isset($data['mimetype']) && str_contains($data['mimetype'], 'pdf')) {
    //                     $this->handlePdfMessage($data, $senderInfo, $messageInfo, $chatInfo);
    //                 } else {
    //                     Log::info('Non-PDF document ignored', [
    //                         'mimetype' => $data['mimetype'] ?? 'unknown'
    //                     ]);
    //                 }
    //                 break;
                    
    //             default:
    //                 Log::info('Unsupported message type received', [
    //                     'message_type' => $data['message_type']
    //                 ]);
    //         }

    //         return response()->json([
    //             'status' => 'success',
    //             'message' => 'Webhook processed successfully',
    //             'timestamp' => now()->toISOString(),
    //             'processed_data' => [
    //                 'sender' => $senderInfo,
    //                 'message' => $messageInfo,
    //                 'chat' => $chatInfo
    //             ]
    //         ]);

    //     } catch (\Exception $e) {
    //         Log::error('Error processing WhatsApp webhook', [
    //             'error' => $e->getMessage(),
    //             'trace' => $e->getTraceAsString(),
    //             'data' => $request->all()
    //         ]);

    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Failed to process webhook'
    //         ], 500);
    //     }
    // }

    // private function extractSenderInfo($data)
    // {
    //     $sender = $data['sender'] ?? [];
        
    //     return [
    //         'jid' => $sender['jid'] ?? null,
    //         'phone_number' => $sender['phone_number'] ?? $data['from'] ?? null,
    //         'formatted_number' => $sender['formatted_number'] ?? null,
    //         'contact_name' => $sender['contact_name'] ?? null,
    //         'profile_name' => $sender['profile_name'] ?? $data['message_info']['push_name'] ?? null,
    //         'profile_picture' => $sender['profile_picture'] ?? null,
    //         'is_business' => $sender['is_business'] ?? false,
    //         'business_info' => $sender['business_info'] ?? null,
    //         'is_contact' => $sender['is_contact'] ?? false,
    //         'status' => $sender['status'] ?? null,
    //         'last_seen' => $sender['last_seen'] ?? null,
    //     ];
    // }

    // private function extractMessageInfo($data)
    // {
    //     $messageInfo = $data['message_info'] ?? [];
        
    //     return [
    //         'id' => $messageInfo['id'] ?? $data['message_id'] ?? null,
    //         'timestamp' => $messageInfo['timestamp'] ?? $data['timestamp'] ?? null,
    //         'push_name' => $messageInfo['push_name'] ?? null,
    //         'from_me' => $messageInfo['from_me'] ?? false,
    //         'broadcast' => $messageInfo['broadcast'] ?? false,
    //         'type' => $data['message_type'] ?? 'unknown',
    //         'content' => $data['content'] ?? null,
    //     ];
    // }

    // private function extractChatInfo($data)
    // {
    //     $chat = $data['chat'] ?? [];
        
    //     return [
    //         'jid' => $chat['jid'] ?? null,
    //         'type' => $chat['type'] ?? $data['chat_type'] ?? 'individual',
    //         'is_group' => $chat['is_group'] ?? false,
    //         'group_info' => $chat['group_info'] ?? null,
    //     ];
    // }

    // private function handleTextMessage($data, $senderInfo, $messageInfo, $chatInfo)
    // {
    //     $text = $data['text'] ?? $data['content']['text'] ?? '';
        
    //     Log::info('Text message received', [
    //         'sender' => $senderInfo,
    //         'message' => $messageInfo,
    //         'chat' => $chatInfo,
    //         'text' => $text
    //     ]);
        
    //     // Enhanced auto-reply logic based on sender info
    //     $this->processAutoReply($text, $senderInfo, $chatInfo);
        
    //     // Save message to database (example)
    //     // $this->saveMessageToDatabase($senderInfo, $messageInfo, $chatInfo, ['text' => $text]);
    // }

    // private function handleImageMessage($data, $senderInfo, $messageInfo, $chatInfo)
    // {
    //     $caption = $data['caption'] ?? $data['content']['caption'] ?? '';
        
    //     Log::info('Image message received', [
    //         'sender' => $senderInfo,
    //         'message' => $messageInfo,
    //         'chat' => $chatInfo,
    //         'caption' => $caption,
    //         'media_info' => $data['media'] ?? null
    //     ]);

    //     if (isset($data['media']['buffer'])) {
    //         try {
    //             // Save image to storage
    //             $imageData = base64_decode($data['media']['buffer']);
    //             $filename = 'whatsapp_images/' . ($data['media']['filename'] ?? 'image_' . time() . '.jpg');
                
    //             Storage::disk('public')->put($filename, $imageData);
                
    //             Log::info('Image saved to storage', [
    //                 'filename' => $filename,
    //                 'size' => $data['media']['size'] ?? 0,
    //                 'sender' => $senderInfo['phone_number']
    //             ]);

    //             // Send personalized confirmation reply
    //             $senderName = $senderInfo['contact_name'] ?? $senderInfo['profile_name'] ?? 'Pengirim';
    //             $this->sendAutoReply(
    //                 $senderInfo['phone_number'], 
    //                 "Halo {$senderName}! Gambar Anda berhasil diterima dan disimpan. Terima kasih!"
    //             );
                
    //         } catch (\Exception $e) {
    //             Log::error('Failed to save image', [
    //                 'error' => $e->getMessage(),
    //                 'sender' => $senderInfo
    //             ]);
    //         }
    //     }
    // }

    // private function handlePdfMessage($data, $senderInfo, $messageInfo, $chatInfo)
    // {
    //     $filename = $data['filename'] ?? $data['content']['filename'] ?? 'unknown.pdf';
        
    //     Log::info('PDF document received', [
    //         'sender' => $senderInfo,
    //         'message' => $messageInfo,
    //         'chat' => $chatInfo,
    //         'filename' => $filename,
    //         'media_info' => $data['media'] ?? null
    //     ]);

    //     if (isset($data['media']['buffer'])) {
    //         try {
    //             // Save PDF to storage
    //             $pdfData = base64_decode($data['media']['buffer']);
    //             $storagePath = 'whatsapp_documents/' . ($data['media']['filename'] ?? 'document_' . time() . '.pdf');
                
    //             Storage::disk('public')->put($storagePath, $pdfData);
                
    //             Log::info('PDF saved to storage', [
    //                 'filename' => $storagePath,
    //                 'size' => $data['media']['size'] ?? 0,
    //                 'sender' => $senderInfo['phone_number']
    //             ]);

    //             // Send personalized confirmation reply
    //             $senderName = $senderInfo['contact_name'] ?? $senderInfo['profile_name'] ?? 'Pengirim';
    //             $this->sendAutoReply(
    //                 $senderInfo['phone_number'], 
    //                 "Halo {$senderName}! Dokumen PDF '{$filename}' berhasil diterima dan disimpan. Terima kasih!"
    //             );
                
    //         } catch (\Exception $e) {
    //             Log::error('Failed to save PDF', [
    //                 'error' => $e->getMessage(),
    //                 'sender' => $senderInfo
    //             ]);
    //         }
    //     }
    // }

    // private function processAutoReply($text, $senderInfo, $chatInfo)
    // {
    //     $text = strtolower($text);
    //     $senderName = $senderInfo['contact_name'] ?? $senderInfo['profile_name'] ?? 'Pengirim';
    //     $phoneNumber = $senderInfo['phone_number'];
        
    //     // Skip auto-reply for group messages (optional)
    //     if ($chatInfo['is_group']) {
    //         Log::info('Skipping auto-reply for group message');
    //         return;
    //     }
        
    //     // Personalized auto-replies based on sender info
    //     if (str_contains($text, 'halo') || str_contains($text, 'hello')) {
    //         $message = "Halo {$senderName}! Terima kasih telah menghubungi kami. Ada yang bisa kami bantu?";
            
    //         // Add business hours info if sender is a business contact
    //         if ($senderInfo['is_business']) {
    //             $message .= "\n\nKami melayani Anda 24/7 untuk kebutuhan bisnis.";
    //         }
            
    //         $this->sendAutoReply($phoneNumber, $message);
            
    //     } elseif (str_contains($text, 'info') || str_contains($text, 'informasi')) {
    //         $message = "Halo {$senderName}! Untuk informasi lebih lanjut, silakan hubungi customer service kami.";
            
    //         // Add contact info if available
    //         if ($senderInfo['is_contact']) {
    //             $message .= "\n\nKarena Anda sudah terdaftar di kontak kami, Anda akan mendapat prioritas layanan.";
    //         }
            
    //         $this->sendAutoReply($phoneNumber, $message);
            
    //     } elseif (str_contains($text, 'terima kasih') || str_contains($text, 'thanks')) {
    //         $message = "Sama-sama {$senderName}! Senang bisa membantu Anda.";
    //         $this->sendAutoReply($phoneNumber, $message);
            
    //     } elseif (str_contains($text, 'status') || str_contains($text, 'order')) {
    //         $message = "Halo {$senderName}! Untuk mengecek status pesanan, silakan berikan nomor order Anda.";
    //         $this->sendAutoReply($phoneNumber, $message);
    //     }
    // }

    // private function sendAutoReply($to, $message)
    // {
    //     try {
    //         $apiUrl = config('services.whatsapp.api_url', 'http://localhost:3000');
            
    //         $response = Http::timeout(10)->post($apiUrl . '/api/send-message', [
    //             'number' => $to,
    //             'message' => $message
    //         ]);

    //         if ($response->successful()) {
    //             Log::info('Auto-reply sent successfully', [
    //                 'to' => $to,
    //                 'message' => $message
    //             ]);
    //         } else {
    //             Log::warning('Auto-reply failed', [
    //                 'to' => $to,
    //                 'status' => $response->status(),
    //                 'response' => $response->body()
    //             ]);
    //         }
    //     } catch (\Exception $e) {
    //         Log::error('Failed to send auto-reply', [
    //             'error' => $e->getMessage(),
    //             'to' => $to
    //         ]);
    //     }
    // }

    // // Example method to save message to database
    // private function saveMessageToDatabase($senderInfo, $messageInfo, $chatInfo, $content)
    // {
    //     try {
    //         // Example database save logic
    //         /*
    //         WhatsAppMessage::create([
    //             'message_id' => $messageInfo['id'],
    //             'sender_phone' => $senderInfo['phone_number'],
    //             'sender_name' => $senderInfo['contact_name'] ?? $senderInfo['profile_name'],
    //             'sender_profile_picture' => $senderInfo['profile_picture'],
    //             'is_business' => $senderInfo['is_business'],
    //             'chat_type' => $chatInfo['type'],
    //             'message_type' => $messageInfo['type'],
    //             'content' => json_encode($content),
    //             'timestamp' => $messageInfo['timestamp'],
    //             'processed_at' => now(),
    //         ]);
    //         */
            
    //         Log::info('Message would be saved to database', [
    //             'sender' => $senderInfo,
    //             'message' => $messageInfo,
    //             'content' => $content
    //         ]);
    //     } catch (\Exception $e) {
    //         Log::error('Failed to save message to database', [
    //             'error' => $e->getMessage(),
    //             'sender' => $senderInfo
    //         ]);
    //     }
    // }
}

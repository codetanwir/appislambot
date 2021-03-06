<?php

    require __DIR__ . '/vendor/autoload.php';
     
    use \LINE\LINEBot;
    use \LINE\LINEBot\HTTPClient\CurlHTTPClient;
    use \LINE\LINEBot\MessageBuilder\MultiMessageBuilder;
    use \LINE\LINEBot\MessageBuilder\TextMessageBuilder;
    use \LINE\LINEBot\MessageBuilder\StickerMessageBuilder;
    use \LINE\LINEBot\SignatureValidator as SignatureValidator;
    
     
    // set false for production
    $pass_signature = true;
     
    // set LINE channel_access_token and channel_secret
    $channel_access_token = "ciBDIiZDO1i6c3q/CDo4a8KBiubrvlqFelYUSSNEBbJfEN/mfrwmfSqsjhYRPg8fXoSTsRDk9BH0ET0tSGgJ19DjGRKgwyyd665DcAOp9zwu935bBJKvAKOQbGt9QW5fyeNerZBKHBfUTQVP+k/GtAdB04t89/1O/w1cDnyilFU=";
    $channel_secret = "8ee5700d543ba7695d0f3ff2934aebef";
     
     
    // inisiasi objek bot
    $httpClient = new CurlHTTPClient($channel_access_token);
    $bot = new LINEBot($httpClient, ['channelSecret' => $channel_secret]);
     
    $configs =  ['settings' => ['displayErrorDetails' => true],];
    
    $app = new Slim\App($configs);
     
    // buat route untuk url homepage
    $app->get('/', function($req, $res)
    {
      echo "Welcome at Slim Framework";
    });
     
    // buat route untuk webhook
    $app->post('/webhook', function ($request, $response) use ($bot, $httpClient)
    {
        // get request body and line signature header
        $body      = file_get_contents('php://input');
        $signature = isset($_SERVER['HTTP_X_LINE_SIGNATURE']) ? $_SERVER['HTTP_X_LINE_SIGNATURE'] : '';
     
        // log body and signature
        file_put_contents('php://stderr', 'Body: '.$body);
     
        // if($pass_signature === false)
        // {
        //     // is LINE_SIGNATURE exists in request header?
        //     if(empty($signature)){
        //         return $response->withStatus(400, 'Signature not set');
        //     }
     
        //     // is this request comes from LINE?
        //     if(! SignatureValidator::validateSignature($body, $channel_secret, $signature)){
        //         return $response->withStatus(400, 'Invalid signature');
        //     }
        // }
    
        // kode aplikasi nanti disini
        $data = json_decode($body, true);
        if(is_array($data['events'])){
            foreach ($data['events'] as $event)
            {
                if ($event['type'] == 'message')
                {
                    // if($event['message']['type'] == 'text')
                    // {
                        // $result = $bot->replyText($event['replyToken'], 'ini pesan balasan');
                        
                        // send same message as reply to user
                        // $result = $bot->replyText($event['replyToken'], $event['message']['text']);
         
                        // or we can use replyMessage() instead to send reply message
                        // $textMessageBuilder = new TextMessageBuilder('Hallo juga');
                        // $imageMessageBuilder = new ImageMessageBuilder('https://avatars2.githubusercontent.com/u/8528725?s=460&v=4', 'url gambar preview');
                        
                        // $textMessageBuilder1 = new TextMessageBuilder('ini pesan balasan pertama');
                        // $textMessageBuilder2 = new TextMessageBuilder('ini pesan balasan kedua');
                        // $stickerMessageBuilder = new StickerMessageBuilder(1, 106);
                         
                        // $multiMessageBuilder = new MultiMessageBuilder();
                        // $multiMessageBuilder->add($textMessageBuilder1);
                        // $multiMessageBuilder->add($textMessageBuilder2);
                        // $multiMessageBuilder->add($stickerMessageBuilder);
                        
                        
                        //  if(
                        //     $event['message']['type'] == 'image' or
                        //     $event['message']['type'] == 'video' or
                        //     $event['message']['type'] == 'audio' or
                        //     $event['message']['type'] == 'file'
                        //     ){
                        //     $basePath  = $request->getUri()->getBaseUrl();
                        //     $contentURL  = $basePath."/content/".$event['message']['id'];
                        //     $contentType = ucfirst($event['message']['type']);
                        //     $result = $bot->replyText($event['replyToken'],
                        //     $contentType. " yang Anda kirim bisa diakses dari link:\n " . $contentURL);
                            
                                
                        //       return $response->withJson($result->getJSONDecodedBody(), $result->getHTTPStatus()); 
                        //     }
                      
                        
                        // $result = $bot->replyMessage($event['replyToken'], $multiMessageBuilder);
         
                       
                       
                       
                       
                if ($event['source']['type'] == 'group' or
                    $event['source']['type'] == 'room'
                ) {
 
                // message from user
                } else {
                    if ($event['message']['type'] == 'text') {
                        if (strtolower($event['message']['text']) == 'user id') {
 
                            $result = $bot->replyText($event['replyToken'], $event['source']['userId']);
 
                        } elseif (strtolower($event['message']['text']) == 'flex') {
 
                            $flexTemplate = file_get_contents("flex_message.json"); // template flex message
                            $result = $httpClient->post(LINEBot::DEFAULT_ENDPOINT_BASE . '/v2/bot/message/reply', [
                                'replyToken' => $event['replyToken'],
                                'messages'   => [
                                    [
                                        'type'     => 'flex',
                                        'altText'  => 'Test Flex Message',
                                        'contents' => json_decode($flexTemplate)
                                    ]
                                ],
                            ]);
 
                        } else {
                            // send same message as reply to user
                            $result = $bot->replyText($event['replyToken'], $event['message']['text']);
                        }
 
                        return $response->withJson($result->getJSONDecodedBody(), $result->getHTTPStatus());
                    }
                       
                }  
                       
                       
                       
                       
                       
                     
                       
                    }
                }
        }
     
    });
    
    
    $app->get('/pushmessage', function($req, $res) use ($bot)
    {
        // send push message to user
        $userId = 'U3bf29c14b2605b75c39e0728375756b9';
        $textMessageBuilder = new TextMessageBuilder('Halo, ini pesan push');
        $result = $bot->pushMessage($userId, $textMessageBuilder);
       
        return $res->withJson($result->getJSONDecodedBody(), $result->getHTTPStatus());
    });
    
    $app->get('/content/{messageId}', function($req, $res) use ($bot)
    {
        // get message content
        $route      = $req->getAttribute('route');
        $messageId = $route->getArgument('messageId');
        $result = $bot->getMessageContent($messageId);
     
        // set response
        $res->write($result->getRawBody());
     
        return $res->withHeader('Content-Type', $result->getHeader('Content-Type'));
    });
     
$app->run();
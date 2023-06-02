<?php

namespace App\Http\Controllers;

use App\Models\ConfigTelegram;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use PHPUnit\Exception;
use function Symfony\Component\String\u;

class WebhookTelegram extends Controller
{
    public function configTelegram(Request $request)
    {
        return view('welcome');
    }

    public function getWebhook(Request $request)
    {
        try {
            $mes = $request->all();

            $configTele = ConfigTelegram::orderBy('id', 'DESC')->first();
            $listTopic = json_decode($configTele['topic']);

            if (!empty($mes['message']) && !empty($mes['message']['chat']['id'])) {
                if ($mes['message']['chat']['id'] == $configTele['chat_id_a'] && !empty($mes['message']['text'])) {
                    foreach ($listTopic as $topic) {
                        $this->sendMesToTopic($configTele['token_b'], $configTele['chat_id_b'], $mes['message']['text'], $topic->message_thread_id);
                    }
                }

                Storage::disk('local')->put('message.txt', json_encode($mes));

                if ($mes['message']['chat']['id'] == $configTele['chat_id_b'] && !empty($mes['message']['text'])) {
                    if ($mes['message']['text'] == '/subscribe') {
                        if (!empty($mes['message']['reply_to_message']['forum_topic_created']['name'])) {
                            $newTopic = [
                                "title" => $mes['message']['reply_to_message']['forum_topic_created']['name'],
                                "message_thread_id" => $mes['message']['message_thread_id'],
                                "status_message" => true
                            ];

                            $text = $this->subscribe($configTele['id'], $listTopic, $newTopic);
                        }
                    }

                    if ($mes['message']['text'] == '/unsubscribe') {
                        if (!empty($mes['message']['reply_to_message']['forum_topic_created']['name'])) {
                            $text = $this->unsubscribe($configTele['id'], $listTopic, $mes['message']['message_thread_id']);
                        }
                    }

                    if ($mes['message']['text'] == '/banChat') {
                        if (!empty($mes['message']['reply_to_message']['forum_topic_created']['name'])) {
                            $text = $this->banChatConfig($configTele['id'], $listTopic, false, $mes['message']['message_thread_id']);
                        }
                    }


                }
            }

            return true;
        } catch (Exception $exception) {
            return true;
        }
    }

    public function deleteMessage($token, $chatId, $messageId)
    {
        $urlApi = 'https://api.telegram.org/bot' . $token . '/deleteMessage?chat_id=' . $chatId . '&message_id=' . $messageId;
    }

    public function banChatConfig($id, $listTopic, $mute, $message_thread_id)
    {
        try {
            if (!empty($listTopic)) {
                array_map(function ($item) use ($mute, $message_thread_id) {
                    if ($item->message_thread_id == $message_thread_id) {
                        $item->status_message = $mute;
                    }
                }, $listTopic);
            }

            $configTele = ConfigTelegram::find($id);
            $configTele->fill(["topic" => json_encode($listTopic)]);
            $configTele->save();

            return true;
        } catch (\Exception $exception) {
            return false;
        }
    }

    public function subscribe($id, $listTopic, $topicInfo)
    {
        $listTopic = !empty($listTopic) ? $listTopic : [];
        $listTopic[] = $topicInfo;

        try {
            $configTele = ConfigTelegram::find($id);
            $configTele->fill(["topic" => json_encode($listTopic)]);
            $configTele->save();

            return true;
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }

    public function unsubscribe($id, $listTopic, $message_thread_id)
    {
        $listTopic = !empty($listTopic) ? array_filter($listTopic, function ($item) use ($message_thread_id) {
            return $item->message_thread_id != $message_thread_id;
        }) : [];

        try {
            $configTele = ConfigTelegram::find($id);
            $configTele->fill(["topic" => json_encode($listTopic)]);
            $configTele->save();

            return true;
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }

    public function getUserAdmin($id, $token, $chatId)
    {
        $urlApi = 'https://api.telegram.org/bot' . $token . '/getChatAdministrators?chat_id=' . $chatId;


    }

    public function createConfig(Request $request)
    {
        try {
            $dataCreate = $request->all();
            if (!empty($dataCreate->topic)) {
                $dataCreate->topic = json_encode($request->topic);
            }

            if (!empty($dataCreate['token_a'])) {
                $this->dangKyWebhook($dataCreate['token_a']);
            }

            if (!empty($dataCreate['token_b'])) {
                $this->dangKyWebhook($dataCreate['token_b']);
            }

            $config = new ConfigTelegram($dataCreate);
            $config->save();

            return 'thành công';
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }

    public function dangKyWebhook($token)
    {
        $apiDK = 'https://api.telegram.org/bot' . $token . '/setWebhook?url=https://' . request()->getHost() . '/webhook-test';

        $headers = [

        ];

        $ressponse = Http::withHeaders($headers)->get($apiDK);
        $statusCode = $ressponse->status();
        Storage::disk('local')->put('webhook.txt', $statusCode);

        if ($statusCode == 200) {
            $responseBody = json_decode($ressponse->getBody(), true);
            $data = $responseBody;
        }
    }

    public function sendMesToTopic($token, $chatID, $text, $message_thread_id = "")
    {
        $apiSend = 'https://api.telegram.org/bot' . $token . '/sendMessage';

        $query = [
            "text" => $text,
            "chat_id" => $chatID,
//            "parse_mode" => "html",
//            "disable_notification" => true,
        ];

        if (!empty($message_thread_id)) {
            $query['message_thread_id'] = $message_thread_id;
        }

        Storage::disk('local')->put('log.txt', json_encode($query));

        $headers = [

        ];

        $ressponse = Http::withHeaders($headers)->post($apiSend, $query);

        $statusCode = $ressponse->status();

        Storage::disk('local')->put('status.txt', $statusCode);

        if ($statusCode == 200) {
            $responseBody = json_decode($ressponse->getBody(), true);
            $data = $responseBody;

            Storage::disk('local')->put('file.txt', json_encode($data));
        }
    }
}

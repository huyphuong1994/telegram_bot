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
        $listConfig = ConfigTelegram::all();

        return view('welcome', compact('listConfig'));
    }

    public function destroy($id)
    {
        try {
            ConfigTelegram::destroy($id);

            return 'Xóa thành công';
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }

    public function getWebhook(Request $request)
    {
        try {
            $mes = $request->all();

            $listConfig = ConfigTelegram::all();

            foreach ($listConfig as $config) {
                $listTopic = json_decode($config['topic']);
                $listAdminB = json_decode($config['admins_b']);

                if (!empty($mes['message']['text']) && $mes['message']['chat']['id'] == $config['chat_id_b']) {
                    if ($mes['message']['text'] == '/admin') {
                        $this->getUserAdmin($config['id']);
                    }
                }

                $this->getActions($mes, $config, $listTopic, $listAdminB);
            }

            return true;
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }

    public function getActions($mes, $configTele, $listTopic, $listAdminB)
    {
        try {
            if (!empty($mes['message']) && !empty($mes['message']['chat']['id'])) {


                if ($mes['message']['chat']['id'] == $configTele['chat_id_a'] && !empty($mes['message']['text'])) {
                    foreach ($listTopic as $topic) {
                        $this->sendMesToTopic($configTele['token_b'], $configTele['chat_id_b'], $mes['message']['text'], $topic->message_thread_id);
                    }

                    if ($mes['message']['text'] == '/getChatId') {
                        $this->sendMesToTopic($configTele['token_a'], $mes['message']['chat']['id'], 'ChatId của bạn là: ' . $mes['message']['chat']['id'], $mes['message']['message_thread_id']);
                    }
                }

                if ($mes['message']['chat']['id'] == $configTele['chat_id_b'] && !empty($mes['message']['text'])) {
                    if ($mes['message']['text'] == '/theoDoi' && in_array($mes['message']['from']['id'], $listAdminB)) {
                        if (!empty($mes['message']['reply_to_message']['forum_topic_created']['name'])) {
                            $newTopic = [
                                "title" => $mes['message']['reply_to_message']['forum_topic_created']['name'],
                                "message_thread_id" => $mes['message']['message_thread_id'],
                                "status_message" => true
                            ];

                            $this->subscribe($configTele['id'], $listTopic, $newTopic);
                        }
                    }

                    if (!empty($listTopic)) {
                        if ($mes['message']['text'] == '/boThoeDoi' && in_array($mes['message']['from']['id'], $listAdminB)) {
                            if (!empty($mes['message']['reply_to_message']['forum_topic_created']['name'])) {
                                $this->unsubscribe($configTele['id'], $listTopic, $mes['message']['message_thread_id']);
                            }
                        }

                        if ($mes['message']['text'] == '/khoa' && in_array($mes['message']['from']['id'], $listAdminB)) {
                            if (!empty($mes['message']['reply_to_message']['forum_topic_created']['name'])) {
                                $this->banChatConfig($configTele['id'], $listTopic, false, $mes['message']['message_thread_id']);
                            }
                        }

                        if ($mes['message']['text'] == '/boKhoa' && in_array($mes['message']['from']['id'], $listAdminB)) {
                            if (!empty($mes['message']['reply_to_message']['forum_topic_created']['name'])) {
                                $this->banChatConfig($configTele['id'], $listTopic, true, $mes['message']['message_thread_id']);
                            }
                        }

                        if (!empty($listAdminB)) {
                            foreach ($listTopic as $topic) {
                                if ($topic->message_thread_id == $mes['message']['message_thread_id'] && !$topic->status_message && !in_array($mes['message']['from']['id'], $listAdminB)) {
                                    $this->deleteMessage($configTele['token_b'], $configTele['chat_id_b'], $mes['message']['message_id']);
                                }
                            }
                        }
                    }
                }
            }

            return true;
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }

    public function deleteMessage($token, $chatId, $messageId)
    {
        $urlApi = 'https://api.telegram.org/bot' . $token . '/deleteMessage?chat_id=' . $chatId . '&message_id=' . $messageId;

        $headers = [

        ];

        Storage::disk('local')->put('admins.txt', ($urlApi));

        $ressponse = Http::withHeaders($headers)->get($urlApi);
        $statusCode = $ressponse->status();

        if ($statusCode == 200) {
            $responseBody = json_decode($ressponse->getBody(), true);
            $data = $responseBody;

            return $data;
        }

        return false;
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

//    public function getUserAdmin(Request $request)
    public function getUserAdmin($id)
    {
        try {
            $config = ConfigTelegram::where('id', $id)->first();

            if (empty($config)) {
                return 'Chưa có config';
            }

            $urlApi = 'https://api.telegram.org/bot' . $config->token_b . '/getChatAdministrators?chat_id=' . $config->chat_id_b;

            $headers = [

            ];

            Storage::disk('local')->put('admins.txt', ($urlApi));

            $ressponse = Http::withHeaders($headers)->get($urlApi);
            $statusCode = $ressponse->status();

            if ($statusCode == 200) {
                $responseBody = json_decode($ressponse->getBody(), true);
                $data = $responseBody;

                $listAdminId = array_map(function ($item) {
                    return $item['user']['id'];
                }, $data['result']);


                $config->fill(["admins_b" => json_encode($listAdminId)]);

                $config->save();

                return true;
            }
            return false;
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }

    public function createConfig(Request $request)
    {
        try {
            $dataCreate = $request->all();
            if (!empty($dataCreate['token_a'])) {
                $p = $this->dangKyWebhook($dataCreate['token_a']);

                if (!$p) {
                    return 'Thất bại';
                }
            }

            if (!empty($dataCreate['token_b'])) {
                $p = $this->dangKyWebhook($dataCreate['token_b']);
                if (!$p) {
                    return 'Thất bại';
                }
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
        try {
            $apiDK = 'https://api.telegram.org/bot' . $token . '/setWebhook?url=https://' . request()->getHost() . '/webhook-test';

            $headers = [

            ];

            $ressponse = Http::withHeaders($headers)->get($apiDK);
            $statusCode = $ressponse->status();
            Storage::disk('local')->put('webhook.txt', $statusCode);

            if ($statusCode == 200) {
                $responseBody = json_decode($ressponse->getBody(), true);
                $data = $responseBody;

                return $data;
            }

            return false;
        } catch (\Exception $exception) {
            return $exception->getMessage();
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

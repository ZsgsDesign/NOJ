<?php

namespace App\Models\Eloquent\Messager;

use App\Models\Eloquent\Message;

class HomeworkMessager extends UniversalMessager
{
    public static function sendNewHomeworkMessageToUser($config)
    {
        $message = Message::where([
            'receiver' => $config['receiver'],
            'type'     => $config['type'],
            'unread'   => true
        ])->first();

        if (filled($message)) {
            $data = json_decode($message->data, true);
            foreach ($data['homework'] as $homework) {
                if($homework['id'] != $config['data']['homework'][0]['id']) {
                    array_push($data['homework'], $config['data']['homework'][0]);
                }
            }
            $message->data = json_encode($data);
            $message->level = $config['level'];
            $message->save();
            return true;
        }

        return self::sendUniversalMessage($config);
    }

    private static function proceedHomeworkAndGroupInfo($data)
    {
        $homeworkList = [];

        foreach ($data['homework'] as $homework) {
            $id = $homework['id'];
            $title = $homework['title'];
            $gcode = $homework['gcode'];
            $url = route('group.homework', [
                'gcode' => $gcode,
                'homework_id' => $id,
            ]);
            $homeworkList[] = "* [$title]($url)";
        }

        $homeworkString = implode(PHP_EOL, $homeworkList);

        return [$homeworkString];
    }

    public static function formatNewHomeworkMessageToUser($data)
    {
        [$homeworkString] = self::proceedHomeworkAndGroupInfo($data);

        return self::formatUniversalMessage('message.homework.new.desc', [
            'homeworkList' => $homeworkString,
            'receiver' => $data['receiver'],
            'sender' => $data['sender'],
        ]);
    }
}

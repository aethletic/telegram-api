<?php 

namespace Telegram;

use Telegram\Bot;

class Statistics
{
    public static function collect()
    {
        $bot = Bot::getInstance();
        $date = $bot->helper()->midnight();

        // messages stats
        $isNewDate = $bot->db('stats_messages')->where('date', $date)->count() == 0;
        if ($isNewDate) {
            $bot->db('stats_messages')->insert([
                'date' => $date,
                'count' => 1,
            ]);
        } else {
            $bot->db('stats_messages')->where('date', $date)->increment('count', 1);
        }

        // new users stats
        $isNewDate = $bot->db()->table('stats_new_users')->where('date', $date)->count() == 0;
        if ($bot->isNewUser()) {
            if ($isNewDate) {
                $bot->db('stats_new_users')->insert([
                    'date' => $date,
                    'count' => 1,
                ]);
            } else {
                $bot->db('stats_new_users')->where('date', $date)->increment('count', 1);
            }
        } else {
            if ($isNewDate) {
                $bot->db('stats_new_users')->insert([
                    'date' => $date,
                    'count' => 0,
                ]);
            }
        }

        $update = $bot->update();

        if (!$update) {
            return;
        }

        $insert = [
            'date' => time(),
            'user_id' => $bot->update('*.from.id'),
            'user' => $bot->update('*.from.first_name'),
            'value' => json_encode($update->toArray(), JSON_UNESCAPED_UNICODE)
        ];

        $bot->db('messages')->insert($insert);
    }
}
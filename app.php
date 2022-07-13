<?php
date_default_timezone_set('Asia/Jakarta');
require_once("twitteroauth/twitteroauth.php");

function randomline($tweet)
{
    $lines = file($tweet);
    return $lines[array_rand($lines)];
}

/* Data-data API Twitter v.1 */
define('consumer_key', "");
define('consumer_secret', "");
define('oauth_token', "");
define('oauth_secret', "");
define('username', "");

$api_twitter = new TwitterOAuth(consumer_key, consumer_secret, oauth_token, oauth_secret);
while (True) {
    $i = 0;
    $search_tweet = $api_twitter->get('search/tweets', ['q' => 'mutualan', 'result_type' => 'recent', 'count' => '10']);
    foreach ($search_tweet->statuses as $tweet_keyword) {
        $id_tweet = $tweet_keyword->id;
        $tweet = $tweet_keyword->text;
        $username_target = $tweet_keyword->user->screen_name;
        $id_target = $tweet_keyword->user->id;
        $check_target = $api_twitter->get('users/show', ['id' => $id_target]);
        if ($check_target->following == TRUE) {
            echo "@{$username_target} - sudah kamu follow (skipped)\n";
        } else {
            $api_twitter->post('friendships/create', ['user_id' => $id_target]);
            $tweet_bot = randomline('mutual.txt');
            $reply_tweet = "@" . $username_target . " " . $tweet_bot;
            $reply = $api_twitter->post('statuses/update', ['status' => $reply_tweet, 'in_reply_to_status_id' => $id_tweet]);
            if (isset($reply->text)) {
                echo "@{$username_target} - berhasil kamu follow dengan tweet : {$reply_tweet}";
                $i++;
            }
        }
        sleep(3);
    }
    echo "Delay . . . .\n";
    sleep(800);
    $tweet_me = $api_twitter->get('statuses/user_timeline', array('count' => $i, 'screen_name' => username));
    foreach ($tweet_me as $tweets) {
        $id_delete = $tweets->id;
        $tweet_delete = $tweets->text;
        $delete = $api_twitter->post("statuses/destroy/" . $id_delete);
        if (isset($delete->text)) {
            echo "Berhasil hapus tweet - " . $tweet_delete . "\n";
        } else {
            echo "Tidak Berhasil hapus tweet";
            exit();
        }
        sleep(3);
    }
    sleep(120);
}

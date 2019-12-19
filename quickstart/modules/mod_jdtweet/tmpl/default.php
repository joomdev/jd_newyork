    
<?php
/**
 * @package   JD Tweet
 * @author    JoomDev https://www.joomdev.com
 * @copyright Copyright (C) 2009 - 2019 JoomDev.
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
// no direct access

defined( '_JEXEC' ) or die;
$document =JFactory::getDocument();
$document->addStyleSheet(JURI::base()."modules/mod_jdtweet/assets/style.css","text/css","screen");
/*twitter settings*/
$accesstoken=$params->get('accesstoken');
$accesstokensecret=$params->get('accesstokensecret');
$consumerkey=$params->get('consumerkey');
$consumersecret=$params->get('consumersecret');
$moduleclass_sfx=$params->get("moduleclass_sfx");

if(!empty($accesstoken) && !empty($accesstokensecret) && !empty($consumerkey) && !empty($consumersecret)){
$settings = array(
    'oauth_access_token' => trim($params->get('accesstoken')),
    'oauth_access_token_secret' => trim($params->get('accesstokensecret')),
    'consumer_key' => trim($params->get('consumerkey')),
    'consumer_secret' => trim($params->get('consumersecret'))
);

/*URL - Twitter Timeline*/
$url = "https://api.twitter.com/1.1/statuses/user_timeline.json";
$requestMethod = "GET";
$getfield = '?screen_name='.$params->get('twitter_username').'&count='.$params->get('count');
$twitter = new TwitterAPIExchange($settings);
$string = json_decode($twitter->setGetfield($getfield)
->buildOauth($url, $requestMethod)
->performRequest(),$assoc = TRUE);

 

function addLink($string)
	{
		$pattern = '/((ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?)/i';
		$replacement = '<a class="tweet_url" href="$1">$1</a>';
		$string = preg_replace($pattern, $replacement, $string);
		return $string;
	}
echo '<div id="jd-twitter" class="jd-twitter '.$moduleclass_sfx.'">';

foreach($string as $items)
    {
	 
		echo '<div class="twitter-article">';
		echo '<div class="twitter-pic"><a href="https://twitter.com/'.$items['user']['screen_name'].'" ><img src="'.$items['user']['profile_image_url_https'].'"images/twitter-feed-icon.png" width="42" height="42" alt="twitter icon" /></a></div>';
		echo '<span class="tweetprofilelink"><strong><a href="https://twitter.com/'.$items['user']['screen_name'].'" >'.$items['user']['name'].'</a></strong> <a href="https://twitter.com/'.$items['user']['screen_name'].'" >@'.$items['user']['screen_name'].'</a></span><span class="tweet-time"><a href="https://twitter.com/'.$items['user']['screen_name'].'/status/'.$items['id_str'].'"></a></span>';
		echo '<div class="twitter-text"><p>'.addLink($items['text']).'</p></div>';
		echo '</div>';
    }?>
    <div align="left" style="color:#024292;margin-bottom:3px;font-size:9px">
	<a target="_blank" class="external" title="" href="">
		<span style="color:#024292;margin-bottom:3px;font-size:9px"></span>
	</a>
	</div>
    <?php 
echo '</div>';
} else{
	echo '<p class="danger">Please Enter All Credentials Carefully !! </p>';
}
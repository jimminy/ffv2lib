<?php
// Copyright 2008 FriendFeed
// Updated to v2 API methods by James Fuller
//
// Licensed under the Apache License, Version 2.0 (the "License"); you may
// not use this file except in compliance with the License. You may obtain
// a copy of the License at
//
//     http://www.apache.org/licenses/LICENSE-2.0
//
// Unless required by applicable law or agreed to in writing, software
// distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
// WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
// License for the specific language governing permissions and limitations
// under the License.
	


// Includes the  OAUTH protocol for authenticating users
//include_once(dirname(__FILE__) . '/../oauth/oAuth.php');

// This module requires the Curl PHP module, available in PHP 4 and 5
assert(function_exists("curl_init"));

class FriendFeed
{
	// Oauth hasn't been added and couldn't be tested do to the inability 
	// of registering an app. 

	// protected $consumer = null;
	// protected $key = null;
	// protected $secret = null;
	// protected $access_token = null;

	// function FriendFeed_Oauth($key, $secret, $access_token=null) {
	// 	$this->key = $key;
	// 	$this->secret = $secret;
	// 	$this->consumer = new OAuthConsumer($key, $secret, null);
	// 	$this->set_access_token($access_token);
	// }
	
	function FriendFeed($auth_nickname=null, $auth_key=null) {
		$this->auth_nickname = $auth_nickname;
		$this->auth_key = $auth_key;
	}
	
	//Resource Fetching Functions

	// Returns the public feed with everyone's public entries.
    //
    // Authentication is not required.
	function fetch_public_feed($service=null, $start=0, $num=30) {
		return $this->fetch_feed("/feed/public", $service, $start, $num);
	}
	

	// Returns the entries the authenticated user sees on their home page.
    //
    // Authentication is always required.
	function fetch_home_feed($service=null, $start=0, $num=30) {
		return $this->fetch_feed("/feed/home", $service, $start, $num);
    }

	// Returns the entries shared by the user with the given nickname.
    //
    // Authentication is required if the user's feed is not public.
	function fetch_user_feed($nickname, $service=null, $start=0, $num=30) {
		return $this->fetch_feed("/feed/" . urlencode($nickname), 
				$service, $start, $num);
	}

	// Returns the most recent entries the given user has commented on.
	function fetch_user_comments_feed($nickname, $service=null, $start=0,
			$num=30) {
		return $this->fetch_feed("/feed/" . urlencode($nickname) . "/comments",
				$service, $start, $num);
	}
	
	// Returns the most recent entries the given user has "liked."
	function fetch_user_likes_feed($nickname, $service=null, $start=0,
			$num=30) {
		return $this->fetch_feed("/feed/" . urlencode($nickname) . "/likes",
				$service, $start, $num);
	}

	// Returns the most recent entries the given user has commented on or "liked."
	function fetch_user_discussion_feed($nickname, $service=null, $start=0,
			$num=30) {
		return $this->fetch_feed("/feed/" . urlencode($nickname) . "/discussion",
				$service, $start, $num);
	}

	// Searches over entries in FriendFeed.
    //
    // If the request is authenticated, the default scope is over all of the
    // entries in the authenticated user's Friends Feed. If the request is
    // not authenticated, the default scope is over all public entries.
    //
    // The query syntax is the same syntax as
    // http://friendfeed.com/search/advanced
	function search($nickname, $query, $num = 30) {
		return $this->fetch_feed("/search", null, 0, $num, urlencode($nickname), urlencode($query), null);
	}

	//Resource Modifying Functions
	//Authentication Required

	// Publishes the given textual message to the authenticated user's feed.
    //
    // See publish_link for additional options.
	function publish_message($message, $link=null, $comment=null, $rooms=null,
				$image_urls=null, $audio_urls=null) {
		return $this->publish_link($message, $link, $comment, $rooms,
				$image_urls, $audio_urls);
	}
	
	// Updates the textual message with the given ID.
	function edit_message($entry_id, $body) {
		$this->fetch("/entry", null, array(
			"id" => $entry_id,
			"body" => $body,
		));
	}
	
	// Deletes the message with the given ID.
	function delete_message($entry_id) {
		$this->fetch("/entry/delete", null, array(
			"id" => $entry_id,
		));
	}

	// Un-deletes the message with the given ID.
	function undelete_message($entry_id) {
		$this->fetch("/entry/delete", null, array(
			"id" => $entry_id, 
			"undelete" => 1,
		));
	}

	// Adds the given comment to the entry with the given ID.
    //
    // We return the ID of the new comment, which can be used to edit or
    // delete the comment.
	function add_comment($entry_id, $body) {
		$result = $this->fetch("/comment", null, array(
			"entry" => $entry_id,
			"body" => $body,
		));
		return $result->id;
	}

	// Updates the comment with the given ID.
	function edit_comment($comment_id, $body) {
		$this->fetch("/comment", null, array(
			"id" => $comment_id,
			"body" => $body,
		)); 	
	}

	// Deletes the comment with the given ID.
	function delete_comment($comment_id) {
		$this->fetch("/comment/delete", null, array(
            "id" => $comment_id,
		));
    }

	// Un-deletes the comment with the given ID.
    function undelete_comment($comment_id) {
		$this->fetch("/comment/delete", null, array(
            "id" => $comment_id,
            "undelete" => 1,
		));
    }

	// 'Likes' the entry with the given ID.
    function add_like($entry_id) {
		$this->fetch("/like", null, array(
            "entry" => $entry_id,
		));
    }
	
	// Deletes the 'Like' for the entry with the given ID (if any).
	function delete_like($entry_id) {
		$this->fetch("/like/delete", null, array(
            "entry" => $entry_id,
		));
    }

	// Publishes the given link/title to the authenticated user's feed.
    //
    // Authentication is always required.
    //
    // image_urls is a list of URLs that will be downloaded and included as
    // thumbnails beneath the link. The thumbnails will all link to the
    // destination link. If you would prefer that the images link somewhere
    // else, you can specify images instead, which should be an array of
    // name-associated arrays of the form array("url"=>...,"link"=>...).
    // The thumbnail with the given url will link to the specified link.
    //
    // audio_urls is a list of MP3 URLs that will show up as a play
    // button beneath the link. You can optionally supply audio[]
    // instead, which should be a list of name-associated arrays of the 
    // form ("url"=> ..., "title"=> ...). The given title will appear when
    // the audio file is played.
    //
    // We return the parsed/published entry as returned from the server,
    // which includes the final thumbnail URLs as well as the ID for the
    // new entry.
    function publish_link($body, $link, $comment=null, $rooms=null,
	    		$image_urls=null, $audio_urls=null) {
		$post_args = array("body" => $body);
		if ($link) $post_args["link"] = $link;
		if ($comment) $post_args["comment"] = $comment;
		if ($room) $post_args["to"] = $room;
		if ($image_urls) $post_args["image_url"] = $image_urls;
		if ($audio_urls) $post_args["audio_url"] = $audio_urls;

		$feed = $this->fetch_feed("/entry", null, null, null,
			null, null, $post_args);
		return $feed->entries[0];
    }
	
	// Internal function to download, parse, and process FriendFeed feeds.
    function fetch_feed($uri, $service, $start, $num, $nickname=null,
			$query=null, $post_args=null) {
		$url_args = array(
	    	"service" => $service,
	    	"start" => $start,
	    	"num" => $num,
       	);
		if ($nickname) $url_args["nickname"] = $nickname;
		if ($query) $url_args["q"] = $query;
		$feed = $this->fetch($uri, $url_args, $post_args);
		if (!$feed) return null;

		return $feed;
    }
	
	// Performs an authenticated FF request, parsing the JSON response.
    function fetch($uri, $url_args=null, $post_args=null) {
		if (!$url_args) $url_args = array();
		$url_args["format"] = "json";
		$pairs = array();
		foreach ($url_args as $name => $value) {
	    	$pairs[] = $name . "=" . urlencode($value);
		}
		$url = "http://friendfeed-api.com/v2" . $uri . "?" . join("&", $pairs);
	
		$curl = curl_init("friendfeed.com");
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

		if ($this->auth_nickname && $this->auth_key) {
	    	curl_setopt($curl, CURLOPT_USERPWD,
				$this->auth_nickname . ":" . $this->auth_key);
	    	curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		}
		if ($post_args) {

	    	curl_setopt($curl, CURLOPT_POST, 1);
	    	curl_setopt($curl, CURLOPT_POSTFIELDS, $post_args);
		}
		$response = curl_exec($curl);
		$info = curl_getinfo($curl);
		curl_close($curl);
		if ($info["http_code"] != 200) {
	    	return null;
		}
		return $this->json_decode($response);
    }
	
	// JSON decoder that uses the PHP 5.2+ functionality if available
	function json_decode($str) {
	if(function_exists("json_decode")) {
		return json_decode($str);
	} else {
		require_once("JSON.php");
		$json = new Services_JSON();
		return $json->decode($str);
	}
	}
	
}
?>
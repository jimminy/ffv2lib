# Friendfeed v2 API PHP


Supports both Basic Auth and OAuth and trys to remain as backwards compatible
as possible to the original library. It also includes extra methods in an
effort to make it as fully functioning as possible.

### Basic Auth

To get started, you need to do 3 things:

>   1) include/require the library

>   2) Initialize a new FriendFeed object by calling
>>      FriendFeed::FriendFeed_Basic($username, $remotekey)

>   3) Make a method call on the object (e.g. $obj->fetch\_home\_feed();)

It is as simple as that to get started with the Basic Auth system.

### OAuth

Getting started with OAuth a bit more complex, so I've added a few files to make it a
bit easier, but you still have several steps to complete before you begin.

Before you begin, you must set up an application on Friendfeed to get your
consumer key and consumer secret, as well as the callback* which will be
required. Set up the application at http://friendfeed.com/api/applications

> *Currently, I haven't added modifiable callbacks, so you will need to set your callback url to point at the location of 'callback.php'

The only other step you will need to make is in setting your consumer key and
consumer secret for use throughout. To make this change, edit the 'config.php'
file, by replacing the value for CONSUMER\_KEY and CONSUMER\_SECRET with their
respective values.

The next steps are almost the exact same as for Basic Auth.

>   With the provided 'index.php' file.

>>  1.a) Library is included, by default

>>  1.b) Un-comment the header redirect, to 'redirect.php,' so that it will automatically take you through the process of authorization, if not already authorized.**

>>  2) Initialize a new FriendFeed object by calling
>>>     FriendFeed::FriendFeed_OAuth($consumer_key, $consumer_secret, $auth_token)

>>  3) Make a method call on the object (e.g. $obj->fetch\_home\_feed();)

>>> **If you don't, you will have to navigate through 'redirect.php' to begin
        the process of authorization.

###OAuth Information

If you wish to implement your own application with your own redirect and
callback files, you will need to make sure you follow the steps in OAuth.
I won't be going into how to do this, but I will provide resources below.

>*  Hueniverse's Guide - http://hueniverse.com/oauth/
>*  LinkedIn's OAuth Overview - http://developer.linkedin.com/docs/DOC-1244
>*  OAuth for Dummies - http://marktrapp.com/blog/2009/09/17/oauth-dummies
>*  OAuth Playground - http://googlecodesamples.com/oauth_playground/

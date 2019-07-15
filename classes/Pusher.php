<?php namespace Igniter\Pusher\Classes;

use Admin\Models\Customers_model;
use Igniter\Pusher\Models\Settings;
use Input;
use Main\Facades\Auth;
use Pusher\Pusher as PusherBase;

class Pusher
{
    use \Igniter\Flame\Traits\Singleton;

    /**
     * @var string Pusher key
     */
    protected $key;

    /**
     * @var string Pusher secret
     */
    protected $secret;

    /**
     * @var string Pusher app id
     */
    protected $appId;

    /**
     * @var array Pusher options
     */
    protected $options = [];

    /**
     * @var \Pusher\Pusher
     */
    protected $pusher;

    /**
     * @var \Igniter\Flame\Auth\Models\User
     */
    protected $authUser;

    /**
     * Creates an instance of Pusher using config values
     */
    public function initialize()
    {
        $this->key = Settings::get('key');
        $this->secret = Settings::get('secret');
        $this->appId = Settings::get('app_id');
        $this->options['cluster'] = Settings::get('cluster');
        $this->options['encrypted'] = Settings::get('encrypted');
        $this->pusher = new PusherBase($this->key, $this->secret, $this->appId, $this->options);
    }

    /**
     * Returns the pusher instance
     * @return mixed
     */
    public function getPusher()
    {
        return $this->pusher;
    }

    public function setAuthUser($user)
    {
        $this->authUser = $user;

        return $this;
    }

    /**
     * Handles the presence and private channel authentications
     * @param $channelName
     * @param $socketId
     * @return $this
     */
    public function auth($channelName, $socketId)
    {
        // Ensures logged in user owns the channel
        if (!$this->checkUserChannel($channelName)) {
            // Otherwise Authentication failed
            $this->reject();

            return $this;
        }

        if ($this->channelIsPresence($channelName))
            $this->allowPresence($channelName, $socketId, $this->authUser);

        if ($this->channelIsPrivate($channelName))
            $this->allowPrivate($channelName, $socketId);

        return $this;
    }

    /**
     * Trigger a pusher event
     * @param $channel
     * @param $event
     * @param $data
     * @return $this
     */
    public function trigger($channel, $event, $data)
    {
        $this->pusher->trigger($channel, $event, $data, post('socket_id'));

        return $this;
    }

    public static function runEntryPoint()
    {
        $channelName = Input::post('channel_name');
        $socketId = Input::post('socket_id');
        if (!strlen($channelName) OR !strlen($socketId))
            return;

        $user = \App::runningInAdmin() ? \AdminAuth::user() : Auth::user();

        self::instance()->setAuthUser($user)->auth($channelName, $socketId);
    }

    /**
     * Reject a pusher authentication API request
     */
    protected function reject()
    {
        header('', TRUE, 403);
        echo 'Forbidden';
    }

    protected function checkUserChannel($channelName)
    {
        // Allows an extra check for allowing every logged in user to have their own channel
        // Format looks like: private-userchannel1 where 1 is their user ID.
        if (!starts_with($channelName, 'private-userchannel'))
            return TRUE;

        if ($this->authUser AND substr($channelName, 19) == $this->authUser->getKey())
            return TRUE;

        return FALSE;
    }

    protected function channelIsPresence($name)
    {
        return strpos($name, 'presence') === 0;
    }

    protected function channelIsPrivate($name)
    {
        return strpos($name, 'private') === 0;
    }

    /**
     * Allow a private channel authentication API request
     * @param $channelName
     * @param $socketId
     */
    protected function allowPrivate($channelName, $socketId)
    {
        echo $this->pusher->socket_auth($channelName, $socketId);
    }

    /**
     * Allow a presence channel authentication
     * @param $channelName
     * @param $socketId
     * @param $user \Igniter\Flame\Auth\Models\User
     * @throws \Pusher\PusherException
     */
    protected function allowPresence($channelName, $socketId, $user)
    {
        echo $this->pusher->presence_auth(
            $channelName,
            $socketId,
            $user->getKey(),
            [
                'id' => $user->getKey(),
                'name' => $user instanceof Customers_model ? $user->customer_name : $user->staff_name,
            ]
        );
    }
}
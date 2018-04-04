<?php
namespace Gwsn\Authentication\Models;

use Log;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Http\Request;

/**
 * Class AuthenticateService
 * @package Gwsn\Authentication\Models
 */
class AuthenticateService {

    /** @var string */
    private $user = null;

    /** @var string*/
    private $pass = null;

    /** @var boolean $authenticated */
    private $authenticated = false;

    /** @var Request $request */
    private $request;

    /** @var Authenticate $authenticate */
    private $authenticate;

    /**
     * @return string
     */
    public function getUser()
    : string {
        return $this->user;
    }

    /**
     * @param string $user
     *
     * @return AuthenticateService
     */
    public function setUser( string $user )
    : AuthenticateService {
        $this->user = $user;

        return $this;
    }

    /**
     * @return string
     */
    public function getPass()
    : string {
        return $this->pass;
    }

    /**
     * @param string $pass
     *
     * @return AuthenticateService
     */
    public function setPass( string $pass )
    : AuthenticateService {
        $this->pass = $pass;

        return $this;
    }

    /**
     * @return Authenticate
     */
    public function getAuthenticate()
    : Authenticate {
        return $this->authenticate;
    }

    /**
     * @param Authenticate $authenticate
     * @return AuthenticateService
     */
    public function setAuthenticate(Authenticate $authenticate)
    : AuthenticateService {
        $this->authenticate = $authenticate;
        return $this;
    }




    /**
     * AuthenticateService constructor.
     *
     * @param Request $request
     */
    public function __construct(Request $request) {
        $this->request = $request;
        $this->authenticate = new Authenticate();
    }


    /**
     * Check the login based on user and pass
     *
     * @param string $user
     * @param string $pass
     *
     * @return bool
     */
    public function checkLogin(string $user = '', string $pass = '') {
        try {
            if(empty($user) || empty($pass)) {
                $this->sendEvent('authenticate', ['status' => false]);
                return false;
            }

            $this->setUser($user)->setPass($pass);

            if($this->authenticate->userLogin($this->getUser(), $this->getPass())) {
                $this->sendEvent('authenticate', ['status' => true]);
                return true;
            }
        } catch(UnauthorizedException $exception) {

        } catch(\Exception $exception) {

        }

        $this->sendEvent('authenticate', ['status' => false]);
        return false;
    }


    /**
     * Check the login based on Basic Auth
     *
     * @param $authHeader
     *
     * @return bool
     */
    public function checkBasicAuth($authHeader) {
        $user = null;
        $pass = null;

        try {
            if (strpos(strtolower($authHeader),'basic') !== 0) {
                $this->sendEvent('authenticate', ['status' => false]);
                return false;
            }

            list($user, $pass) = explode(':', base64_decode(substr($authHeader, 6)));

            $this->setUser($user)->setPass($pass);


            if($this->authenticate->userLogin($this->getUser(), $this->getPass())) {
                $this->sendEvent('authenticate', ['status' => true]);
                return true;
            }
        } catch(UnauthorizedException $exception) {

        } catch(\Exception $exception) {

        }

        $this->sendEvent('authenticate', ['status' => false]);
        return false;
    }


    /**
     * Get the authenticated user
     *
     * @return null
     */
    public function getAuthenticatedUser() {
        if($this->authenticated === false) {
            return null;
        }

        return ( new Account )->where( [
            ['email', $this->getUser()],
            ['disabled', 0],
            ['deleted_at', null],
        ])->firstOrFail();
    }


    /**
     * @param $handler
     * @param $eventData
     */
    private function sendEvent($handler, $eventData) {
        if($handler === 'authenticate' && array_key_exists('status', $eventData) && $eventData['status'] === true) {
            $this->authenticated = true;
        }

        // @todo send event to event service
    }
}
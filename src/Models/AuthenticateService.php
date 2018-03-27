<?php
namespace Gwsn\Authentication\Models;

use Illuminate\Validation\UnauthorizedException;
use Log;
use Illuminate\Http\Request;

class AuthenticateService {

    /** @var string */
    private $user = null;

    /** @var string*/
    private $pass = null;

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
     * AuthenticateService constructor.
     *
     * @param Request $request
     */
    public function __construct(Request $request) {
        $this->request = $request;
        $this->authenticate = new Authenticate();
    }


    /**
     * @param $authHeader
     *
     * @return bool
     */
    public function checkBasicAuth($authHeader) {
        $user = null;
        $pass = null;

        if (strpos(strtolower($authHeader),'basic') !== 0)
            return false;

        list($user, $pass) = explode(':', base64_decode(substr($authHeader, 6)));

        $this->setUser($user)->setPass($pass);

        try {
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
     * @param $handler
     * @param $eventData
     */
    private function sendEvent($handler, $eventData) {
    }
}
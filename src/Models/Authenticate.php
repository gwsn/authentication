<?php
namespace Gwsn\Authentication\Models;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\UnauthorizedException;

/**
 * Class Authenticate
 * @package Gwsn\Authentication\Models
 */
class Authenticate {

    /**
     * @param string $username
     * @param string $password
     *
     * @return array|bool
     */
    public function userLogin(string $username = '', string $password = '') {

        try {
            $account = $this->getAccount($username);

            if(!password_verify($password, $account->password)) {
                $this->registerLogin(false);
                throw new UnauthorizedException( "UnAuthorized", 401 );
            }

        } catch( ModelNotFoundException $e) {
            // Bad Login
            throw new UnauthorizedException("UnAuthorized", 401);
        }

        // Successfully login
        $this->registerLogin(true);
        // @todo Log also the user details like IP and other user specific details.

        return true;
    }

    /**
     * @param string $username
     * @return mixed
     */
    private function getAccount(string $username = '') {
        return ( new Account )->where( [
            ['email', $username],
            ['disabled', 0],
            ['deleted_at', null],
        ])->firstOrFail();
    }

    /**
     * @param int $timestamp
     * @return null|string
     */
    private function getDateTimeFromTimestamp($timestamp = 0) {
        if(!is_integer($timestamp) || $timestamp <= 0) {
            return null;
        }

        $timezone = new \DateTimeZone(env('APP_TIMEZONE', 'UTC'));
        $dateTime = new \DateTime('now', $timezone);

        return $dateTime->setTimestamp($timestamp)->format('Y-m-d H:i:s');
    }

    /**
     * @param bool $result
     */
    public function registerLogin(bool $result) {
        // @todo
    }

    /**
     * @param bool $result
     */
    public function registerAuthentication(bool $result) {
        // @todo
    }
}
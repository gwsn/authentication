<?php
namespace Gwsn\Authentication\Controllers;

use Gwsn\Authentication\Models\Account;
use Gwsn\Authentication\Models\AuthenticateService;
use Gwsn\Rest\BaseController;
use Illuminate\Http\Request;


/**
 * Class AccountController
 * @package Gwsn\Authentication\Controllers
 */
class AccountController extends BaseController {


    /** @var Account $account */
    private $account;

    /**
     * AccountsMainController constructor.
     */
    public function __construct() {
        $this->account = new Account();
    }

    /**
     * @param Request $request
     *
     * @return mixed
     */
    public function login( Request $request ) {
        try {
            $authHeader = $request->header('Authorization', null);

            $user = $request->input('email', $request->input('user', ''));
            $pass = $request->input('password', $request->input('pass', ''));

            $authService = new AuthenticateService($request);

            // Check if Authenticate headers are set and if the base64(username:password) exists
            if($authService->checkBasicAuth($authHeader) === false && $authService->checkLogin($user, $pass) === false) {
                return $this->failedResponse( $request, 'Unauthorized.', 401 );
            }

            // Clear response
            $request->replace([]);


            return $this->response($request, ['user' => $authService->getAuthenticatedUser()->accountGUID]);

        }
        catch ( \InvalidArgumentException $e ) {
            return $this->failedResponse( $request, $e->getMessage(), 400 );
        }
        catch ( \Exception $e ) {
            return $this->failedResponse( $request, $e->getMessage(), 500 );
        }

    }

    /**
     * @param Request $request
     *
     * @return mixed
     */
    public function create( Request $request ) {
        try {

            $response = $this->account->createAccount( $request->except('authUser') );

            // Clear response
            $request->replace([]);

            return $this->response( $request, $response );

        }
        catch ( \InvalidArgumentException $e ) {
            return $this->failedResponse( $request, $e->getMessage(), 400 );
        }
        catch ( \Exception $e ) {
            return $this->failedResponse( $request, $e->getMessage(), 500 );
        }

    }


    /**
     * @param Request $request
     * @param string  $username
     *
     * @return mixed
     */
    public function read( Request $request, $username ) {
        try {
            $this->validateUser($request, $username);

            $response = $this->account->readAccount( $username );

            return $this->response( $request, $response );

        }
        catch ( \InvalidArgumentException $e ) {
            return $this->failedResponse( $request, $e->getMessage(), 400 );
        }
        catch ( \Exception $e ) {
            return $this->failedResponse( $request, $e->getMessage(), 500 );
        }

    }

    /**
     * @param Request $request
     * @param string  $username
     *
     * @return mixed
     */
    public function update( Request $request, $username ) {
        try {
            $this->validateUser($request, $username);

            $response = $this->account->updateAccount( $username, $request->except('authUser', 'password') );

            return $this->response( $request, $response );

        }
        catch ( \InvalidArgumentException $e ) {
            return $this->failedResponse( $request, $e->getMessage(), 400 );
        }
        catch ( \Exception $e ) {
            return $this->failedResponse( $request, $e->getMessage(), 500 );
        }


    }

    /**
     * @param Request $request
     * @param string  $username
     *
     * @return mixed
     */
    public function delete( Request $request, $username ) {
        try {
            $this->validateUser($request, $username);

            $response = $this->account->deleteAccount( $username );

            return $this->response( $request, $response );

        }
        catch ( \InvalidArgumentException $e ) {
            return $this->failedResponse( $request, $e->getMessage(), 400 );

        }
        catch ( \Exception $e ) {
            return $this->failedResponse( $request, $e->getMessage(), 500 );
        }
    }

    /**
     * @param Request $request
     * @param string  $username
     *
     * @return mixed
     */
    public function changePassword( Request $request, $username ) {
        try {
            $this->validateUser($request, $username);

            $response = $this->account->changePassword( $username, ['password' => $request->input('password', null)] );

            // Clear response
            $request->replace([]);

            return $this->response( $request, $response );

        }
        catch ( \InvalidArgumentException $e ) {
            return $this->failedResponse( $request, $e->getMessage(), 400 );

        }
        catch ( \Exception $e ) {
            return $this->failedResponse( $request, $e->getMessage(), 500 );
        }
    }

    /**
     * @param Request $request
     * @param string  $username
     *
     * @return mixed
     */
    public function verify( Request $request, $hash ) {
        try {
            // Search for the account check the hash and verify

            $response = $this->account->verifyAccount( $hash, $request->all() );

            return $this->response( $request, $response );

        }
        catch ( \InvalidArgumentException $e ) {
            return $this->failedResponse( $request, $e->getMessage(), 400 );
        }
        catch ( \Exception $e ) {
            return $this->failedResponse( $request, $e->getMessage(), 500 );
        }


    }


    /**
     *
     */
    public function dummyDataUser() {

    }

    private function validateUser(Request $request, $username) {
        if($request->get('authUser', null) !== $username) {
            throw new \InvalidArgumentException("This account has not the correct privileges to do this action");
        }

        return true;

    }
}


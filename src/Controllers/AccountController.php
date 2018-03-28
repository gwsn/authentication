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

            $user = $request->input('email', $request->input('user', null));
            $pass = $request->input('password', $request->input('pass', null));

            $authenticateService = new AuthenticateService($request);

            // Check if Authenticate headers are set and if the base64(username:password) exists
            if($authenticateService->checkBasicAuth($authHeader) === false && $authenticateService->checkLogin($user, $pass) === false) {
                return response('Unauthorized.', 401);
            }

            // Clear response
            $request->replace([]);


            return $this->response($request, []);

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

            $response = $this->account->createAccount( $request->all() );

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

            $response = $this->account->readAccount( $username, $request->all() );

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

            $response = $this->account->updateAccount( $username, $request->all() );

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

            $response = $this->account->deleteAccount( $username, $request->all() );

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
}


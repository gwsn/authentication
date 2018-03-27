<?php
namespace Gwsn\Authentication\Controllers;

use Gwsn\Authentication\Models\Account;
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


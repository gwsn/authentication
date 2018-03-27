<?php
namespace Gwsn\Authentication\Models;


use Illuminate\Database\Eloquent\Model;


/**
 * Class Account
 *
 * @package Gwsn\Authentication\Models\Account
 */
class Account extends Model {


    protected $table = 'accounts';

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];


    /**
     * @param array $data
     *
     * @return array
     * @throws \InvalidArgumentException
     */
    public function createAccount( Array $data = [] ) {
        $data = $this->sanitize( $data );
        $this->validateLogin($data);
        $this->validateAccount( $data );

        $data['accountGUID'] = Uuid::create();
        $data['password'] = bcrypt($data['password']);

        $account = Account::where('email', $data['email']);

        if($account) {
            throw new \InvalidArgumentException("User already exists");
        }

        $account = new Account();
        $account->fill($data);
        $account->save();

        return $this->toArray();
    }

    /**
     * @param array $data
     *
     * @return array
     * @throws \InvalidArgumentException
     */
    public function readAccount( $accountGUID = null, Array $data = [] ) {

        // check the accountGUID of the logged in user;

        $account = Account::where('accountGUID', $accountGUID)->where('deleted_at', NULL)->where('disabled', 0);

        if($account) {
            return $account;
        }

        return [];
    }

    /**
     * @param array $data
     *
     * @return array
     * @throws \InvalidArgumentException
     */
    public function updateAccount( $accountGUID = null, Array $data = [] ) {
        $data = $this->sanitize( $data );
        $this->validateAccount( $data );

        $account = Account::where('accountGUID', $accountGUID)->where('deleted_at', NULL)->where('disabled', 0);
        $account->fill($data);
        $account->save();

        return $account;
    }

    /**
     * @param array $data
     *
     * @return array
     * @throws \InvalidArgumentException
     */
    public function deleteAccount( $accountGUID = null, Array $data = [] ) {
        // check the accountGUID of the logged in user;

        $account = Account::where('accountGUID', $accountGUID)->where('deleted_at', NULL)->where('disabled', 0);

        if($account) {
            $account->delete();
            return true;
        }


        return [];
    }

    /**
     * @param array $data
     *
     * @return array
     */
    private function sanitize( Array $data = [] ) {

        // Sanitize phone numbers
        // @todo

        return $data;
    }

    /**
     * Validate the user login data
     *
     * @param array $data
     *
     * @return bool
     * @throws \InvalidArgumentException
     */
    private function validateLogin( Array $data = [] ) {
        // params=> email, password
        $requiredKeys = [
            'email',
            'password',
        ];

        foreach ( $requiredKeys as $key ) {
            if ( empty( $data[ $key ] ) ) {
                $this->throwInvalidException( $key, 'is empty' );
            }
        }

        if ( ! filter_var( $data['email'], FILTER_VALIDATE_EMAIL ) ) {
            $this->throwInvalidException( 'email' );
        }

        if ( ! $this->validatePassword( $data['password'] ) ) {
            $this->throwInvalidException( 'password' );
        }

        return true;
    }

    /**
     * Validate the user account data
     *
     * @param array $data
     *
     * @return bool
     */
    private function validateAccount( Array $data = [] ) {
        // params=>  displayName, email, password, gender, firstName, insertion, surname, salutation, phone, mobile
        $requiredKeys = [
            'displayName',
            'gender',
            'firstName',
            // 'insertion', // insertion is not required
            'surname',
            'salutation',
            // 'phone', // phone is not required
            'mobile',
        ];
        $nameRegex    = '/^[A-Za-z0-9\s\.\,]+$/';

        foreach ( $requiredKeys as $key ) {
            if ( empty( $data[ $key ] ) ) {
                $this->throwInvalidException( $key, 'is empty' );
            }
        }


        // Check if displayName is regex [a-zA-Z0-9]
        if ( preg_match( $nameRegex, $data['displayName'], $matches ) !== 1 ) {
            $this->throwInvalidException( 'displayName' );
        }

        // Check if gender is 'male' or 'female'
        if ( !in_array($data['gender'], ['male', 'female'])) {
            $this->throwInvalidException( 'gender' );
        }

        // Check if the firstName is valid
        if ( preg_match( $nameRegex, $data['firstName'], $matches ) !== 1 ) {
            $this->throwInvalidException( 'firstName' );
        }

        // Check if the insertion is valid
        if ( ! empty($data['insertion']) && preg_match( $nameRegex, $data['insertion'], $matches ) !== 1 ) {
            $this->throwInvalidException( 'insertion' );
        }

        // Check if the surname is valid
        if ( preg_match( $nameRegex, $data['surname'], $matches ) !== 1 ) {
            $this->throwInvalidException( 'surname' );
        }

        // Check if the salutation is valid
        if ( preg_match( $nameRegex, $data['salutation'], $matches ) !== 1 ) {
            $this->throwInvalidException( 'salutation' );
        }

        // Check if its a valid phone number
        if ( ! $this->validatePhone( $data['phone'] ) ) {
            $this->throwInvalidException( 'phone' );
        }

        // Check if its a valid phone number
        if ( ! $this->validatePhone( $data['mobile'] ) ) {
            $this->throwInvalidException( 'mobile' );
        }

        return true;
    }

    /**
     * Check if the password is a valid password and strong enough
     *
     * @param string $password
     *
     * @return bool
     */
    private function validatePassword( $password = null ) {
        $i      = 1; // Minimal amount of times the character needs to be in the password.
        $length = 8; // Password Length

        // Regex magic
        $regex = [
            '/[A-Z]/', // uppercase
            '/[a-z]/', // lowercase
            '/[!@#$%^&*()\-_=+{};=>,<.>]/',  // special chars
            '/[0-9]/' // numbers
        ];

        foreach ( $regex as $r ) {
            if ( preg_match_all( $r, $password, $o ) < $i ) {
                return false;
            }
        }

        if ( strlen( $password ) < $length ) {
            return false;
        }

        return true;
    }

    /**
     * Validate the phone number
     *
     * @param string $phone
     *
     * @return bool
     */
    private function validatePhone( $phone = null ) {

        // Check if there are numbers and a few special characters in the number.
//        $regexPhone = '/(((0)[1-9]{2}[0-9][-]?[1-9][0-9]{5})|((\\+31|0|0031)[1-9][0-9][-]?[1-9][0-9]{6}))/';
//        $regexMobile = '/(((\\+31|0|0031)6){1}[1-9]{1}[0-9]{7})/i';
//
//        return (bool) ( preg_match( $regexPhone, $phone, $matches ) !== 1 || preg_match( $regexMobile, $phone, $matches ) !== 1 );
        return true;
    }

    /**
     * Wrapper for InvalidArgumentException
     *
     * @param string $key
     * @param string $msg
     *
     * @throws \InvalidArgumentException
     */
    private function throwInvalidException( $key = null, $msg = "is not matching the requirements" ) {
        throw new \InvalidArgumentException(  $key  . " => " . $msg, 1 );
    }
    /**
     * Get the relationships for the entity.
     *
     * @return array
     */
    public function getQueueableRelations()
    {
        // TODO: Implement getQueueableRelations() method.
    }

}
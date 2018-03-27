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
     * @param array $data
     *
     * @return array
     * @throws \InvalidArgumentException
     */
    public function createAccount( Array $data = [] ) {
        $data = $this->sanitize( $data );
        $this->validateAccount( $data );

        return [
            "clientID"        => 25,
            "displayName"     => "J.N. Overmars",
            "email"           => "jovermars@bizhost.nl",
            "password"        => "Secret!",
            "gender"          => "male",
            "firstName"       => "Jurn",
            "insertion"       => "",
            "surname"         => "Overmars",
            "salutation"      => "Dhr.",
            "phone"           => "085 3010884",
            "mobile"          => "06 13322424",
            "loginType"       => "basic_auth"
        ];
    }

    /**
     * @param array $data
     *
     * @return array
     * @throws \InvalidArgumentException
     */
    public function readAccount( $username = null, Array $data = [] ) {

        return [
            "accountID"   => 21,
            "clientID"    => 25,
            "displayName" => "J.N. Overmars",
            "email"       => "jovermars@bizhost.nl",
            "password"    => "Secret!",
            "gender"      => "male",
            "firstName"   => "Jurn",
            "insertion"   => "",
            "surname"     => "Overmars",
            "salutation"  => "Dhr.",
            "phone"       => "085 3010884",
            "mobile"      => "06 13322424",
            "loginType"   => "basic_auth",
        ];
    }

    /**
     * @param array $data
     *
     * @return array
     * @throws \InvalidArgumentException
     */
    public function updateAccount( $username = null, Array $data = [] ) {
        $data = $this->sanitize( $data );
        $this->validateAccount( $data );

        return [
            "accountID"       => 21,
            "clientID"        => 25,
            "displayName"     => "J.N. Overmars",
            "email"           => "jovermars@bizhost.nl",
            "password"        => "Secret!",
            "gender"          => "male",
            "firstName"       => "Jurn",
            "insertion"       => "",
            "surname"         => "Overmars",
            "salutation"      => "Dhr.",
            "phone"           => "085 3010884",
            "mobile"          => "06 13322424",
            "loginType"       => "basic_auth"
        ];
    }

    /**
     * @param array $data
     *
     * @return array
     * @throws \InvalidArgumentException
     */
    public function deleteAccount( $username = null, Array $data = [] ) {
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
            $this->throwInvalidException( 'Email' );
        }

        if ( ! $this->validatePassword( $data['password'] ) ) {
            $this->throwInvalidException( 'Password' );
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
        // params=> clientID, displayName, email, password, gender, firstName, insertion, surname, salutation, phone, mobile
        $requiredKeys = [
            'clientID',
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

        // validate the email and password
        $this->validateLogin();

        // Check if clientID is integer and positive value=>
        if ( ! is_numeric( $data['clientID'] ) || intval( $data['clientID'] ) <= 0 ) {
            $this->throwInvalidException( 'clientID' );
        }

        // Check if displayName is regex [a-zA-Z0-9]
        if ( preg_match( $nameRegex, $data['displayName'], $matches ) !== 1 ) {
            $this->throwInvalidException( 'displayName' );
        }

        // Check if gender is 'male' or 'female'
        if ( $data['gender'] !== 'male' || $data['gender'] !== 'female' ) {
            $this->throwInvalidException( 'gender' );
        }

        // Check if the firstName is valid
        if ( preg_match( $nameRegex, $data['firstName'], $matches ) !== 1 ) {
            $this->throwInvalidException( 'firstName' );
        }

        // Check if the insertion is valid
        if ( preg_match( $nameRegex, $data['insertion'], $matches ) !== 1 ) {
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
        $regex = '/^[0-9()\+\-\s]+$/'; // Check if there are numbers and a few special characters in the number.

        return (bool) ( preg_match( $regex, $phone, $matches ) !== 1 );
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
        throw new \InvalidArgumentException( ucfirst( $key ) . "=> " . $msg, 1 );
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
<?php
/**
 * @file Etc/User.php
 * This file provides a class to represent a user.
 *
 */
namespace JackBradford\ActionRouter\Etc;

use Cartalyst\Sentinel\Native\Facades\Sentinel;
use Cartalyst\Sentinel\Activations\EloquentActivation as EloquentActivation;
use Cartalyst\Sentinel\Users\EloquentUser;

class User {

    private $sentinelUser;

    /**
     * @method User::__construct
     * This implementation depends on the Sentinel library.
     *
     * @param EloquentUser $user
     * An instance of Sentinel's EloquentUser class.
     *
     * @return User
     */
    public function __construct(EloquentUser $user) {

        $this->sentinelUser = $user;
    }

    /**
     * @method User::getActivation
     * Create an activation record for the user if the user has not already
     * been activated. Otherwise, get the existing activation record.
     *
     * @return Activation
     */
    public function getActivation() {

        $user = $this->sentinelUser;

        if (!$activation = EloquentActivation::exists($user)) {
    
            $activation = EloquentActivation::create($user);
        }

        return new Activation([

            'code' => $activation->code,
            'userId' => $activation->user_id,
            'createdAt' => $activation->created_at,
            'updatedAt' => $activation->updated_at,
            'id' => $activation->id,
        ]);
    }

    /**
     * @method User::getDetails
     * Get the current user's details.
     *
     * @return stdClass
     */
    public function getDetails() {

        return (object) [
            
            'id' =>  $this->sentinelUser->id,
            'email' =>  $this->sentinelUser->email,
            'permissions' => $this->sentinelUser->permissions,
            'lastLogin' => $this->sentinelUser->last_login,
            'firstName' => $this->sentinelUser->first_name,
            'lastName' => $this->sentinelUser->last_name,
            'createdAt' => $this->sentinelUser->created_at,
            'updatedAt' => $this->sentinelUser->updated_at,
        ];
    }

    /**
     * @method User::getFullName()
     * Get the current user's full name.
     *
     * @return str
     */
    public function getFullName() {

        $fn = $this->sentinelUser->first_name;
        $ln = $this->sentinelUser->last_name;
        return $fn . ' ' . $ln;
    }

    /**
     * @method User::completeActivation
     * Complete the activation process for the user via an activation code.
     *
     * @param str $code
     * The activation code. This is given when the activation record is 
     * created.
     * 
     * @return void
     * Returns if the user is activated, even if the user's activation
     * was already complete. Throws an exception if the user remains
     * unactivated.
     */
    public function completeActivation($code) {

        $user = $this->sentinelUser;

        if (EloquentActivation::completed($user)) return;

        if (!EloquentActivation::exists($user)) {

            throw new Exception(
                __METHOD__.': No activation record exists for this user.'
            );
        }

        if (!EloquentActivation::complete($user, $code)) {

            throw new Exception(
                __METHOD__.': Activation failed. Sentinel returned false.'
            );
        }
    }

    /**
     * @method User::deactivate
     * Deactivate a user. This will bar access to the user without deleting
     * the user from the system.
     *
     * @return void
     * Throws an exception if the user's activation cannot be removed.
     */
    public function deactivate() {

        $user = $this->sentinelUser;

        if (EloquentActivation::remove($user) !== true) {

            throw new Exception(
                __METHOD__.': Could not remove activation record for user.'
            );
        }
    }

    /**
     * @method User::hasAccess()
     * Check whether the user has been granted a set of permissions.
     *
     * @param $permissions  TODO specify type
     * The set of permissions to check.
     *
     * @return bool
     */
    public function hasAccess($permissions) {

        return ($this->sentinelUser->hasAccess($permissions)) ? true : false;
    }

    /**
     * @method User::update
     * Update the record for the user. Change the user's email, first name,
     * and/or last name.
     *
     * @param array $credentials
     * 'email':         Include this key to change the user's email.
     * 'first_name':    Include this key to change the user's first name.
     * 'last_name':     Include this key to change the user's last name.
     *
     * @return void
     * Throws an exception on error.
     */
    public function update(array $credentials) {

        if (!Sentinel::update($this->sentinelUser, $credentials)) {

            throw new Exception(
                __METHOD__.': Could not update user.'
            );
        }

        $creds = [];
        $updates = [
            'email' => $email,
            'first_name' => $fname,
            'last_name' => $lname,
        ];

        foreach ($updates as $key => $value) {

            if (!is_null($value)) $creds[$key] = $value;
        }

    }
}


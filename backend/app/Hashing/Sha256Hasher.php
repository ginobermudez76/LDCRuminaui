<?php

namespace App\Hashing;

use Illuminate\Contracts\Hashing\Hasher;

class Sha256Hasher implements Hasher
{
    /**
     * Get information about the given hashed value.
     *
     * @param  string  $hashedValue
     * @return array
     */
    public function info($hashedValue)
    {
        return [
            'algo' => strlen($hashedValue) === 64 ? 'sha256' : 'bcrypt',
            'algoName' => strlen($hashedValue) === 64 ? 'SHA-256' : 'Bcrypt',
            'options' => [],
        ];
    }

    /**
     * Hash the given value.
     *
     * @param  string  $value
     * @param  array  $options
     * @return string
     */
    public function make(#[\SensitiveParameter] $value, array $options = [])
    {
        return hash('sha256', $value);
    }

    /**
     * Check the given plain value against a hash.
     *
     * @param  string  $value
     * @param  string  $hashedValue
     * @param  array  $options
     * @return bool
     */
    public function check(#[\SensitiveParameter] $value, $hashedValue, array $options = [])
    {
        if (strlen($hashedValue) === 64) {
            return hash('sha256', $value) === $hashedValue;
        }

        // Fallback to bcrypt if it matches standard bcrypt prefix
        if (str_starts_with($hashedValue, '$2y$')) {
            return password_verify($value, $hashedValue);
        }

        return hash('sha256', $value) === $hashedValue;
    }

    /**
     * Check if the given hash has been hashed using the given options.
     *
     * @param  string  $hashedValue
     * @param  array  $options
     * @return bool
     */
    public function needsRehash($hashedValue, array $options = [])
    {
        return false;
    }
}

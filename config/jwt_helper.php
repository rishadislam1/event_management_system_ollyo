<?php

class Token
{
    // Helper function to encode Base64 URL-safe (without padding)
    static function base64url_encode($data)
    {
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($data));
    }

    // Helper function to decode Base64 URL-safe
    static function base64url_decode($data)
    {
        $data = str_replace(['-', '_'], ['+', '/'], $data);
        $padding = strlen($data) % 4;
        if ($padding) {
            $data .= str_repeat('=', 4 - $padding);
        }
        return base64_decode($data);
    }

    static function Sign($payload, $key, $expire = null)
    {
        // Header
        $headers = ['alg' => 'HS256', 'typ' => 'JWT'];
        if ($expire) {
            $payload['exp'] = time() + $expire; // Set expiration in payload
        }

        // Encode header and payload
        $headers_encoded = self::base64url_encode(json_encode($headers));
        $payload_encoded = self::base64url_encode(json_encode($payload));

        // Generate signature
        $signature = hash_hmac('SHA256', "$headers_encoded.$payload_encoded", $key, true);
        $signature_encoded = self::base64url_encode($signature);

        // Return JWT token
        return "$headers_encoded.$payload_encoded.$signature_encoded";
    }

    static function Verify($token, $key)
    {
        // Clean up token (remove extra quotes if present)
        $token = trim($token, "\"");

        // Split token into 3 parts
        $token_parts = explode('.', $token);
        if (count($token_parts) !== 3) {
            return false; // Invalid token format
        }

        list($headers_encoded, $payload_encoded, $signature_encoded) = $token_parts;

        // Recalculate signature using same method as in `Sign`
        $expected_signature = self::base64url_encode(
            hash_hmac('SHA256', "$headers_encoded.$payload_encoded", $key, true)
        );

        // Compare signatures securely
        if (!hash_equals($expected_signature, $signature_encoded)) {
            return false; // Signature mismatch
        }

        // Decode payload
        $payload = json_decode(self::base64url_decode($payload_encoded), true);
        if (!$payload) {
            return false; // Invalid payload
        }

        // Check expiration
        if (isset($payload['exp']) && $payload['exp'] < time()) {
            return false; // Token expired
        }

        return $payload; // Token is valid
    }
}
<?php
/**
 * JWT Handler for Pandemic Resilience System (PRS)
 * This file handles JWT token generation and validation
 */

// Setting a secure secret key for signing JWT tokens
define('JWT_SECRET', 'your_secure_secret_key_for_prs_system');
define('JWT_EXPIRY', 3600); // Token expiry time in seconds (1 hour)

/**
 * Creating a JWT token for the user ID
 * 
 * @param int $user_id The user ID to include in the token
 * @return string The generated JWT token
 */
function createJWT($user_id) {
    // Creating JWT header
    $header = [
        'alg' => 'HS256',
        'typ' => 'JWT'
    ];
    
    // Creating JWT payload
    $payload = [
        'user_id' => $user_id,
        'iat' => time(), // Issued at time
        'exp' => time() + JWT_EXPIRY // Expiration time
    ];
    
    // Encoding the Header
    $header_encoded = base64UrlEncode(json_encode($header));
    
    // Encoding the Payload
    $payload_encoded = base64UrlEncode(json_encode($payload));
    
    // Creating a Signature
    $signature = hash_hmac('sha256', "$header_encoded.$payload_encoded", JWT_SECRET, true);
    $signature_encoded = base64UrlEncode($signature);
    
    // Creating JWT Token
    $jwt = "$header_encoded.$payload_encoded.$signature_encoded";
    
    return $jwt;
}

/**
 * Validating a JWT token
 * 
 * @param string $token The JWT token to validate (usually from Authorization header)
 * @return bool|int Returns user_id if valid, false otherwise
 */
function validateJWT($token) {
    // Removing 'Bearer ' prefix if present
    if (strpos($token, 'Bearer ') === 0) {
        $token = substr($token, 7);
    }
    
    // Splitting the token into parts
    $parts = explode('.', $token);
    if (count($parts) !== 3) {
        return false; // Invalid token format
    }
    
    list($header_encoded, $payload_encoded, $signature_encoded) = $parts;
    
    // Verifying signature
    $signature = base64UrlDecode($signature_encoded);
    $expected_signature = hash_hmac('sha256', "$header_encoded.$payload_encoded", JWT_SECRET, true);
    
    if (!hash_equals($signature, $expected_signature)) {
        return false; // Invalid signature
    }
    
    // Checking payload
    $payload = json_decode(base64UrlDecode($payload_encoded), true);
    
    // Verifying token expiration
    if (!isset($payload['exp']) || $payload['exp'] < time()) {
        return false; // Token expired
    }
    
    // Returning user ID from token
    return $payload['user_id'];
}

/**
 * Checking if request is authenticated and returns user ID
 * 
 * @return int|false User ID if authenticated, false otherwise
 */
function getAuthenticatedUser() {
    $headers = getallheaders();
    
    if (!isset($headers['Authorization'])) {
        return false;
    }
    
    return validateJWT($headers['Authorization']);
}

/**
 * Base64Url encoding a string
 * 
 * @param string $data The data to encode
 * @return string Base64Url encoded string
 */
function base64UrlEncode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

/**
 * Base64Url decoding a string
 * 
 * @param string $data The data to decode
 * @return string Decoded data
 */
function base64UrlDecode($data) {
    return base64_decode(strtr($data, '-_', '+/'));
}
?>
<?php

/**
 * Simple PHP Encryption functions
 *
 * Attempts to be as secure as possible given:
 *
 * - Key can be any string
 * - No knowledge of encryption is required
 * - Only key and raw/encrypted string is needed at each end
 * - Metadata can be anything (string, array, etc.)
 *
 * If possible, always prefer a library like https://github.com/defuse/php-encryption
 * and only use these functions if that isn't possible
 *
 * Adapted from http://stackoverflow.com/a/30239440/1562799
 */

/**
 * Encrypts a string
 *
 * @param string $key  Encryption key, also required for decryption
 * @param string $raw  Raw string to be encrypted
 * @param mixed  $meta Associated data that must be provided during decryption
 *
 * @return string Raw data encrypted with key
 */
function encrypt( $key, $plaintext, $meta = '' ) {

	// Generate valid key
	$key = hash_pbkdf2( 'sha256', $key, '', 10000, 0, true );

	// Serialize metadata
	$meta = serialize($meta);

	// Derive two subkeys from the original key
	$mac_key = hash_hmac( 'sha256', 'mac', $key, true );
	$enc_key = hash_hmac( 'sha256', 'enc', $key, true );
	$enc_key = substr( $enc_key, 0, 32 );

	// Derive a "synthetic IV" from the nonce, plaintext and metadata
	$temp = $nonce = ( 16 > 0 ? mcrypt_create_iv( 16 ) : "" );
	$temp .= hash_hmac( 'sha256', $plaintext, $mac_key, true );
	$temp .= hash_hmac( 'sha256', $meta, $mac_key, true );
	$mac = hash_hmac( 'sha256', $temp, $mac_key, true );
	$siv = substr( $mac, 0, 16 );

	// Encrypt the message
	$enc = mcrypt_encrypt( 'rijndael-128', $enc_key, $plaintext, 'ctr', $siv );
	return base64_encode( $siv . $nonce . $enc );

}

/**
 * Decrypts an encrypted string
 *
 * @param string $key       Encryption key, also used during encryption
 * @param string $encrypted Encrypted string to be decrypted
 * @param mixed  $meta      Associated data that must be the same as when encrypted
 *
 * @return string Decrypted string or `null` if key/meta has been tampered with
 */
function decrypt( $key, $ciphertext, $meta = '' ) {

	// Generate valid key
	$key = hash_pbkdf2( 'sha256', $key, '', 10000, 0, true );

	// Serialize metadata
	$meta = serialize($meta);

	// Derive two subkeys from the original key
	$mac_key = hash_hmac( 'sha256', 'mac', $key, true );
	$enc_key = hash_hmac( 'sha256', 'enc', $key, true );
	$enc_key = substr( $enc_key, 0, 32 );

	// Unpack MAC, nonce and encrypted message from the ciphertext
	$enc = base64_decode( $ciphertext );
	$siv = substr( $enc, 0, 16 );
	$nonce = substr( $enc, 16, 16 );
	$enc = substr( $enc, 16 + 16 );

	// Decrypt message
	$plaintext = mcrypt_decrypt( 'rijndael-128', $enc_key, $enc, 'ctr', $siv );

	// Verify MAC, return null if message is invalid
	$temp = $nonce;
	$temp .= hash_hmac( 'sha256', $plaintext, $mac_key, true );
	$temp .= hash_hmac( 'sha256', $meta, $mac_key, true );
	$mac = hash_hmac( 'sha256', $temp, $mac_key, true );
	if ( $siv !== substr( $mac, 0, 16 ) ) return null;

	return $plaintext;

}

/**
 * Encrypts a string
 *
 * Do not use this function, it is only here for historical reference
 *
 * @param string $key Encryption key, also required for decryption
 * @param string $raw Raw string to be encrypted
 *
 * @return string Raw data encrypted with key
 */
// function encrypt($key, $raw) {
// 	return base64_encode(mcrypt_encrypt(
// 			MCRYPT_RIJNDAEL_256,
// 			md5($key),
// 			$raw,
// 			MCRYPT_MODE_CBC,
// 			md5(md5($key))
// 	));
// }

/**
 * Decrypts an encrypted string
 *
 * Do not use this function, it is only here for historical reference
 *
 * @param string $key       Encryption key, also used during encryption
 * @param string $encrypted Encrypted string to be decrypted
 *
 * @return string Decrypted string or `null` if key/meta has been tampered with
 */
// function decrypt($key, $encrypted) {
// 	return rtrim(
// 		mcrypt_decrypt(
// 			MCRYPT_RIJNDAEL_256,
// 			md5($key),
// 			base64_decode($encrypted),
// 			MCRYPT_MODE_CBC,
// 			md5(md5($key))
// 		)
// 	);
// }
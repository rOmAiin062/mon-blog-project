<?php
/**
 * Created by PhpStorm.
 * User: letaaz
 * Date: 01/02/19
 * Time: 18:28
 */

namespace App\Encryptor;


use Ambta\DoctrineEncryptBundle\Encryptors\EncryptorInterface;

class CustomEncryptor implements EncryptorInterface
{
    /**
     * @var string
     */
    private $secretKey;

    /**
     * @var string
     */
    private $initializationVector;

    /**
     * Must accept secret key for encryption
     * @param string $secretKey the encryption key
     */
    public function __construct($secretKey)
    {
        $this->secretKey = md5($secretKey);
        $this->initializationVector = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB), MCRYPT_DEV_RANDOM);
    }

    /**
     * @param string $data Plain text to encrypt
     * @return string Encrypted text
     */
    public function encrypt($data)
    {
        if(is_string($data)) {
            return trim(base64_encode(mcrypt_encrypt(
                    MCRYPT_RIJNDAEL_128,
                    $this->secretKey,
                    $data,
                    MCRYPT_MODE_ECB,
                    $this->initializationVector
                ))). "<ENC>";
        }

        return $data;
    }

    /**
     * @param string $data Encrypted text
     * @return string Plain text
     */
    public function decrypt($data)
    {
        if(is_string($data)) {
            return trim(mcrypt_decrypt(
                MCRYPT_RIJNDAEL_128,
                $this->secretKey,
                base64_decode($data),
                MCRYPT_MODE_ECB,
                $this->initializationVector
            ));
        }

        return $data;
    }
}
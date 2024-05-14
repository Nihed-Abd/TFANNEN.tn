<?php 
namespace App\Service;


class JwtServicee

{


    /**
     * @param array $header
     * @param array $playload
     * @param array $secret
     * @param array $validity
     * @return string
     */

    public function generate(array $header, array $playload,string $secret, int $validity=10800): string
     {
            if($validity >0){
                $now = new \DateTimeImmutable();
                $exp = $now->getTimestamp()+$validity;
                $playload['iat']=$now->getTimestamp();
                $playload['exp']=$exp;
            }

        


        $base64Header=base64_encode(json_encode($header));
        $base64Playload=base64_encode(json_encode($playload));
        
        
        $baseHeader=str_replace(['+', '/', '='], ['-', '_', ''], $base64Header);
        $base64Playload=str_replace(['+', '/', '='],['-', '_', ''], $base64Playload);


        $secret = base64_encode($secret);

        $signature = hash_hmac('sha256',$base64Header . '.' . $base64Playload,$secret,true);
        $base64Signature= base64_encode($signature);
        $base64Signature=str_replace(['+','/','='],['-','_',''],$base64Header);
        $jwt= $base64Header . '.' . $base64Playload . '.' . $base64Signature;
        return $jwt;


     }


     public function isValid(string $token): bool
     {
        return preg_match(
           '/^[a-zA-Z0-9\-\_\=]+\.[a-zA-Z0-9\-\_\=]+\.[a-zA-Z0-9\-\_\=]+$/',
           $token 
        ) ==1;
     }

     public function getPayload(string $token)
     {
        $array = explode('.', $token);

        $payload = json_decode(base64_decode($array[1]), true);

        return $payload;
     }
     public function getHeader(string $token)
     {
        $array = explode('.', $token);

        $header = json_decode(base64_decode($array[0]), true);

        return $header;
     }

     public function isExpired(string $token): bool
     {
        $payload=$this->getPayload($token);

        $now = new \DateTimeImmutable();

        return $payload['exp'] < $now->getTimestamp();


     }


     public function check(string $token, string $secret)
     {
        $header= $this->getHeader($token);
        $payload= $this->getPayload($token);


        $verifToken = $this->generate($header, $payload, $secret, 0);

        return $token === $verifToken;
     }
     
}
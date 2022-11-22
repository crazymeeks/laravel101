<?php

namespace Tests\Feature\Controllers\Api;

use Mockery;
use Tests\TestCase;
use App\Models\User;
use App\Authenticator\TwoFA;


class SigninControllerTest extends TestCase
{

    

    public function testShouldReturn2FAQRLinkOnFirstSignin()
    {
        User::factory()->create();

        $this->mockTwoFAQRGenerator();
        $request = [
            'email' => 'admin@email.com',
            'password' => 'password'
        ];
        $response = $this->json('POST', route('api.v1.post.signin'), $request);
        
        $user = User::first();

        $original = $response->original;
        
        $this->assertFalse($original['two_fa_enabled']);
        $this->assertEquals($original['two_fa_qr_url'], 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=otpauth%3A%2F%2Ftotp%2FGoogleAuthenticatorExample%3Achregu%3Fsecret%3DPU7S6QDOGJQN7Z2Z%26issuer%3DGoogleAuthenticatorExample&ecc=M');
        $this->assertEquals($original['message'], 'Please scan qr using your 2FA app from your mobile device.');
        $this->assertNotNull($user->two_fa_secret);
        $this->assertNull($user->two_fa_enabled_at);
        $this->assertArrayHasKey('access_token', $original);

    }

    public function testShouldAskUserFor2FACodeWhen2FAisAlreadyEnabled()
    {
        

        $original = $this->signInWithEnabled2FA()->original;
        
        $this->assertTrue($original['two_fa_enabled']);
        $this->assertNull($original['two_fa_qr_url']);
        $this->assertEquals($original['message'], 'Please enter your 2FA code.');
        $this->assertArrayHasKey('access_token', $original);
    }



    public function testShouldCheckIfEntered2FACodeIsValid()
    {

        $original = $this->signInWithEnabled2FA()->original;
        $this->mockAuthenticator();
        $request = [
            'code' => '1234'
        ];
        
        $headers = [
            'Authorization' => sprintf('Bearer %s', $original['access_token'])
        ];
        
        $response = $this->json('POST', route('api.v1.post.verify.2fa'), $request, $headers);

        $this->assertSame('Bearer', $response->original['type']);
        $this->assertArrayHasKey('access_token', $response->original);
        $user = User::first();
        $this->assertNotNull($user->two_fa_enabled_at);
    }

    

    protected function mockTwoFAQRGenerator()
    {
        $this->twoFa->shouldReceive('getQrCode')
                    ->with('admin@email.com')
                    ->andReturn(
                        'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=otpauth%3A%2F%2Ftotp%2FGoogleAuthenticatorExample%3Achregu%3Fsecret%3DPU7S6QDOGJQN7Z2Z%26issuer%3DGoogleAuthenticatorExample&ecc=M'
                    );
        $this->twoFa->shouldReceive('getSecrect')
                    ->andReturn(encrypt('secret'));
        
        $this->app->bind(TwoFA::class, function(){
            return $this->twoFa;
        });
    }
}
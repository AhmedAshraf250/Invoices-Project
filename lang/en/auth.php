<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    'failed' => 'These credentials do not match our records.',
    'password' => 'The provided password is incorrect.',
    'throttle' => 'Too many login attempts. Please try again in :seconds seconds.',

    'titles' => [
        'login' => 'Login',
        'register' => 'Register',
        'forgot_password' => 'Forgot Password',
        'reset_password' => 'Reset Password',
        'confirm_password' => 'Confirm Password',
        'verify_email' => 'Verify Email',
        'two_factor' => 'Two-Factor Challenge',
    ],

    'headings' => [
        'welcome_back' => 'Welcome back',
        'sign_in_to_continue' => 'Sign in to continue.',
        'create_account' => 'Create account',
        'register_subtitle' => 'Create your new account.',
        'forgot_password' => 'Forgot password?',
        'reset_password' => 'Reset your password',
        'confirm_password' => 'Confirm your password',
        'verify_email' => 'Verify your email',
        'two_factor' => 'Two-factor authentication',
    ],

    'fields' => [
        'name' => 'Name',
        'email' => 'Email',
        'password' => 'Password',
        'password_confirmation' => 'Confirm password',
        'remember' => 'Remember me',
        'two_factor_code' => 'Authentication code',
        'recovery_code' => 'Recovery code',
    ],

    'placeholders' => [
        'email' => 'Enter your email',
        'password' => 'Enter your password',
        'password_confirmation' => 'Confirm your password',
    ],

    'actions' => [
        'login' => 'Sign in',
        'create_account' => 'Create account',
        'send_reset_link' => 'Send reset link',
        'reset_password' => 'Reset password',
        'confirm_password' => 'Confirm password',
        'resend_verification' => 'Resend verification email',
        'verify' => 'Verify',
        'logout' => 'Log out',
    ],

    'links' => [
        'forgot_password' => 'Forgot your password?',
        'no_account' => 'Don\'t have an account?',
        'already_have_account' => 'Already have an account?',
        'remember_password' => 'Remembered your password?',
    ],

    'messages' => [
        'forgot_password_description' => 'Enter your email and we will send you a reset link.',
        'reset_password_description' => 'Please choose a new secure password.',
        'confirm_password_description' => 'Please confirm your password before continuing.',
        'verify_email_description' => 'Please verify your email address using the link sent to your inbox.',
        'verification_sent' => 'A new verification link has been sent to your email address.',
        'two_factor_description' => 'Enter your authentication code or one of your recovery codes.',
        'or' => 'or',
    ],

];

<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Customer extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'wallet_id',
        'account_number',
        'phone',
        'password',
        'fullname',
        'first_name',
        'last_name',
        'email',
        'country',
        'deriv_account',
        'deriv_token',
        'deriv_email',
        'deriv_login_id',
        'deriv_account_number',
        'deriv_user_id',
        'deriv_verified',
        'deriv_verification_date',
        'deriv_last_sync',
        'landing_company_name',
        'landing_company_fullname',
        'is_virtual',
        'date_of_birth',
        'place_of_birth',
        'address_line_1',
        'address_line_2',
        'address_city',
        'address_state',
        'address_postcode',
        'has_secret_answer',
        'email_consent',
        'tax_identification_number',
        'tax_residence',
        'scopes',
        'account_list',
        'agent'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'deriv_verified' => 'boolean',
        'agent' => 'boolean',
        'is_virtual' => 'boolean',
        'has_secret_answer' => 'boolean',
        'email_consent' => 'boolean',
        'deriv_verification_date' => 'datetime',
        'deriv_last_sync' => 'datetime',
        'date_of_birth' => 'date',
        'scopes' => 'array',
        'account_list' => 'array'
    ];
}

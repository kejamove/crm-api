<?php
namespace App\Enums;

enum LeadSource: string
{
    case web = 'web';
    case referral = 'referral';
    case offline_marketing = 'offline_marketing';
    case social_media = 'social_media';
    case repeat_client = 'repeat_client';
}

<?php
namespace App\Enums;

enum RoleEnum: string
{
    case new_lead = 'new_lead';
    case contacted = 'contacted';
    case survey_scheduled = 'survey_scheduled';
    case quote_sent = 'quote_sent';
    case negotiations_started = 'negotiations_started';
    case won = 'won';
    case lost = 'lost';
}


?>
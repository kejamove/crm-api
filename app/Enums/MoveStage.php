<?php
namespace App\Enums;

enum MoveStage: string
{
    case new_lead = 'new_lead';
    case contacted = 'contacted';
    case proposal = 'proposal';
    case negotions_started = 'negotions_started';
    case won = 'won';
    case lost = 'lost';
}

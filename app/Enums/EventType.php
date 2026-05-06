<?php 

namespace App\Enums;

enum EventType: string
{
    case ARRIVED_HOME = 'ARRIVED_HOME';
    case DEPARTED_HOME = 'DEPARTED_HOME';
    case BOARDED_BUS = 'BOARDED_BUS';
    case ARRIVED_SCHOOL = 'ARRIVED_SCHOOL';
    case LEFT_SCHOOL = 'LEFT_SCHOOL';
    case CHECKED_IN_SPORTS = 'CHECKED_IN_SPORTS';
    case BUS_DELAY = 'BUS_DELAY';
    case UNKNOWN = 'UNKNOWN';
};
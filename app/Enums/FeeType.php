<?php

namespace App\Enums;

enum FeeType: string
{
    case Semester = 'semester';
    case Hostel = 'hostel';
    case LibraryFine = 'library_fine';
}

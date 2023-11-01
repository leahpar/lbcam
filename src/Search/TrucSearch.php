<?php

namespace App\Search;

use App\Entity\User;

class TrucSearch extends SearchableEntitySearch
{
    public ?string $search = null;
    public ?string $tag = null;

    public ?User $user = null;
}

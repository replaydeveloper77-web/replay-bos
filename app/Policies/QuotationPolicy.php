<?php

namespace App\Policies;

use App\Models\Quotation;
use App\Models\User;

class QuotationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('quotation.view');
    }

    public function view(User $user): bool
    {
        return $user->hasPermission('quotation.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('quotation.create');
    }

    public function update(User $user, Quotation $quotation): bool
    {
        if ($user->hasPermission('quotation.manage')) {
            return true;
        }

        return $user->hasPermission('quotation.create') && $quotation->created_by === $user->id;
    }
}

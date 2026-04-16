<?php

namespace App\Policies;

use App\Models\Report;
use App\Models\User;

class ReportPolicy
{
    public function view(User $user, Report $report): bool
    {
        return $user->id === $report->created_by || $user->hasRole('admin');
    }

    public function update(User $user, Report $report): bool
    {
        return $user->id === $report->created_by || $user->hasRole('admin');
    }

    public function delete(User $user, Report $report): bool
    {
        return $user->id === $report->created_by || $user->hasRole('admin');
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function viewAny(User $user): bool
    {
        return true;
    }
}

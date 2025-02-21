<?php

namespace App\Policies;

use App\Models\Timesheet;
use App\Models\User;

class TimesheetPolicy
{
    /**
     * Determine whether the user can view the timesheet.
     */
    public function view(User $user, Timesheet $timesheet): bool
    {
        return $user->id === $timesheet->user_id;
    }

    /**
     * Determine whether the user can update the timesheet.
     */
    public function update(User $user, Timesheet $timesheet): bool
    {
        return $user->id === $timesheet->user_id;
    }

    /**
     * Determine whether the user can delete the timesheet.
     */
    public function delete(User $user, Timesheet $timesheet): bool
    {
        return $user->id === $timesheet->user_id;
    }
}

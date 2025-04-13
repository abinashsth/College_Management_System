<?php

namespace App\Policies;

use App\Models\Mark;
use App\Models\User;
use App\Models\Exam;
use App\Models\Subject;
use Illuminate\Auth\Access\HandlesAuthorization;

class MarkPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any marks.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('view marks');
    }

    /**
     * Determine whether the user can view specific exam-subject marks.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Exam  $exam
     * @param  \App\Models\Subject  $subject
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewMarks(User $user, Exam $exam, Subject $subject)
    {
        // Any user with view marks permission can view marks
        if ($user->hasPermissionTo('view marks')) {
            return true;
        }
        
        // Teachers can view marks for subjects they teach
        if ($user->hasRole('Teacher') && $subject->teachers->contains($user->id)) {
            return true;
        }
        
        // Students can only view their own published marks
        if ($user->hasRole('Student')) {
            $student = $user->student;
            if ($student && $exam->is_published) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Determine whether the user can create marks.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Exam  $exam
     * @param  \App\Models\Subject  $subject
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function createMarks(User $user, Exam $exam, Subject $subject)
    {
        // Admins and users with create marks permission can create marks
        if ($user->hasPermissionTo('create marks')) {
            return true;
        }
        
        // Teachers can only create marks for subjects they teach
        if ($user->hasRole('Teacher')) {
            return $subject->teachers->contains($user->id);
        }
        
        return false;
    }

    /**
     * Determine whether the user can update marks.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Mark  $mark
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Mark $mark)
    {
        // Cannot update published marks
        if ($mark->status === 'published') {
            return false;
        }
        
        // Admins can update any mark that's not published
        if ($user->hasPermissionTo('edit marks')) {
            return true;
        }
        
        // Teachers can only update marks they created or for subjects they teach
        if ($user->hasRole('Teacher')) {
            // Check if mark was created by this teacher
            if ($mark->created_by === $user->id) {
                return true;
            }
            
            // Check if teacher teaches this subject
            return $mark->subject->teachers->contains($user->id);
        }
        
        return false;
    }

    /**
     * Determine whether the user can delete marks.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Mark  $mark
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Mark $mark)
    {
        // Cannot delete published marks
        if ($mark->status === 'published') {
            return false;
        }
        
        // Admins can delete any mark that's not published
        if ($user->hasPermissionTo('delete marks')) {
            return true;
        }
        
        // Teachers can only delete marks they created
        if ($user->hasRole('Teacher') && $mark->created_by === $user->id) {
            return true;
        }
        
        return false;
    }

    /**
     * Determine whether the user can verify marks.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Mark  $mark
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function verify(User $user, Mark $mark)
    {
        // Mark must be in submitted status to be verified
        if ($mark->status !== 'submitted') {
            return false;
        }
        
        // Users with verify marks permission can verify marks
        if ($user->hasPermissionTo('verify marks')) {
            return true;
        }
        
        // Department heads can verify marks for their department
        if ($user->hasRole('Department Head')) {
            return $mark->subject->department->head_id === $user->id;
        }
        
        return false;
    }

    /**
     * Determine whether the user can publish marks.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Mark  $mark
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function publish(User $user, Mark $mark)
    {
        // Mark must be in verified status to be published
        if ($mark->status !== 'verified') {
            return false;
        }
        
        // Only users with publish marks permission can publish marks
        return $user->hasPermissionTo('publish marks');
    }

    /**
     * Determine whether the user can import marks.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Exam  $exam
     * @param  \App\Models\Subject  $subject
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function importMarks(User $user, Exam $exam, Subject $subject)
    {
        // Admins and users with create marks permission can import marks
        if ($user->hasPermissionTo('create marks')) {
            return true;
        }
        
        // Teachers can only import marks for subjects they teach
        if ($user->hasRole('Teacher')) {
            return $subject->teachers->contains($user->id);
        }
        
        return false;
    }

    /**
     * Determine whether the user can export marks.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Exam  $exam
     * @param  \App\Models\Subject  $subject
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function exportMarks(User $user, Exam $exam, Subject $subject)
    {
        // Admins and users with view marks permission can export marks
        if ($user->hasPermissionTo('view marks')) {
            return true;
        }
        
        // Teachers can export marks for subjects they teach
        if ($user->hasRole('Teacher')) {
            return $subject->teachers->contains($user->id);
        }
        
        return false;
    }
} 
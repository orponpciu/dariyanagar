<?php

namespace App\Policies;

use App\Models\NewsPost;
use App\Models\User;

class NewsPostPolicy
{
    private function canManage(User $user): bool
    {
        return $user->isAdmin() || $user->isEditor();
    }

    public function viewAny(User $user): bool
    {
        return $this->canManage($user);
    }

    public function view(
        User $user,
        NewsPost $post
    ): bool {
        return $this->canManage($user);
    }

    public function create(User $user): bool
    {
        return $this->canManage($user);
    }

    public function update(
        User $user,
        NewsPost $post
    ): bool {
        return $this->canManage($user);
    }

    public function delete(
        User $user,
        NewsPost $post
    ): bool {
        return $this->canManage($user);
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }

    public function restore(
        User $user,
        NewsPost $post
    ): bool {
        return false;
    }

    public function restoreAny(User $user): bool
    {
        return false;
    }

    public function forceDelete(
        User $user,
        NewsPost $post
    ): bool {
        return false;
    }

    public function forceDeleteAny(User $user): bool
    {
        return false;
    }

    public function reorder(User $user): bool
    {
        return false;
    }
}

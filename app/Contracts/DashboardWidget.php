<?php

namespace App\Contracts;

interface DashboardWidget
{
    /**
     * Render the widget HTML
     *
     * @return string
     */
    public function render(): string;

    /**
     * Get widget data
     *
     * @return array
     */
    public function getData(): array;

    /**
     * Get required permissions for this widget
     *
     * @return array
     */
    public function getPermissions(): array;

    /**
     * Get cache key for this widget
     *
     * @return string
     */
    public function getCacheKey(): string;

    /**
     * Get widget configuration
     *
     * @return array
     */
    public function getConfig(): array;

    /**
     * Check if user can access this widget
     *
     * @param \App\Models\User $user
     * @return bool
     */
    public function canAccess($user): bool;
}
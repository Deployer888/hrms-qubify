<?php

namespace App\Widgets;

use App\Contracts\DashboardWidget;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;

abstract class BaseWidget implements DashboardWidget
{
    protected $user;
    protected $config;
    protected $cacheEnabled = true;
    protected $cacheTtl = 300; // 5 minutes default

    public function __construct(User $user, array $config = [])
    {
        $this->user = $user;
        $this->config = array_merge($this->getDefaultConfig(), $config);
    }

    /**
     * Render the widget HTML
     */
    public function render(): string
    {
        if (!$this->canAccess($this->user)) {
            return '';
        }

        $cacheKey = $this->getCacheKey();
        
        if ($this->cacheEnabled && Cache::has($cacheKey)) {
            $data = Cache::get($cacheKey);
        } else {
            $data = $this->getData();
            if ($this->cacheEnabled) {
                Cache::put($cacheKey, $data, $this->cacheTtl);
            }
        }

        return $this->renderView($data);
    }

    /**
     * Get widget data - to be implemented by child classes
     */
    abstract public function getData(): array;

    /**
     * Get required permissions for this widget
     */
    public function getPermissions(): array
    {
        return [];
    }

    /**
     * Get cache key for this widget
     */
    public function getCacheKey(): string
    {
        return sprintf(
            'widget_%s_user_%d_%s',
            $this->getWidgetType(),
            $this->user->id,
            md5(serialize($this->config))
        );
    }

    /**
     * Get widget configuration
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * Check if user can access this widget
     */
    public function canAccess($user): bool
    {
        $permissions = $this->getPermissions();
        
        if (empty($permissions)) {
            return true;
        }

        foreach ($permissions as $permission) {
            if ($user->can($permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get default configuration for the widget
     */
    protected function getDefaultConfig(): array
    {
        return [
            'title' => $this->getDefaultTitle(),
            'icon' => $this->getDefaultIcon(),
            'color' => $this->getDefaultColor(),
            'size' => 'medium'
        ];
    }

    /**
     * Get widget type identifier
     */
    abstract protected function getWidgetType(): string;

    /**
     * Get default widget title
     */
    abstract protected function getDefaultTitle(): string;

    /**
     * Get default widget icon
     */
    abstract protected function getDefaultIcon(): string;

    /**
     * Get default widget color
     */
    protected function getDefaultColor(): string
    {
        return 'primary';
    }

    /**
     * Render the widget view
     */
    protected function renderView(array $data): string
    {
        $viewName = $this->getViewName();
        
        if (!View::exists($viewName)) {
            return $this->renderFallbackView($data);
        }

        return View::make($viewName, array_merge($data, [
            'config' => $this->config,
            'widget' => $this
        ]))->render();
    }

    /**
     * Get the view name for this widget
     */
    protected function getViewName(): string
    {
        return 'dashboard.widgets.' . $this->getWidgetType();
    }

    /**
     * Render fallback view when main view doesn't exist
     */
    protected function renderFallbackView(array $data): string
    {
        return sprintf(
            '<div class="dashboard-widget widget-%s">
                <div class="widget-header">
                    <h3><i class="%s"></i> %s</h3>
                </div>
                <div class="widget-body">
                    <p>Widget data: %s</p>
                </div>
            </div>',
            $this->getWidgetType(),
            $this->config['icon'],
            $this->config['title'],
            json_encode($data, JSON_PRETTY_PRINT)
        );
    }

    /**
     * Clear widget cache
     */
    public function clearCache(): void
    {
        Cache::forget($this->getCacheKey());
    }

    /**
     * Disable caching for this widget instance
     */
    public function disableCache(): self
    {
        $this->cacheEnabled = false;
        return $this;
    }

    /**
     * Set cache TTL
     */
    public function setCacheTtl(int $seconds): self
    {
        $this->cacheTtl = $seconds;
        return $this;
    }
}
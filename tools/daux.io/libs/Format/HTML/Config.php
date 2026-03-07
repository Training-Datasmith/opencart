<?php

declare(strict_types=1);

namespace Todaymade\Daux\Format\HTML;

use Todaymade\Daux\BaseConfig;

class Config extends BaseConfig
{
    private function prepareGithubUrl($url): array
    {
        $url = str_replace('http://', 'https://', $url);

        return [
            'name' => 'GitHub',
            'basepath' => (str_starts_with($url, 'https://github.com/') ? '' : 'https://github.com/') . trim($url, '/'),
        ];
    }

    public function getEditOn()
    {
        if ($this->hasValue('edit_on')) {
            $editOn = $this->getValue('edit_on');
            if (is_string($editOn)) {
                return $this->prepareGithubUrl($editOn);
            }
            $editOn['basepath'] = rtrim($editOn['basepath'], '/');

            return $editOn;
        }

        if ($this->hasValue('edit_on_github')) {
            return $this->prepareGithubUrl($this->getValue('edit_on_github'));
        }

        return null;
    }

    public function hasSearch(): bool
    {
        return $this->hasValue('search') && $this->getValue('search');
    }

    public function showDateModified(): bool
    {
        return $this->hasValue('date_modified') && $this->getValue('date_modified');
    }

    public function showPreviousNextLinks()
    {
        return $this->getValue('jump_buttons', true);
    }

    public function showCodeToggle()
    {
        return $this->getValue('toggle_code', true);
    }

    public function hasAutomaticTableOfContents(): bool
    {
        return $this->hasValue('auto_toc') && $this->getValue('auto_toc');
    }

    public function hasGoogleAnalytics(): bool
    {
        return $this->hasValue('google_analytics') && $this->getValue('google_analytics');
    }

    public function getGoogleAnalyticsId()
    {
        return $this->getValue('google_analytics');
    }

    public function hasPlausibleAnalyticsDomain(): bool
    {
        return $this->hasValue('plausible_domain') && $this->getValue('plausible_domain');
    }

    public function getPlausibleAnalyticsDomain()
    {
        return $this->getValue('plausible_domain');
    }

    public function hasPiwikAnalytics(): bool
    {
        return $this->getValue('piwik_analytics') && $this->hasValue('piwik_analytics_id');
    }

    public function getPiwikAnalyticsId()
    {
        return $this->getValue('piwik_analytics_id');
    }

    public function getPiwikAnalyticsUrl()
    {
        return $this->getValue('piwik_analytics');
    }

    public function hasPoweredBy(): bool
    {
        return $this->hasValue('powered_by') && !empty($this->getValue('powered_by'));
    }

    public function getPoweredBy()
    {
        return $this->getValue('powered_by');
    }

    public function hasTwitterHandles(): bool
    {
        return $this->hasValue('twitter') && !empty($this->getValue('twitter'));
    }

    public function getTwitterHandles()
    {
        return $this->getValue('twitter');
    }

    public function hasLinks(): bool
    {
        return $this->hasValue('links') && !empty($this->getValue('links'));
    }

    public function getLinks()
    {
        return $this->getValue('links');
    }

    public function hasRepository(): bool
    {
        return $this->hasValue('repo') && !empty($this->getValue('repo'));
    }

    public function getRepository()
    {
        return $this->getValue('repo');
    }

    public function hasButtons(): bool
    {
        return $this->hasValue('buttons') && !empty($this->getValue('buttons'));
    }

    public function getButtons()
    {
        return $this->getValue('buttons');
    }

    public function hasLandingPage()
    {
        return $this->getValue('auto_landing', true);
    }

    public function hasBreadcrumbs()
    {
        return $this->getValue('breadcrumbs', true);
    }

    public function getBreadcrumbsSeparator()
    {
        return $this->getValue('breadcrumb_separator');
    }

    public function getTheme()
    {
        return $this->getValue('theme');
    }

    public function hasThemeVariant(): bool
    {
        return $this->hasValue('theme-variant') && !empty($this->getValue('theme-variant'));
    }

    public function getThemeVariant()
    {
        return $this->getValue('theme-variant');
    }
}

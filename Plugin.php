<?php

use Typecho\Plugin\PluginInterface;
use Typecho\Widget\Helper\Form;
use Utils\Helper;

/**
 * 路由助手 - Typecho 1.3
 *
 * @package RoutesHelper
 * @author 棋
 * @version 1.3.0
 * @link https://github.com/imqi1-github/routes-helper
 * @date 2026-01-17
 */
class RoutesHelper_Plugin implements PluginInterface
{
    /**
     * 激活插件
     */
    public static function activate(): void
    {
        Helper::addAction('routes-helper', 'RoutesHelper_Action');
        Helper::addPanel(4, 'RoutesHelper/panel.php', _t('路由助手'), _t('管理路由设置'), 'administrator');
    }

    /**
     * 禁用插件
     */
    public static function deactivate(): void
    {
        Helper::removeAction('routes-helper');
        Helper::removePanel(4, 'RoutesHelper/panel.php');
    }

    /**
     * 获取插件配置面板（无需配置）
     */
    public static function config(Form $form): void
    {
    }

    /**
     * 个人用户配置面板（无需配置）
     */
    public static function personalConfig(Form $form): void
    {
    }

    /**
     * 获取 Typecho 1.3 默认路由配置
     *
     * @return array
     */
    public static function getDefaultRoutes(): array
    {
        return [
            'index' => [
                'url' => '/',
                'widget' => '\\Widget\\Archive',
                'action' => 'render',
            ],
            'archive' => [
                'url' => '/blog/',
                'widget' => '\\Widget\\Archive',
                'action' => 'render',
            ],
            'do' => [
                'url' => '/action/[action:alpha]',
                'widget' => '\\Widget\\Action',
                'action' => 'action',
            ],
            'post' => [
                'url' => '/archives/[cid:digital]/',
                'widget' => '\\Widget\\Archive',
                'action' => 'render',
            ],
            'attachment' => [
                'url' => '/attachment/[cid:digital]/',
                'widget' => '\\Widget\\Archive',
                'action' => 'render',
            ],
            'category' => [
                'url' => '/category/[slug]/',
                'widget' => '\\Widget\\Archive',
                'action' => 'render',
            ],
            'tag' => [
                'url' => '/tag/[slug]/',
                'widget' => '\\Widget\\Archive',
                'action' => 'render',
            ],
            'author' => [
                'url' => '/author/[uid:digital]/',
                'widget' => '\\Widget\\Archive',
                'action' => 'render',
            ],
            'search' => [
                'url' => '/search/[keywords]/',
                'widget' => '\\Widget\\Archive',
                'action' => 'render',
            ],
            'index_page' => [
                'url' => '/page/[page:digital]/',
                'widget' => '\\Widget\\Archive',
                'action' => 'render',
            ],
            'archive_page' => [
                'url' => '/blog/page/[page:digital]/',
                'widget' => '\\Widget\\Archive',
                'action' => 'render',
            ],
            'category_page' => [
                'url' => '/category/[slug]/[page:digital]/',
                'widget' => '\\Widget\\Archive',
                'action' => 'render',
            ],
            'tag_page' => [
                'url' => '/tag/[slug]/[page:digital]/',
                'widget' => '\\Widget\\Archive',
                'action' => 'render',
            ],
            'author_page' => [
                'url' => '/author/[uid:digital]/[page:digital]/',
                'widget' => '\\Widget\\Archive',
                'action' => 'render',
            ],
            'search_page' => [
                'url' => '/search/[keywords]/[page:digital]/',
                'widget' => '\\Widget\\Archive',
                'action' => 'render',
            ],
            'archive_year' => [
                'url' => '/[year:digital:4]/',
                'widget' => '\\Widget\\Archive',
                'action' => 'render',
            ],
            'archive_month' => [
                'url' => '/[year:digital:4]/[month:digital:2]/',
                'widget' => '\\Widget\\Archive',
                'action' => 'render',
            ],
            'archive_day' => [
                'url' => '/[year:digital:4]/[month:digital:2]/[day:digital:2]/',
                'widget' => '\\Widget\\Archive',
                'action' => 'render',
            ],
            'archive_year_page' => [
                'url' => '/[year:digital:4]/page/[page:digital]/',
                'widget' => '\\Widget\\Archive',
                'action' => 'render',
            ],
            'archive_month_page' => [
                'url' => '/[year:digital:4]/[month:digital:2]/page/[page:digital]/',
                'widget' => '\\Widget\\Archive',
                'action' => 'render',
            ],
            'archive_day_page' => [
                'url' => '/[year:digital:4]/[month:digital:2]/[day:digital:2]/page/[page:digital]/',
                'widget' => '\\Widget\\Archive',
                'action' => 'render',
            ],
            'comment_page' => [
                'url' => '[permalink:string]/comment-page-[commentPage:digital]',
                'widget' => '\\Widget\\CommentPage',
                'action' => 'action',
            ],
            'feed' => [
                'url' => '/feed[feed:string:0]',
                'widget' => '\\Widget\\Feed',
                'action' => 'render',
            ],
            'feedback' => [
                'url' => '[permalink:string]/[type:alpha]',
                'widget' => '\\Widget\\Feedback',
                'action' => 'action',
            ],
            'page' => [
                'url' => '/[slug].html',
                'widget' => '\\Widget\\Archive',
                'action' => 'render',
            ],
        ];
    }
}

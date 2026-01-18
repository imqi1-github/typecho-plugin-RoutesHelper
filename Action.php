<?php

use JetBrains\PhpStorm\NoReturn;
use Typecho\Db;
use Typecho\Widget;
use Utils\Helper;
use Widget\ActionInterface;
use Widget\Notice;
use Widget\User;

/**
 * RoutesHelper Action 处理类
 */
class RoutesHelper_Action extends Widget implements ActionInterface
{
    private ?array $_default = null;
    private ?array $_restore = null;

    /**
     * 初始化路由数据
     */
    private function initRoutes(): void
    {
        if ($this->_default === null) {
            $this->_default = Helper::options()->routingTable;
            // 移除解析后的缓存（索引为0）
            if (isset($this->_default[0])) {
                unset($this->_default[0]);
            }
        }

        if ($this->_restore === null) {
            $this->_restore = RoutesHelper_Plugin::getDefaultRoutes();
            if (isset($this->_restore[0])) {
                unset($this->_restore[0]);
            }
        }
    }

    /**
     * 入口方法
     */
    #[NoReturn]
    public function action(): void
    {
        // 权限检查
        try {
            User::alloc()->pass('administrator');
        } catch (Exception) {
            Notice::alloc()->set(_t("权限不足"), NULL, 'error');
            $this->response->goBack();
        }

        // 初始化路由数据
        $this->initRoutes();

        // 根据 do 参数执行对应操作
        $do = $this->request->get('do');

        if ($do === 'restore') {
            $this->restore();
        } elseif ($do === 'edit') {
            $this->edit();
        } elseif ($do === 'backup') {
            $this->backup();
        } elseif ($do === 'recover') {
            $this->recover();
        } elseif ($do === 'delete-backup') {
            $this->deleteBackup();
        }

        $this->response->goBack();
    }

    /**
     * 恢复默认路由
     */
    private function restore(): void
    {
        if (!$this->request->isPost()) {
            return;
        }

        $modified = false;
        foreach ($this->_default as $key => $value) {
            // 只恢复系统默认路由，不处理插件新增的路由
            if (isset($this->_restore[$key]) && isset($value['url']) &&
                $this->_restore[$key]['url'] !== $value['url']) {
                Helper::removeRoute($key);
                Helper::addRoute(
                    $key,
                    $this->_restore[$key]['url'],
                    $this->_restore[$key]['widget'],
                    $this->_restore[$key]['action']
                );
                $modified = true;
            }
        }

        if ($modified) {
            Notice::alloc()->set(_t("路由已恢复为默认设置"), 'success');
        } else {
            Notice::alloc()->set(_t("当前路由已是默认设置，无需恢复"), NULL);
        }
    }

    /**
     * 编辑路由
     */
    private function edit(): void
    {
        if (!$this->request->isPost()) {
            return;
        }

        $modified = false;
        foreach ($this->_default as $key => $value) {
            // 跳过非系统路由和 do 路由
            if (!isset($this->_restore[$key]) || $key === 'do') {
                continue;
            }

            // 获取提交的新路由值
            $newUrl = $this->request->get($key, null, $exists);

            if ($exists && $newUrl !== $value['url']) {
                Helper::removeRoute($key);
                Helper::addRoute(
                    $key,
                    $newUrl,
                    $value['widget'],
                    $value['action']
                );
                $modified = true;
            }
        }

        if ($modified) {
            Notice::alloc()->set(_t("路由设置已保存"), 'success');
        } else {
            Notice::alloc()->set(_t("路由未发生变化"), NULL);
        }
    }

    /**
     * 备份路由
     */
    private function backup(): void
    {
        if (!$this->request->isPost()) {
            return;
        }

        $db = Db::get();
        $name = 'RoutesHelper';

        // 序列化当前路由配置
        $backupData = json_encode($this->_default);

        // 检查是否已存在备份
        $existingBackup = $db->fetchRow(
            $db->select()->from('table.options')->where('name = ?', 'plugin:' . $name . 'bf')
        );

        if ($existingBackup) {
            // 更新现有备份
            $db->query(
                $db->update('table.options')
                    ->rows(['value' => $backupData])
                    ->where('name = ?', 'plugin:' . $name . 'bf')
            );
            Notice::alloc()->set(_t("备份更新成功"), 'success');
        } else {
            // 创建新备份
            $db->query(
                $db->insert('table.options')
                    ->rows([
                        'name' => 'plugin:' . $name . 'bf',
                        'user' => '0',
                        'value' => $backupData
                    ])
            );
            Notice::alloc()->set(_t("备份成功"), 'success');
        }
    }

    /**
     * 还原备份
     */
    private function recover(): void
    {
        if (!$this->request->isPost()) {
            return;
        }

        $db = Db::get();
        $name = 'RoutesHelper';

        // 检查是否存在备份
        $backupData = $db->fetchRow(
            $db->select()->from('table.options')->where('name = ?', 'plugin:' . $name . 'bf')
        );

        if (!$backupData) {
            Notice::alloc()->set(_t("未备份过数据，无法恢复"), 'error');
            return;
        }

        $routes = json_decode($backupData['value'], true);
        if (!is_array($routes)) {
            Notice::alloc()->set(_t("备份数据损坏，无法恢复"), 'error');
            return;
        }

        $modified = false;
        foreach ($routes as $key => $value) {
            // 移除旧路由并添加备份的路由
            Helper::removeRoute($key);
            Helper::addRoute(
                $key,
                $value['url'],
                $value['widget'],
                $value['action']
            );
            $modified = true;
        }

        if ($modified) {
            Notice::alloc()->set(_t("备份还原成功"), 'success');
        }
    }

    /**
     * 删除备份
     */
    private function deleteBackup(): void
    {
        if (!$this->request->isPost()) {
            return;
        }

        $db = Db::get();
        $name = 'RoutesHelper';

        // 检查是否存在备份
        $existingBackup = $db->fetchRow(
            $db->select()->from('table.options')->where('name = ?', 'plugin:' . $name . 'bf')
        );

        if (!$existingBackup) {
            Notice::alloc()->set(_t("没有备份内容，无法删除"), 'error');
            return;
        }

        // 删除备份
        $db->query(
            $db->delete('table.options')->where('name = ?', 'plugin:' . $name . 'bf')
        );

        Notice::alloc()->set(_t("备份删除成功"), 'success');
    }
}

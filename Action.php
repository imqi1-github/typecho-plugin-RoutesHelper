<?php

use JetBrains\PhpStorm\NoReturn;
use Typecho\Widget;
use Utils\Helper;
use Widget\ActionInterface;
use Widget\Notice;
use Widget\User;

class RoutesHelper_Action extends Widget implements ActionInterface
{
  /**
   * 数据库中的路由数据
   *
   * @access private
   * @var array
   */
  private array $_default;

  /**
   * 系统默认的路由数据
   *
   * @access private
   * @var array
   */
  private mixed $_restore;

  public function __construct($request, $response, $params = NULL)
  {
    parent::__construct($request, $response, $params);
    $this->_default = Helper::options()->routingTable;
    $this->_restore = RoutesHelper_Plugin::getDefaultRoutes();
  }

  /**
   * 绑定动作
   *
   * @access public
   * @return void
   */
  #[NoReturn]
  public function action(): void
  {
    try {
      User::alloc()->pass('administrator');
    } catch (\Typecho\Db\Exception $e) {
      Notice::alloc()->set(_t("数据库异常：%s", $e->getMessage()), NULL, 'error');
    } catch (Widget\Exception $e) {
      Notice::alloc()->set(_t("用户异常：%s", $e->getMessage()), NULL, 'error');
    }
    $this->on($this->request->is('restore'))->restore();
    $this->on($this->request->is('edit'))->edit();
    $this->response->goBack();
  }

  /**
   * 恢复系统默认的路由
   */
  public function restore(): void
  {
    $modified = false;
    if ($this->request->isPost()) {
      foreach ($this->_default as $key => $value) {
        if (array_key_exists($key, $this->_restore) && $this->_restore[$key]['url'] != $this->_default[$key]['url']) {
          Helper::removeRoute($key);
          Helper::addRoute($key, $this->_restore[$key]['url'], $this->_restore[$key]['widget'], $this->_restore[$key]['action']);
          $modified = true;
        }
      }
    }
    if ($modified) {
      Notice::alloc()->set(_t("路由变更已经保存"), 'success');
    } else {
      Notice::alloc()->set(_t("当前路由已为默认，无需恢复"), NULL);
    }
  }

  /**
   * 修改路由
   */
  public function edit(): void
  {
    $modified = false;
    if ($this->request->isPost()) {
      foreach ($this->_default as $key => $value) {
        if (array_key_exists($key, $this->_restore) && null !== $this->request->get($key, null, $exists) && $exists && $this->request->{$key} != $this->_default[$key]['url'] && $key != 'do') {
          Helper::removeRoute($key);
          Helper::addRoute($key, $this->request->{$key}, $this->_default[$key]['widget'], $this->_default[$key]['action']);
          $modified = true;
        }
      }
    }
    if ($modified) {
      Notice::alloc()->set(_t("路由变更已经保存"), NULL, 'success');
    } else {
      Notice::alloc()->set(_t("路由未变更"), NULL);
    }
  }
}

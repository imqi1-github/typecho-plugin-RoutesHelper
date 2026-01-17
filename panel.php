<?php
/**
 * RoutesHelper 后台面板
 *
 * 以下文件来源于 Typecho 后台公共组件：
 * - header.php    页面头部
 * - menu.php      侧边栏菜单
 * - copyright.php 版权信息
 * - common-js.php 通用 JS
 * - footer.php    页面尾部
 *
 * @see SOURCES.md
 */

use Utils\Helper;
use Widget\Security;

if (!defined('__TYPECHO_ROOT_DIR__')) exit;

/** @var Security $security */
?>
<?php include 'header.php'; ?>
<?php include 'menu.php'; ?>

<?php
$default = RoutesHelper_Plugin::getDefaultRoutes();
$routingTable = Helper::options()->routingTable;
if (isset($routingTable[0])) {
    unset($routingTable[0]);
}
?>

<div class="main">
  <div class="body container">
    <div class="typecho-page-title col-mb-12">
      <h2><?php _e('路由助手'); ?></h2>
    </div>
    <div class="row typecho-page-main">
      <div class="col-mb-12 col-tb-8 col-tb-offset-2">

        <!-- 恢复默认路由 -->
        <form action="<?php $security->index('/action/routes-helper?do=restore'); ?>" method="post">
          <ul class="typecho-option">
            <li>
              <label class="typecho-label"><?php _e('恢复默认路由'); ?></label>
              <button type="submit" class="primary"><?php _e('恢复系统默认路由'); ?></button>
              <p class="description">
                <?php _e('恢复为 Typecho 1.3 安装时默认的路由配置，不影响由插件增加的路由。'); ?>
              </p>
            </li>
          </ul>
        </form>

        <!-- 编辑路由 -->
        <form action="<?php $security->index('/action/routes-helper?do=edit'); ?>" method="post">
          <ul class="typecho-option">
            <li>
              <label class="typecho-label"><?php _e('路由表'); ?></label>
              <?php foreach ($routingTable as $key => $value): ?>
                <span class="multiline">
                  <input id="route-<?php echo $key; ?>"
                         name="<?php echo $key; ?>"
                         type="text"
                         class="w-60 text-s mono"
                         value="<?php echo htmlspecialchars($value['url'] ?? ''); ?>"
                         <?php
                         // 禁用插件新增的路由和 do 路由
                         if (!isset($default[$key]) || $key === 'do'): ?>
                         disabled="disabled"
                         <?php endif; ?>
                  />
                  =>
                  <label for="route-<?php echo $key; ?>" id="for-route-<?php echo $key; ?>"
                    <?php
                    // 样式：与默认不同显示红色，插件新增显示蓝色
                    if (isset($default[$key])) {
                        if (isset($value['url']) && $value['url'] !== $default[$key]['url']) {
                            echo 'style="color:red;font-weight:bold;"';
                        }
                    } else {
                        echo 'style="color:blue;font-weight:bold;"';
                    }
                    ?>>
                    [ <?php echo $key; ?> ]
                  </label>
                </span>
              <?php endforeach; ?>
              <p class="description">
                <?php _e('1. 与默认路由不同的以<strong style="color:red">红色</strong>显示，由插件增加的路由以<strong style="color:blue">蓝色</strong>显示。<br>
                2. <strong>[ do ]</strong> 为后台路由，为避免问题，本插件不允许修改。'); ?>
              </p>
            </li>
          </ul>
          <ul class="typecho-option typecho-option-submit">
            <li>
              <button type="submit" class="primary"><?php _e('保存设置'); ?></button>
            </li>
          </ul>
        </form>

      </div>
    </div>
  </div>
</div>

<?php
include 'copyright.php';
include 'common-js.php';
include 'footer.php';
?>

<?php use Utils\Helper;

if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php
include 'header.php';
include 'menu.php';
?>
<?php
$default = RoutesHelper_Plugin::getDefaultRoutes();
?>

<div class="main">
  <div class="body container">
    <div class="col-group">
      <div class="typecho-page-title col-mb-12">
        <h2>高级路由设置</h2>
      </div>
    </div>
    <div class="col-group typecho-page-main">
      <div class="col-mb-12 col-tb-8 col-tb-offset-2">
        <form action="<?php $options->index('/action/RoutesHelper?restore'); ?>" method="post"
              enctype="application/x-www-form-urlencoded">
          <ul class="typecho-option" id="routeshelper-option-item-restore-0">
            <li>
              <label class="typecho-label">路由还原</label>
              <button type="submit" class="primary">恢复系统默认路由</button>
              <p class="description">恢复为程序安装时默认的路由，不影响由插件增加的路由。<br/>当前支持Typecho版本
                1.3 版本（最后更新于2025年12月）</p>
            </li>
          </ul>
        </form>
        <?php
        $routingTable = Helper::options()->routingTable;
        if (isset($routingTable[0])) unset($routingTable[0]);
        ?>
        <form action="<?php $options->index('/action/RoutesHelper?edit'); ?>" method="post"
              enctype="application/x-www-form-urlencoded">
          <ul class="typecho-option" id="">
            <li>
              <label class="typecho-label">路由表</label>
              <?php foreach ($routingTable as $key => $value) { ?>
                <span class="multiline">
                                <input id="route-<?php echo $key; ?>" name="<?php echo $key; ?>" type="text"
                                       class="w-60 text-s mono"
                                       value="<?php echo $value['url']; ?>" <?php if (!isset($default[$key]) || 'do' == $key) echo 'disabled="disabled" '; ?>/>
                                 => <label for="route-<?php echo $key; ?>"
                                           id="for-route-<?php echo $key; ?>" <?php if (isset($default[$key])) {
                    if ($value['url'] != $default[$key]['url']) echo 'style="color:red;font-weight:bold;"';
                  } else {
                    echo 'style="color:blue;font-weight:bold;"';
                  } ?>>[ <?php echo $key; ?> ]</label>
                            </span>
              <?php } ?>
              <p class="description">1. 与默认路由不同的以红色显示，由插件增加的路由以蓝色显示。<br/>2. [ do
                ] 为后台路由，避免出现问题，本插件默认不允许修改插件路由和后台路由。</p>
            </li>
          </ul>
          <ul class="typecho-option typecho-option-submit" id="routeshelper-option-item-submit-0">
            <li>
              <button type="submit" class="primary">保存设置</button>
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

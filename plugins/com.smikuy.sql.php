<?php
pluginRegister(new class extends pluginParent
{
    const _pluginName = 'SQLPlugin';
    const _pluginAuthor = 'smikuy';
    const _pluginDescription = '这是一个前置数据库插件';
    const _pluginPackage = 'com.smikuy.sql';
    const _pluginVersion = '1.0.0';
    const _pluginFrontLib = true;
    const dbPath='../config/smikuy/db/main.sql';

    public function initializeDatabase() {
    // 检查数据库文件是否存在
    if (!file_exists('../config/smikuy/db/main.sql')) {
        // 创建一个新的SQLite数据库
        $db = new SQLite3('../config/smikuy/db/main.sql');

        // 创建admin表
        $db->exec('CREATE TABLE permissions (ID INTEGER PRIMARY KEY, permit_level INTEGER)');

        // 插入初始数据
        $db->exec('INSERT INTO permissions (ID, permit_level) VALUES (3506765106, 0)');

        // 创建黑名单表
        $db->exec('CREATE TABLE blacklist (ID INTEGER PRIMARY KEY, blacklisted_time TEXT, reason TEXT)');

        return true;
    } else {
        return true;
        }
    }

    public function __construct()
    {
        return true;
    }

    public function hasPermit($id, $requiredLevel) {
    // 如果要求的权限级别是0，直接返回true
    if ($requiredLevel === 0) {
        return true;
    }

    // 创建一个SQLite3对象来连接数据库
    $db = new SQLite3('../config/smikuy/db/main.sql');

    // 查询权限表中是否存在与传入ID匹配的记录
    $stmt = $db->prepare('SELECT permit_level FROM permissions WHERE ID = :id');
    $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
    $result = $stmt->execute();

    // 检查查询结果
    if ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        // 检查用户的权限级别是否小于或等于要求的权限级别
        if ($row['permit_level'] <= $requiredLevel) {
            return true;
        }
    }

    return false;
    }

    public function manageBlacklist($id, $action, $reason = null) {
    // 创建一个SQLite3对象来连接数据库
    $db = new SQLite3('../config/smikuy/db/main.sql');

    if ($action === 'add') {
        // 检查ID是否已在黑名单中
        $stmt = $db->prepare('SELECT ID FROM blacklist WHERE ID = :id');
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        $result = $stmt->execute();

        if ($result->fetchArray(SQLITE3_ASSOC)) {
            return "QQ $id 已存在于黑名单中。";
        } else {
            // 添加ID到黑名单
            $stmt = $db->prepare('INSERT INTO blacklist (ID, blacklisted_time, reason) VALUES (:id, :time, :reason)');
            $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
            $stmt->bindValue(':time', date('Y-m-d H:i:s'), SQLITE3_TEXT);
            $stmt->bindValue(':reason', $reason, SQLITE3_TEXT);
            $stmt->execute();
            return "QQ $id 已添加到黑名单。";
        }
    } elseif ($action === 'remove') {
        // 检查ID是否在黑名单中
        $stmt = $db->prepare('SELECT ID FROM blacklist WHERE ID = :id');
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        $result = $stmt->execute();

        if ($result->fetchArray(SQLITE3_ASSOC)) {
        // 从黑名单中移除ID
        $stmt = $db->prepare('DELETE FROM blacklist WHERE ID = :id');
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        $stmt->execute();
        return "QQ $id 已从黑名单中移除。";}else{
            return "QQ $id 不在黑名单中，无法移除。";
        }
    } else {
        return "无效的操作。";
    }
}

public function getBlacklist() {
    // 创建一个SQLite3对象来连接数据库
    $db = new SQLite3('../config/smikuy/db/main.sql');

    // 查询黑名单表中的所有条目
    $results = $db->query('SELECT ID, blacklisted_time, reason FROM blacklist');

    // 遍历结果集并打印每个黑名单条目
    $back="当前黑名单列表：\n";
    while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
        $id = $row['ID'];
        $time = $row['blacklisted_time'];
        $reason = $row['reason'] ?? '无';
        $back=$back."QQ: $id, 拉黑时间: $time, 原因: $reason\n";
    }
    return $back;
}

public function queryBlacklist($id) {
    // 创建一个SQLite3对象来连接数据库
    $db = new SQLite3('../config/smikuy/db/main.sql');

    // 查询指定ID是否在黑名单中
    $stmt = $db->prepare('SELECT ID, blacklisted_time, reason FROM blacklist WHERE ID = :id');
    $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
    $result = $stmt->execute();

    // 检查查询结果
    if ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $time = $row['blacklisted_time'];
        $reason = $row['reason'] ?? '无';
        return "QQ $id 在黑名单中，拉黑时间: $time, 原因: $reason\n";
    } else {
        return "QQ $id 不在黑名单中。\n";
    }
}
});
?>
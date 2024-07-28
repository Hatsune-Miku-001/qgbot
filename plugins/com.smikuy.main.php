<?php
/**
 * MiraiEz Copyright (c) 2021-2023 NKXingXh
 * License AGPLv3.0: GNU AGPL Version 3 <https://www.gnu.org/licenses/agpl-3.0.html>
 * This is free software: you are free to change and redistribute it.
 * There is NO WARRANTY, to the extent permitted by law.
 *
 * Github: https://github.com/nkxingxh/MiraiEz
 */

/**
 * 这个是示例插件
 *
 * 作者: smikuy
 * 邮箱: smikuy@lucloud.top
 * Github: https://github.com/Hatsune-Miku-001
 *
 */
pluginRegister(new class extends pluginParent   //建议继承 pluginParent 插件类,当框架更新导致插件类定义发生变化时, pluginParent 将能提供一定的容错能力
{
    //以下五行插件信息必须定义
    const _pluginName = "MainPlugin";                    //插件名称
    const _pluginAuthor = "smikuy";                       //插件作者
    const _pluginDescription = "主插件插件";                  //插件描述
    const _pluginPackage = "com.smikuy.main";    //插件包名 必须是唯一的 (如已加载相同包名的插件，将跳过当前插件类，不予加载)
    const _pluginVersion = "1.0.0";                         //插件版本

    //构造函数, 目前没有用到，写不写这个函数都可以
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 插件初始化函数
     * 请不要在该函数中做除 hookRegister 外的任何操作
     * 返回 false 则表示插件初始化失败, 该插件将不会在后续被调用 (即使已经使用 hookRegister 注册 消息、事件或请求等 的处理函数)
     */
    public function _init():bool
    {
        /**
         * hookRegister
         * 注册消息、事件或请求等的处理函数
         * 第一个参数 (func) 被注册的函数名称
         * 从第二个参数开始到最后一个参数 (...$types) 为消息/事件类型,
         *
         * 具体消息类型、事件类型请参阅 mirai-api-http 文档:
         * https://github.com/project-mirai/mirai-api-http/blob/master/docs/api/MessageType.md
         * https://github.com/project-mirai/mirai-api-http/blob/master/docs/api/EventType.md
         */
        hookRegister('hook', 'FriendMessage', 'GroupMessage');
        return true;
    }

    /**
     * hook 处理函数
     * 这个函数被注册了, 所以必须设置为 公共 (public) 函数
     * 否则调用时会出错
     */
    public function hook($_DATA): int
    {
        $pluginObject = plugin_loadFrontObject('com.smikuy.sql',);
        if(!$pluginObject) {
            replyMessage('前置插件加载失败', true);
            return 1;
        }
        $pluginObject->initializeDatabase();
        /**
         * $_PlainText 全局变量, 类型为 字符串 (String), 存储消息的纯文本内容，使用前需要先通过 global 声明或者通过 $GLOBALS['_PlainText'] 调用
         * $_ImageUrl 全局变量，类型为 数组 (Array), 成员类型为 字符串 (String), 存储消息中图片的链接，使用前需要先通过 global 声明或者通过 $GLOBALS['_ImageUrl'] 调用
         * $_At 全局变量，类型为 数组 (Array), 成员类型为 整型 (int), 存储消息中被 @ 用户的 QQ 号，使用前需要先通过 global 声明或者通过 $GLOBALS['_At'] 调用
         */
        global $_PlainText, $_At, $_ImageUrl;
        if ($_DATA['type'] == 'GroupMessage'){
            if ($_PlainText == "菜单"){
                replyMessage("⭐机器人菜单⭐\n⚠注意:'[]'内的是需要替换的内容,注意命令中的空格\n@机器人后跟文字/图片即可调用ChatGPT\n@机器人后跟画xxx即可调用AI绘图\n---------\n🥵舞萌功能:\n\n📄查b50/b40   /mai b50/b40\n🔍查歌   /mai 查歌 [歌曲名称]\n🔍别名查歌   /mai [歌曲别名]是什么歌\n🎼谱面详情   /mai [谱色]id[歌曲ID]\n🔍查歌曲详情   /mai info [曲名/俗称]\n🔀随机歌曲   /mai 随个[谱色][等级]\n🔍查歌曲别名   /mai [歌曲ID]有什么别名\n🔍查分数线   /mai 分数线 [谱色][歌曲id] [分数线]\n🔀随机推分金曲   /mai 随机推分金曲\n🔍牌子进度   /mai [牌名]进度\n🔍牌子完成表   /mai [牌名]完成表\n🔍等级进度   /mai [等级][达成]进度\n🔍等级完成表   /mai [等级]完成表\n🔍等级分数列表   /mai [等级]分数列表\n---------\n🌠漂流瓶功能:\n\n🌟扔漂流瓶   丢漂流瓶[漂流瓶内容(图片或文本)]\n⭐捡漂流瓶   捡漂流瓶\n📨评论漂流瓶   评论[文本](需要回复/引用你想评论的瓶子)\n🔍查看漂流瓶数量   跳海\n---------\n🎰赌博功能:\n\n🎲抽禁言   天弃之子\n🔫俄罗斯赌轮   赌\n---------\n🤖群管功能:\n\n🚫拉黑(群主命令)   拉黑[@你想拉黑的人] 原因(可选)\n🔍查黑   查黑 [被查黑QQ号]\n📄完整黑名单   黑名单\n🚫删黑(群主命令)   [被删黑QQ号]");
                return 1;
            }
            if (strpos($_PlainText,"拉黑") == 0 && strpos($_PlainText,"拉黑")!="") {
                if (!$pluginObject->hasPermit($_DATA['sender']['id'],3)) {
                    replyMessage("无操作权限\n", true, null);
                } elseif ($_At == null) {
                    replyMessage("请按照如下示例使用: 拉黑[@你想拉黑的人] 原因(可选)", true, null);
                } else {
                    $resultArray = explode(" ",$_PlainText,2);
                    if (count($resultArray) < 2) {
                        $resultArray[] = ""; // 添加空字符串作为第二部分
                    }
                    replyMessage($pluginObject->manageBlacklist($_At[0], 'add',$resultArray[1]), true, null);
                }
            return 1;
        }
        if (strpos($_PlainText,"删黑") == 0 && strpos($_PlainText,"删黑")!="") {
            $resultArray = explode(" ",$_PlainText,2);
                if (!$pluginObject->hasPermit($_DATA['sender']['id'],3)) {
                    replyMessage("无操作权限\n", true, null);
                } elseif (count($resultArray) < 2) {
                    replyMessage("请按照如下示例使用: 删黑 [被删黑QQ号]", true, null);
                } else {
                    replyMessage($pluginObject->manageBlacklist($resultArray[1], 'remove'), true, null);
                }
            return 1;
        }
        if($_PlainText == "黑名单"){
            replyMessage($pluginObject->getBlacklist(), true, null);
            return 1;
        }
        if (strpos($_PlainText,"查黑") == 0 && strpos($_PlainText,"查黑")!="") {
            $resultArray = explode(" ",$_PlainText,2);
                if (count($resultArray) < 2) {
                    replyMessage("请按照如下示例使用: 查黑 [被查黑QQ号]", true, null);
                } else {
                    replyMessage($pluginObject->queryBlacklist($resultArray[1]), true, null);
                }
            return 1;
        }
        }else{
        if ($_PlainText == "/ping") {
            replyMessage(plugin_whoami());   //使用 replyMessage 快速回复消息
            return 0;
        }

        if ($_PlainText == '/拦截') {
            replyMessage("OK");
            return 1;   //任何 hook 处理函数返回 1 (类型严格为 int), 则表示该 hook 处理函数已经处理完毕, 并且不再继续执行其他 hook 处理函数
        }

        if ($_PlainText == '/引用') {
            /**
             * 当 replyMessage 函数的 quote 参数传入 true (类型严格为 bool) 时, 则表示当前处理的消息将被引用。
             * 当然你也可以传入要引用的消息 ID, 如无需引用可传入任何 empty() 结果为 true 的值, 如: 0, '', null, false, array() 等
             */
            replyMessage("这是一条有引用的消息", true);
            return 0;
        }

        if ($_PlainText == '/at') {
            if ($_DATA['type'] == 'GroupMessage') {
                /**
                 * 当 replyMessage 函数的 at 参数传入 true (类型严格为 bool) 时, 将会在回复的消息中 @ 当前处理消息的发送者
                 * 当然你也可以传入要 @ 的单个用户 QQ 号, 如无需 @ 可传入任何 empty() 结果为 true 的值, 如: 0, '', null, false, array() 等
                 * 注意这个参数仅在支持 @ 的场景生效，例如 群消息回复
                 */
                replyMessage("我 At 你了", null, true);
            } else {
                replyMessage("只能在群消息中 At 你");
            }
            return 0;
        }

        if (trim($_PlainText) == '/ats') {
            $msg = "At 的目标有: \n";
            $n = count($_At);
            for ($i = 0; $i < $n; $i++) {
                $msg .= "QQ: " . $_At[$i] . "\n";
            }
            replyMessage($msg);
        }

        if ($_PlainText == '/image') {
            $imgUrl = 'http://q1.qlogo.cn/g?b=qq&s=640&nk=' . $_DATA['sender']['id'];   //当前处理消息的发送者头像的 URL

            /**
             * 创建消息链
             * 第一个参数 (PlainText) 为消息链中的文本消息 (字符串)
             * 第二个参数 (ImageUrl) 为消息链中图片的链接 (可以是数组)
             * 第三个参数 (AtTarget ) 为消息链中要 @ 的 QQ 号 (可以是数组)
             * 注意: 只有群消息回复才支持 @, 如果在非群消息 @, 有可能导致消息发送失败
             */
            $messageChain = getMessageChain("这是文本内容, 并且本消息包含一张图片", $imgUrl);

            //手动发送消息(链)
            if ($_DATA['type'] == 'GroupMessage') {
                sendGroupMessage($_DATA['sender']['group']['id'], $messageChain);
            } else {
                sendFriendMessage($_DATA['sender']['id'], $messageChain);
            }
        }

        /**
         * 为了方便开发与定位错误
         * 本框架有一定的日志记录功能, 可以通过 writeLog 函数记录日志
         */
        if ($_PlainText == '/log') {
            /**
             * writeLog
             * 参数一: 日志内容 (string) (必须)
             * 参数二: 日志类型/模块名称 或其他你认为有用的 (string) (可选)
             * 参数三: 日志文件名 (不包括拓展名 .log) (可选, 但是不建议留空, 因为默认日志文件为 core.log, 不便于区分)
             *
             * 该函数将会自动记录时间，当日志文件不存在时会自动创建
             */
            writeLog("这是一条日志", '这是模块名称', 'examplePlugin');
            replyMessage("请查看 logs 文件夹中的 examplePlugin.log 文件");
            return 0;
        }

        /**
         * 错误处理
         * 本框架会尝试捕获运行中发生的异常、错误, 并记录到 errorHandle.log 文件中
         *
         * 如果当前处理的消息为好友消息且发送者在 $MIRAIEZ_DEBUG_FRIENDS 中, 则会将错误信息直接发送给当前消息的发送者;
         * 如果当前处理的消息为群消息且该群号在 $MIRAIEZ_DEBUG_GROUPS 中, 则会将错误信息直接发送到当前消息的所在群
         * (通过 config.exe 来设置上述的两个变量)
         *
         * 向机器人发送 /error, 你将可以直观地看到本框架的错误处理反馈
         */
        if ($_PlainText == '/error') {
            //模拟一次错误
            $a = 1 / 0;
            return 0;
        }

        /**
         * 除了本框架定义的函数外
         * mirai-api-http 的 HTTP、WebHook 适配器支持的一切 API 命令、返回命令 都可以直接使用
         * 这意味着在本框架中没有定义的命令, 都可以通过调用 适配器 来实现
         *
         * 接下来将讲解 适配器 的用法
         */
        if ($_PlainText == '/adapter') {
            /**
             * autoAdapter
             * 自动适配器, 将会把传入的命令和内容发送到(或返回到) mirai-api-http 的对应适配器
             * 第一个参数 (command) 为命令字
             * 第二个参数 (content) 为命令内容
             * 返回 JSON 解码后的 适配器响应内容
             *
             * 注意: 当使用 autoAdapter 发送命令时, 如果 WebHook 适配器支持该命令, 将会优先使用 WebHook 适配器, 此时函数返回值将为空。
             * 请放心, 无论你如何使用 autoAdapter 函数, 它都不会重复使用 WebHook 适配器返回数据
             *
             * 使用适配器函数，数据将直接发送给 mirai-api-http 的 HTTP 或 WebHook 适配器 (autoAdapter 具有自动判断)
             * 所以, 在使用本框架时, 你还需要阅读 mirai-api-http 的开发文档, 了解其相关的命令和返回数据格式
             *
             * Tips: 在使用 适配器 函数时，不需要考虑 sessionKey 等鉴权的问题, 因为本框架已经自动处理了。所以你只需要专注于命令与其内容即可
             *
             * 这里以 获取好友资料 为例 (文档: https://github.com/project-mirai/mirai-api-http/blob/master/docs/adapter/HttpAdapter.md#获取好友资料)
             */

            //获取发送者的资料
            $resp = autoAdapter('friendProfile', array(
                'target' => $_DATA['sender']['id']
            ));
            replyMessage(
                "你的昵称: " . $resp['nickname']
                    . "\n你的等级: " . $resp['level']
            );
        }

        //上传群文件
        if ($_PlainText == '/file_upload') {
            $fileUrl = 'http://q1.qlogo.cn/g?b=qq&s=640&nk=' . $_DATA['sender']['id'];   //当前处理消息的发送者头像的 URL
            $fileName = $_DATA['sender']['id'] . '的头像_' . time() . '.jpg';
            $cFile = curl_file_create($fileUrl, null, $fileName);   //创建cURL文件对象
            $resp = file_upload($cFile);
            replyMessage("已尝试上传你的头像至群文件");
        }
        return 0;
        }
        return 0;
    }
});

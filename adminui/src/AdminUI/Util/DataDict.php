<?php
namespace AdminUI\Util;

class DataDict
{
    private static $dict = array(
        'storage_plan' => array(
            'none_2015' => '2015 - 无套餐',
            'basic_2015' => '2015 - 基础 (588元)',
            'normal_2015' => '2015 - 标准 (2888元)',
            'advanced_2015' => '2015 - 高级 (5888元)',
            'super_2015' => '2015 - 旗舰 (28888元)',
            'none' => '无套餐',
            'basic' => '经济 (588元)',
            'normal' => '标准 (1488元)',
            'advanced' => '高级 (2888元)',
            'super' => '旗舰 (28888元)',
            'custom' => '定制',
            'old' => '老套餐 (即将废弃)',
            'custom_package' => '定制套餐',
        ),
        'cashflow' => array(
            'outflow' => '支出',
            'inflow' => '收入',
        ),
        'bill_category' => array(
            'expense' => '消费',
            'recharge' => '充值',
            'sms_bill' => '短信帐单',
            'email_bill' => '邮件账单',
            'storage_bill' => '存储账单',
        ),
        'user_level' => array(
            'none' => '免费版',
            'license' => '商业授权',
            'personal' => '个人版',
            'basic' => '企业初级',
            'medium' => '企业高级',
            'advanced' => '企业旗舰',
            'gold' => '白金版',
            'custom' => '定制版',
        ),
        'user_status' => array(
            'enabled' => '未禁止',
            'disabled' => '已禁止',
            'unlocked' => '未锁定',
            'locked' => '已锁定',
        ),
        'edition' => array(
            'opensource' => '通用版',
            'k12' => '校园版',
            'mooc' => '慕课版',
        ),
        'sms_account_status' => array(
            'created' => '新建',
            'used' => '使用中',
            'stoped' => '停用',
        ),
        'sms_apply_status' => array(
            'created' => '新建',
            'auditing' => '审核中',
            'failed' => '失败',
            'passed' => '通过',
        ),
        'sms_sended_status' => array(
            'created' => '新建',
            'sended' => '发送成功',
            'failed' => '发送失败',
            'submited' => '已提交',
            'partSended' => '部分成功',
        ),
        'sms_sended_category' => array(
            'sms_bind' => '绑定用户',
            'sms_registration' => '用户注册',
            'sms_forget_password' => '找回密码',
            'sms_user_pay' => '用户支付',
            'sms_forget_pay_password' => '找回支付密码',
            'system_remind' => '系统提醒',
            'system_remind_only_data' => '系统提醒',
            'system_remind_only_owe' => '系统提醒',
            'system_remind_only_live_expire' => '系统提醒',
            'system_remind_only_try_expire' => '系统提醒',
            'system_remind_only_video_expire' => '系统提醒',
            'system_remind_only_sms_expire' => '系统提醒',
            'sms_draw_money' => '提现验证',
            'sms_realname_notify' => '实名认证提醒',
            'sms_classroom_publish' => '新班级发布',
            'sms_course_publish' => '新课程发布',
            'sms_normal_lesson_publish' => '新课时发布通知（普通课程）',
            'sms_live_lesson_publish' => '新课时发布通知（直播）',
            'sms_live_play_one_day' => '直播开播前通知（提前1天）',
            'sms_live_play_one_hour' => '直播开播前通知（提前1小时）',
            'sms_homework_check' => '作业完成批阅',
            'sms_testpaper_check' => '试卷完成批阅',
            'sms_course_buy_notify' => '课程购买',
            'sms_classroom_buy_notify' => '班级购买',
            'sms_vip_buy_notify' => '会员购买',
            'sms_coin_buy_notify' => '虚拟币充值',
            'sms_trial_verify' => '一键试用验证码',
        ),
        'email_record_status' => array(
            'failed' => '发送失败',
            'submited' => '提交成功',
            'sended' => '发送成功',
            'waited' => '等待回执'
        ),
        'email_account_status' => array(
            'created' => '新建',
            'enable' => '使用中',
            'disable' => '停用',
        ),
        'email_record_category' => array(
            'email_registration' => '用户注册',
            'email_reset_email' => '用户重置邮箱',
            'email_reset_password' => '用户重置密码',
        ),
        'timerange' => array(
            'will_expire' => '即将过期',
            'expire' => '已过期',
        ),
        'salesman' => array(
            'none' => '无',
            'lipengcheng' => '李鹏程',
            'duankun' => '段昆',
            'chenzhongqian' => '陈忠乾',
            'taoyangyang' => '陶杨杨',
            'zhenguihan' => '郑贵涵',
            'fanzhigang' => '樊志刚',
            'wenzuoyi' => '温作毅',
            'jiangxuelong' => '江学龙',
            'yuchangming' => '余常明',
            'panxinxing' => '潘新星',
            'lantian' => '蓝天',
            'huanghong' => '黄黉',
            'liwei' =>'李卫',
        ),
        'service' => array(
            'hasStorage' => '云存储',
            'hasLive' => '直播',
            'hasMobile' => '移动端定制',
            'copyright' => '去版权',
        ),
        'live_account_type' => array(
            'tryOut' => '试用',
            'normal' => '正式',
        ),
        'live_account_replay' => array(
            'allowable' => '允许',
            'forbidden' => '禁止'
        ),
        'task_status' => array(
            'created' => '已创建',
            'processing' => '处理中',
            'finished' => '已完成',
            'failure' => '失败',
        ),
        'resource_storage' => array(
            'qiniu' => '七牛',
            'baidu' => '百度',
        ),
        'resource_type' => array(
            'video' => '视频',
            'document' => '文档',
            'audio' => '音频',
            'image' => '图片',
            'ppt' => 'ppt',
            'flash' => 'flash',
            'other' => '其他',
        ),
        'resource_process_status' => array(
            'none' => '无',
            'waiting' => '等待',
            'processing' => '处理中',
            'ok' => '成功',
            'error' => '出错',
        ),
        'resource_job_status' => array(
            'created' => '创建',
            'executing' => '处理中',
            'finished' => '完成',
            'failed' => '出错',
        ),

        'resource_status' => array(
            'unknow' => '未知',
            'uploaded' => '已上传',
            'deleted' => '已删除',
            'deleting' => '正在删除',
            'uploading' => '正在上传',
        ),

        'video_processor' => array(
            'QiQiuYunV1' => 'QiQiuYunV1',
            'QiQiuYunV2' => 'QiQiuYunV2',
            'Qiniu' => 'Qiniu',
            'Baidu' => 'Baidu',
            'BaiduV1' => 'BaiduV1',
        ),
        'audio_processor' => array(
            'QiQiuYunV1' => 'QiQiuYunV1',
            'QiQiuYunV2' => 'QiQiuYunV2',
            'Qiniu' => 'Qiniu',
            'Baidu' => 'Baidu',
            'BaiduV1' => 'BaiduV1',
        ),
        'document_processor' => array(
            'QiQiuYunV1' => 'QiQiuYunV1',
            'QiQiuYunV2' => 'QiQiuYunV2',
            'Qiniu' => 'Qiniu',
            'Baidu' => 'Baidu',
            'BaiduV1' => 'BaiduV1',
        ),
        'ppt_processor' => array(
            'QiQiuYunV1' => 'QiQiuYunV1',
            'QiQiuYunV2' => 'QiQiuYunV2',
            'Qiniu' => 'Qiniu',
            'Baidu' => 'Baidu',
            'BaiduV1' => 'BaiduV1',
        ),

        'admin_roles' => array(
            'ROLE_CUSTOMER_SERVICE_STAFF' => '客服专员',
            'ROLE_CUSTOMER_SERVICE_MANAGER' => '客服主管',
            'ROLE_SALES_MANAGER' => '销售主管',
            'ROLE_FINANCE_MANAGER' => '财务主管',
            'ROLE_OPERATION_MANAGER' => '运维主管',
            'ROLE_ADMIN' => '管理员',
            'ROLE_SUPER_ADMIN' => '超级管理员',
        ),
        'invoice_status' => array(
            'pending' => '待邮寄',
            'mailed' => '已邮寄',
        ),

        'drawMoney_status' => array(
            'pending' => '待处理',
            'turnDown' => '已驳回',
            'done' => '已处理',
        ),
	 'coupon_status' => array(
            'pending' => '待激活',
            'active' => '已激活',
            'expire' => '已过期',
            'consume' => '已消费',
        ),
	 'identityVerify_type' => array(
            'personal' => '个人认证',
            'company' => '企业认证',
        ),
	 'identityVerify_status' => array(
            'verifying' => '未审核',
            'fail' => '已驳回',
            'pass' => '已审核',
        ),

        'cloud_tlp_status' => array(
            '0' => '未开通',
            '1' => '已开通',
        ),

        'cloud_sms_status' => array(
            '0' => '未开通',
            '1' => '正常使用',
        ),

        'cloud_live_status' => array(
            '0' => '未开通',
            '1' => '正常使用',
        ),

        'cloud_video_status' => array(
            '0' => '未开通',
            '1' => '正常使用',
        ),

        'service_name' => array(
            'space' => '空间',
            'transfer' => '流量',
            'sms' => '短信',
            'live' => '直播',
            'tlp' => 'TLP'
        ),

        'service_unit' => array(
            'spaceNode' => 'GB',
            'spacePrice' => '元/GB/月',
            'transferNode' => 'GB/月',
            'transferPrice' => '元/GB/月',
            'liveNode' => '人/月',
            'livePrice' => '元/人/月',
            'smsNode' => '条',
            'smsPrice' => '元/条',
            'tlpPrice' => '元'
        ),
        'priceRange_status' => array(
            'pending' => '未生效',
            'effective' => '已生效',
        ),

        'recommand_storage_status' => array(
            'pending' => '未发布',
            'releasing' => '预发布',
            'released' => '已发布',
        ),

        'live_providers' => array(
            'none' => '未开通',
            'vhall' => '微吼',
            'soooner' => '光慧',
            'sanmang' => '三芒',
            'gensee' => '展示互动'
        ),
        'liveaccount_status' => array(
            'none' => '停用',
            'activate' => '使用中',
        ),

        'liveaccount_status:html' => array(
            'none' => '<span class="text-danger">停用</span>',
            'activate' => '<span class="text-success">使用中</span>',
        ),


        'trial_field' => array(
            'skill' => '职业技能培训',
            'language' => '语言培训',
            'certification' => '资格认证培训',
            'corporate' => '企业内训',
            'interest' => '兴趣教学',
            'k12' => 'K12教育',
            'college' => '大学教育',
            'mooc' => 'MOOC',
            'other' => '其他',
        ),

        'trial_callback_status' => array(
            'pending' => '正在开通',
            'success' => '开通成功',
            'fail' => '开通失败',
            'close' => '关闭',
            'lock' => '锁定',
            ),
        'IDC' => array(
            'baidu-huabei' => '百度华北',
            'baidu-huanan' => '百度华南',
            'aliyun-hangzhou' => '阿里杭州',
            'aliyun-beijing' => '阿里北京',
            'aliyun-shenzhen' => '阿里深圳',
            'aliyun-qingdao' => '阿里青岛',
            'aliyun-hongkong' => '阿里香港',
            'aliyun-shanghai' => '阿里上海',
            'ucloud-huadong' => 'UCloud华东',
            'linkcloud' => 'Linkcloud',
        ),
        'host_purpose' => array(
            'web' => 'web',
            'db' => 'db'
        ),
        'host_owner' => array(
            'saas' => 'SaaS',
            'customer' => '客户自己',
            'transfer' => '已移交'
        ),
        'nature' => array(
            'en' => '英语教学',
            'it' => 'IT技术'
        ),
        'star' => array(
            '5' => '★★★★★',
            '4' => '★★★★',
            '3' => '★★★',
            '2' => '★★',
            '1' => '★',
            '0' => '',
        ),
        'host_deleted' => array(
            '0' => '正常',
            '1' => '已删除'
        ),
        'host_sort' => array(
            'createdTime.DESC' => '创建时间',
            'userId.DESC' => '最新用户',
            'buyTime.DESC' => '最近购买',
            'expireTime.ASC' => '快要到期'
        ),
         'payment' => array(
            'icbc' => '工商银行',
            'zjtlcb' => '泰隆银行',
            'alipay' => '支付宝',
            'cbc' => '建设银行',
            'cheque' => '支票',
            'cash' => '现金',
        ),
         'user_changelog_type' => array(
            'storage_change_open' => '开通',
            'storage_change_buy' => '购买',
            'storage_change_upgrade' => '升级',
            'storage_change_renew' => '续费',
            'sms_change_buy' => '购买',
            'sms_change_open' => '开通',
            'storage_change_update_price' => '变更单价',
            'storage_change_tpl_buy' => 'TLP购买'
        ),
        'host_backup_status' => array(
            'success' => '成功',
            'failed' => '失败',
            'executing' => '执行中'
        ),
        'search_category' => array(
            'course' => '课程',
            'lesson' => '课时',
            'user' => '用户',
            'thread' => '话题',
            'article' => '资讯'
        ),
        'search_log_status' => array(
            'SUCCESS' => '成功',
            'FAILED' => '失败',
            'goWriter' => '写入中'
        ),
        'search_account_init' => array(
            'yes' => '完成',
            'no' => '未完成'
        ),
        'search_user_role' => array(
            'student' => '学生',
            'teacher' => '老师'
        ),
        'search_course_type' => array(
            'live' => '直播',
            'normal' => '普通'
        ),
        'search_lesson_type' => array(
            'audio' => '音频',
            'document' => '文档',
            'flash' => 'flash',
            'live' => '直播',
            'ppt' => 'ppt',
            'testpaper' => '测试',
            'text' => '文字',
            'video' => '视频'
        ),
        'search_thread_type' => array(
            'classroom' => '班级',
            'course' => '课程',
            'group' => '小组',
        ),
        'cps_servers_type' => array(
            'video' => '视频',
            'document' => '文档'
        ),
        'cps_servers_status' => array(
            'stopped' => '停止',
            'running' => '运行'
        ),
        'cps_job_name' => array(
            'downloading' => '下载',
            'processing' => '执行',
            'uploading' => '上传',
            'callbacking' => '通知'
        ),
        'cps_job_status' => array(
            'waiting' => '等待',
            'executing' => '执行中',
            'success' => '成功',
            'failed' => '失败',
        ),
        'cps_job_run_status' => array(
            'unstart' => '未开始',
            'downloading' => '下载中',
            'downloaded' => '已下载',
            'downloadRetry' => '下载重试',
            'downloadFailed' => '下载失败',
            'processing' => '执行中',
            'processFailed' => '执行失败',
            'processed' => '执行成功',
            'processedRetry' => '执行重试',
            'uploading' => '上传中',
            'uploadFailed' => '上传失败',
            'uploaded' => '上传成功',
            'uploadedRetry' => '上传重试'
        ),
        'cps_job_notice_status' => array(
            'unstart' => '未开始',
            'success' => '成功',
            'failed' => '失败'
        ),
        'knowledge_link' =>array(
            'product' =>'产品类',
            'technology' =>'技术类'
            )
    );

    public static function dict($type)
    {
        return isset(self::$dict[$type]) ? self::$dict[$type] : array();
    }

    public static function text($type, $key)
    {
        if (!isset(self::$dict[$type])) {
            return null;
        }

        if (!isset(self::$dict[$type][$key])) {
            return null;
        }

        return self::$dict[$type][$key];
    }

}
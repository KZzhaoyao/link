seajs.config({
	alias: {
		'jquery': 'jquery/2.1.1/jquery',
		'$': 'jquery/2.1.1/jquery',
		'bootstrap': 'bootstrap.js/3.2.0/bootstrap',
		'notify': 'bootstrap.js/3.2.0/bootstrap-notify',
		'arale-validator': 'arale-validator/0.10.0/index',
		'jquery.bootstrap-datetimepicker': "jquery-plugin/bootstrap-datetimepicker/1.0.0/datetimepicker",
	},

	// 变量配置
	vars: {
		'locale': 'zh-cn'
	},

    base: '/jslib/dist/',

	charset: 'utf-8',

	debug: true
});

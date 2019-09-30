import Vue from 'vue'

uni['DOMIAN'] = 'http://www.xxx.com';
if(process.env.NODE_ENV === 'development'){
	uni.DOMIAN = 'http://xxx.sssui.com';
}

const res = uni.getStorageSync('User');
uni['User']= uni.array_merge({
		phone	:	"",
		account	:	"",
		nickname	:	"",
		token	:	"",
		avatar	:	'../../static/noavatar.jpg',
},res);
/*
uni['signin'] = function(phone,password) {
	var data = {		
		username: phone,
		password: password
	}
	uni.post({method:'user.login'},data,function(res){
		//返回会员信息,同步设置
		Vue.prototype.User =  res.data;
		uni.setStorageSync('state',Vue.prototype.G);
		uni.tips(res.msg,res.code,-1);
	});
}*/

uni['signout'] = function () {
	uni.User = {
		phone	:	"",
		account	:	"",
		nickname	:	"",
		token	:	"",
		avatar	:	'../../static/noavatar.jpg',
	};
	uni.setStorageSync('User',uni.User);	
	uni.reLaunch({url: '../index/index'});
},

uni['needlogin'] = function (callback) {
	if (!uni.User.phone) {
		uni.showModal({
			title: '未登录',
			content: '您未登录，需要登录后才能继续',
			/**
			* 如果需要强制登录，不显示取消按钮
			*/
			showCancel: !uni.User.forcedLogin,
			success: (res) => {
				if (res.confirm) {
					/**
					* 如果需要强制登录，使用reLaunch方式
					*/
					if (uni.User.forcedLogin) {
						uni.reLaunch({url: '../user/login'});
					} else {
						uni.navigateTo({url: '../user/login'});
					}
				}
			}
		});
	}else{
		callback && callback.call(this);
	}
},

uni['isLogin'] = function (){
	return uni.User.phone ? true : false;
}
uni['getUserInfo'] = function(callback){
	this.post({method:'User.getUserInfo'},{},function(res){
		callback && callback.call(this,res.data);
	});
}
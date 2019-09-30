import Vue from 'vue';
import SHA from '../common/sha.js'
import RSA from '../common/rsa.js'
Date.prototype.format = function (mask)
{
	mask = mask ? mask : 'Y-m-d H:i:s';
	var d = this;
	var zeroize = function (value, length)
	{
		if (!length) length = 2;
		value = String(value);
		for (var i = 0, zeros = ''; i < (length - value.length); i++)
		{
			zeros += '0';
		}
		return zeros + value;
	};
	return mask.replace(/"[^"]*"|'[^']*'|\b([jdDLnmMFyYghGHisuUaA])\b/g, function ($0)
	{
		switch ($0)
		{
			case 'j': return d.getDate();
			case 'd': return zeroize(d.getDate());
			case 'D': return ['Sun', 'Mon', 'Tue', 'Wed', 'Thr', 'Fri', 'Sat'][d.getDay()];
			case 'L': return ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'][d.getDay()];
			case 'n': return d.getMonth() + 1;
			case 'm': return zeroize(d.getMonth() + 1);
			case 'M': return ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'][d.getMonth()];
			case 'F': return ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'][d.getMonth()];
			case 'y': return String(d.getFullYear()).substr(2);
			case 'Y': return d.getFullYear();
			case 'g': return d.getHours() % 12 || 12;
			case 'h': return zeroize(d.getHours() % 12 || 12);
			case 'G': return d.getHours();
			case 'H': return zeroize(d.getHours());
			case 'i': return zeroize(d.getMinutes());
			case 's': return zeroize(d.getSeconds());
			case 'u': return zeroize(d.getMilliseconds(), 3);
			case 'U': var m = d.getMilliseconds();
				if (m > 99) m = Math.round(m / 10);
				return zeroize(m);
			case 'a': return d.getHours() < 12 ? 'am' : 'pm';
			case 'A': return d.getHours() < 12 ? 'AM' : 'PM';
			default: return $0.substr(1, $0.length - 2);
		}
	});
};
Date.prototype.addDate = function (num,dw){
	switch(dw){
		case 'y':
			this.setYear(this.getYear()+num); 
			break;
		case 'm':
			this.setMonth(this.getMonth()+num); 
			break;
		case 'd':
			this.setDate(this.getDate()+num); 
			break;
	}
	return this;
}

uni['RSA'] = Vue.prototype.RSA = RSA;
uni['SHA'] = Vue.prototype.SHA = SHA;

uni['strtotime'] = function (date) {
	return parseInt(new Date(date).getTime()/1000);
}

uni['timetostr'] = function (time){
	if(time){
		var now = new Date(parseInt(time) * 1000);
	}else{
		var now = new Date();			
	}
	return now.format();
}

var array_merge = function (FirstOBJ, SecondOBJ) { // 深度合并对象
    for (var key in SecondOBJ) {
        FirstOBJ[key] = FirstOBJ[key] && FirstOBJ[key].toString() === "[object Object]" ?
            uni.array_merge(FirstOBJ[key], SecondOBJ[key]) : FirstOBJ[key] = SecondOBJ[key];
    }
    return FirstOBJ;
};
uni['array_merge'] = function () { // 深度合并对象
	var args= arguments;
	var ret = args[0];
	for(var i=1;i<args.length;i++){
		ret = array_merge(ret,args[i]);
	}
    return ret;
}
uni['ksort'] =function (arys) { 
	//先用Object内置类的keys方法获取要排序对象的属性名，再利用Array原型上的sort方法对获取的属性名进行排序，newkey是一个数组
	var newkey = Object.keys(arys).sort();　　 
	//console.log('newkey='+newkey);
	var newObj = {}; //创建一个新的对象，用于存放排好序的键值对
	for(var i = 0; i < newkey.length; i++) {
		//遍历newkey数组
		newObj[newkey[i]] = arys[newkey[i]]; 
		//向新创建的对象中按照排好的顺序依次增加键值对

	}
	return newObj; //返回排好序的新对象
}

uni['now'] = Date.now || function () {
    return new Date().getTime();
};

uni['isArray'] = Array.isArray || function (obj) {
    return obj instanceof Array;
};

uni['send'] = Vue.prototype.send = function(url,data,method,success,err,complate){
	if(uni.User.token){
		data['access_token'] = uni.User.token;
	}
	var data = SHA.getSignUrl(data);
	var obj = {
		url: uni.DOMIAN+url, //仅为示例，并非真实接口地址。
		data: data,
		method:method,
		header:{'ly':'cansnow'},
		success: function(res){
			uni.hideLoading();
			//uni.log(res.data);
			if(res.data.code!=1){
				uni.tips(res.data.msg);
				err && err.call(this,res.data);
			}else{
				success && success.call(this,res.data);				
			}
		},
		err: function(){ 
			uni.hideLoading();
			err && err.call(this);
		},
		complate: function(){
			uni.hideLoading();
			complate && complate.call(this);
		}
	};
	uni.showLoading({
		title: '加载中'
	});
	uni.request(obj);
}

uni['post'] = Vue.prototype.post = function(url,data,success,err,complate){
	this.send(url,data,'POST',success,err,complate)
}

uni['get'] = Vue.prototype.get = function(url,data,success,err,complate){
	if(typeof data == 'function'){
		complate = err;
		err = success;
		success = data;
		data = {};
	}
	this.send(url,data,'GET',success,err,complate)		
}

uni['alert'] = function(msg,time){
	uni.showModal({
		content:msg,
		showCancel:false,
		time: (time || 3000)
	});	
}

uni['log'] = function(){
	console.log.apply(this,arguments);
}

uni['upload'] = function(src,name,callback){
	if(src.substring(0,4)!=='blob'){callback && callback.call(uni,src); return;}
	var getArr = {method:'User.upload'};
	var postArr	= {access_token:uni.G.User.token};
	var url = SHA.getSignUrl(getArr,postArr); 
	//uni.log(src);
	uni.uploadFile({
		url: uni.DOMIAN+ url,
		filePath: src,
		formData:postArr,
		name: name || 'A'+(Math.random()*10e9).toFixed(0),
		success: (res) => {
			//uni.log(res);
			var data = JSON.parse(res.data);
			if(data.code!=1){
				uni.alert(data.msg);
			}else{
				callback && callback.call(uni,data.url)
				//that.$emit('input', data.msg.url) //触发 input 事件，并传入新值
			}
		},
		fail: (err) => {			
			uni.log(err)
			uni.alert(err.errMsg);
		},
		complete: () => {
			uni.hideLoading();
		}
	})
}

/** 
 * @description 提示函数
 * @param {String} msg 提示消息
 * @param {String} icon 图标
 * @param {String} url 跳转地址
 * @return {none} 返回值 
 * @author 李乾坤 
 */
uni['tips'] = function(msg,icon,url){
	if(typeof msg !=='string'){
		url = icon;
		icon = msg.code;
		msg = msg.msg;
	}
	icon = icon +"";
	var obj = { title: msg };
	var imgs={
		'a1':"../../static/success.png",
		'a-1':"../../static/warning.png",
		'a0':"../../static/error.png",
		'a2':"../../static/info.png",
	};
	if(!icon){icon="none";}
	if(imgs['a'+icon]){
		icon = imgs['a'+icon];
		obj.image=icon;
	}else{
		icon ="none";
		obj.icon=icon;
	}
	if(url){
		obj.duration = 3000;
		//uni.log(obj,url);
		if(url=='-1'){	
			setTimeout(function(){		
				uni.navigateBack({delta: 1});
			},obj.duration);
		}else{
			setTimeout(function(){
				uni.reLaunch({url: url});
			},obj.duration);
		}
	}
	//console.log(obj);
	uni.showToast(obj);
}
//console.log(uni);
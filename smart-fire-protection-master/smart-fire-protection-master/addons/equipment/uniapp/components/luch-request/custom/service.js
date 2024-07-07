/**
 * @version 3.0.7
 * @Author lu-ch
 * @Date 2021-09-04
 * @Email webwork.s@qq.com
 * 文档: https://www.quanzhan.co/luch-request/
 * github: https://github.com/lei-mu/luch-request
 * DCloud: http://ext.dcloud.net.cn/plugin?id=392
 * HBuilderX: beat-3.0.4 alpha-3.0.4
 */

import { // 局部引入
	auth
} from './api.js'
import Config from '@/utils/config.js'
import Request from '../index.js'

const http = new Request({
	validateStatus: (statusCode) => { // statusCode 必存在。此处示例为全局默认配置
		return (statusCode >= 200 && statusCode < 300) || statusCode == 401
	}
})
http.setConfig((config) => { /* 设置全局配置 */
	config.baseURL = Config.apiUrl
	config.header = {
		...config.header,
		ContentType: 'application/json;charset=UTF-8',
		ContentType: 'application/x-www-form-urlencoded',
		'Accept-Language': 'zh-CN,zh',
		'X-Requested-With': 'XMLHttpRequest'
	}
	return config
})

http.interceptors.request.use((config) => { /* 请求之前拦截器。可以使用async await 做异步操作 */
	config.header = {
		...config.header,
		'token': getTokenStorage()
	}
	/*
	 if (!token) { // 如果token不存在，return Promise.reject(config) 会取消本次请求
	   return Promise.reject(config)
	 }
	 */
	return config
}, (config) => {
	return Promise.reject(config)
})

http.interceptors.response.use(async (response) => { /* 请求之后拦截器。可以使用async await 做异步操作  */
	// if (response.data.code !== 200) { // 服务端返回的状态码不等于200，则reject()
	//   return Promise.reject(response)
	// }

	const {
		statusCode,
		config
	} = response
	try {
		return await handleCode(response, statusCode, config)
	} catch (err) {
		return Promise.reject(err)
	}

	return response
}, (response) => { // 请求错误做点什么。可以使用async await 做异步操作
	console.log(response)
	return Promise.reject(response)
})


/**
 * 获取 token
 * @return {string}
 */
const getTokenStorage = () => {
	return uni.getStorageSync('equipment_token') || ''
}

/**
 * 重新请求更新获取 `token`
 * @param {number} uid
 * @return {Promise}
 */
const getApiToken = () => {
	let params = { // 重新授权登录
		code: uni.getStorageSync('equipment_code') || '',
		openid: uni.getStorageSync('equipment_openid') || '',
		token: uni.getStorageSync('equipment_token') || '', 
	}
	return auth(params).then((res) => {
		return res.data
	})
}

/**
 * 保存 token 到 localStorage
 * @param {object} data
 */
const saveTokenOpenid = (data) => {
	uni.setStorageSync('equipment_token', data.token)
	if (data.openid != '') {
		uni.setStorageSync('equipment_openid', data.openid)
	}
}

/**
 * 处理 http状态码
 * @param {object} o
 * @param {object} o.res 请求返回的数据
 * @param {object} o.config 本次请求的config数据
 * @param {string|number} o.statusCode http状态码
 * @return {object|Promise<reject>}
 */
const handleCode = (res, statusCode, config) => {
	const STATUS = {
		'200'() {
			if (res.data.code == 1) {
				return res.data
			} else {
				return Promise.reject(res.data)
			}
		},
		'401'() {
			// 定义实例发送次数
			if (!config.hasOwnProperty("count")) {
				config.count = 0
			}

			// 只让这个实例发送一次请求，如果code还是401则抛出错误
			if (config.count === 1) {
				return Promise.reject(res)
			}

			config.count++; // count字段自增，可以用来判断请求次数，避免多次发送重复的请求

			return getApiToken()
				.then(saveTokenOpenid)
				.then(() => http.request(config))
		}
	}

	return STATUS[statusCode] ? STATUS[statusCode]() : Promise.reject(res) // 有状态码但不在这个封装的配置里，就直接进入 `fail`
}

export {
	http
}

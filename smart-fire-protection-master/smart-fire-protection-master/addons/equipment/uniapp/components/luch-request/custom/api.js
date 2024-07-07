import {
	http
} from './service.js'

/**
 * 授权
 * @param {Object} params
 */
export const auth = (params) => {
	// 若使用code, 刷新code待后续使用
	if (params.code) {
		wx.login({
			success(res) {
				if (res.code) {
					uni.setStorageSync('equipment_code', res.code)
				}
			}
		})
	}

	return http.post('user/weapplogin', params)
}

export default {
	/**
	 * 系统数据
	 */
	getSystemInfo() {
		return http.get('index/getSystemInfo')
	},
	/**
	 * 登录
	 * @param {Object} params
	 */
	login(params) {
		return http.post('user/login', params)
	},
	/**
	 * 微信授权登录
	 * @param {Object} params
	 */
	welogin(params) {
		return http.post('user/weapplogin', params)
	},
	/**
	 * 退出登录
	 * @param {Object} params
	 */
	logout(params) {
		return http.post('user/logout', params)
	},
	/**
	 * 工作台
	 */
	workbench() {
		return http.get('manage/workbench')
	},
	/**
	 * 设备档案列表
	 * @param {Object} params
	 */
	archives(params) {
		return http.post('manage/archives', params)
	},
	/**
	 * 设备列表
	 * @param {Object} params
	 */
	equipments(params) {
		return http.post('manage/equipments', params)
	},
	/**
	 * 设备信息
	 * @param {Object} params
	 */
	equipment(params) {
		return http.get('index/equipments', {
			params
		})
	},
	/**
	 * 维修工单列表
	 * @param {Object} params
	 */
	repairs(params) {
		return http.post('manage/repairs', params)
	},
	/**
	 * 报修详情
	 * @param {Object} params
	 */
	repairInfo(params) {
		return http.get('index/repairInfos', {
			params
		})
	},
	/**
	 * 设备报修
	 * @param {Object} params
	 */
	repair(params) {
		return http.post('index/repairs', params)
	},
	/**
	 * 维修接单
	 * @param {Object} params
	 */
	receiveRepairs(params) {
		return http.post('manage/receiveRepairs', params)
	},
	/**
	 * 维修登记
	 * @param {Object} params
	 */
	register(params) {
		return http.post('index/registers', params)
	},
	/**
	 * 计划任务字段
	 * @param {Object} params
	 */
	planTaskField(params) {
		return http.get('index/planTaskFields', {
			params
		})
	},
	/**
	 * 完成计划任务
	 * @param {Object} params
	 */
	submitPlanTask(params) {
		return http.post('index/submitPlanTasks', params)
	},
	/**
	 * 记录详情
	 * @param {Object} params
	 */
	getRecordInfo(params) {
		return http.post('index/getRecordInfo', params)
	},
	/**
	 * 故障原因
	 * @param {Object} params
	 */
	failureCause(params) {
		return http.post('index/getFailureCause', params)
	},
	/**
	 * 员工信息
	 * @param {Object} params
	 */
	getStaffInfo(params) {
		return http.post('user/getStaffInfo', params)
	},
}

<template>
	<view class="container">
		<view class="header-bg" :class="'bg-' + info.work_status">
			<view class="text-lg text-white padding-lr padding-tb-sm">运行状态：{{info.work_status_text || '正常'}}</view>
		</view>
		<view class="task cu-list menu sm-border card-menu margin-top bg-white shadow-warp">
			<view class="sub-title">待完成任务</view>
			<block v-if="info.todos.length === 0">
				<view class="none">
					暂无任务
				</view>
			</block>
			<block v-else>
				<view class="cu-item" v-if="info.todos.repair">
					<view class="cu-avatar round repair-avatar"></view>
					<view class="content padding-tb-sm">
						<view>维修工单 {{info.todos.repair.title}}</view>
						<view class="text-gray text-sm">
							请尽快维修
						</view>
					</view>
					<view class="action">
						<button class="cu-btn line-blue" @click="repairInfo(info.todos.repair.id)">记录</button>
					</view>
				</view>
				<view class="cu-item" v-if="info.todos.inspection">
					<view class="cu-avatar round inspection-avatar"></view>
					<view class="content padding-tb-sm">
						<view>{{info.todos.inspection.title}}<text
								class="cu-tag light bg-gray sm">每{{info.todos.inspection.periodicity}}天/次</text></view>
						<view class="text-gray text-sm">
							请在{{info.todos.inspection.duetime}}前完成
						</view>
					</view>
					<view class="action">
						<button class="cu-btn line-blue"
							@click="planTodo(info.todos.inspection.id, 'inspection')">记录</button>
					</view>
				</view>
				<view class="cu-item" v-if="info.todos.maintenance">
					<view class="cu-avatar round maintenance-avatar"></view>
					<view class="content padding-tb-sm">
						<view>{{info.todos.maintenance.title}}<text
								class="cu-tag light bg-gray sm">每{{info.todos.maintenance.periodicity}}天/次</text></view>
						<view class="text-gray text-sm">
							请在{{info.todos.maintenance.duetime}}前完成
						</view>
					</view>
					<view class="action">
						<button class="cu-btn line-blue"
							@click="planTodo(info.todos.maintenance.id, 'maintenance')">记录</button>
					</view>
				</view>
			</block>
		</view>

		<view class="card cu-list menu margin-top bg-white shadow-warp">
			<view class="sub-title">实时环境信息</view>
			<view class="info margin-sm">
				<view class="flex flex-wrap">
					<view class="basis-sm padding-sm">温度</view>
					<view class="basis-lg padding-sm">{{info.temp}}  ℃</view>
				</view>
				<view class="flex flex-wrap">
					<view class="basis-sm padding-sm">湿度</view>
					<view class="basis-lg padding-sm">{{info.hum}}  %RH</view>
				</view>
				<view class="flex flex-wrap">
					<view class="basis-sm padding-sm">气压</view>
					<view class="basis-lg padding-sm">{{info.press}}  kPa</view>
				</view>
				<view class="flex flex-wrap">
					<view class="basis-sm padding-sm">光照强度</view>
					<view class="basis-lg padding-sm">{{info.light}}  lx</view>
				</view>
				<view class="flex flex-wrap">
					<view class="basis-sm padding-sm">烟雾检测</view>
					<view class="basis-lg padding-sm">{{info.sta==0?'正常':'含有易燃气体' || ''}}</view>
				</view>
			</view>
		</view>
		<view class="card cu-list menu margin-top bg-white shadow-warp">
			<view class="sub-title">设备信息</view>
			<view class="info margin-sm">
				<view class="flex flex-wrap">
					<view class="basis-sm padding-sm">设备编号</view>
					<view class="basis-lg padding-sm">{{info.equipment_code || ''}}</view>
				</view>
				<view class="flex flex-wrap">
					<view class="basis-sm padding-sm">设备型号</view>
					<view class="basis-lg padding-sm">{{info.archive_model || ''}}</view>
				</view>
				<view class="flex flex-wrap">
					<view class="basis-sm padding-sm">设备名称</view>
					<view class="basis-lg padding-sm">{{info.archive_name || ''}}</view>
				</view>
				<view class="flex flex-wrap">
					<view class="basis-sm padding-sm">供应商</view>
					<view class="basis-lg padding-sm">{{info.supplier || ''}}</view>
				</view>
				<view class="flex flex-wrap">
					<view class="basis-sm padding-sm">购置日期</view>
					<view class="basis-lg padding-sm">{{info.purchasetime || ''}}</view>
				</view>
				<view class="flex flex-wrap">
					<view class="basis-sm padding-sm">所在区域</view>
					<view class="basis-lg padding-sm">{{info.region || ''}}</view>
				</view>
				<view class="flex flex-wrap">
					<view class="basis-sm padding-sm">负责人</view>
					<view class="basis-lg padding-sm">{{info.responsible_name || ''}}</view>
				</view>
				<view class="flex flex-wrap">
					<view class="basis-sm padding-sm">联系电话</view>
					<view class="basis-lg padding-sm" @click="call(info.responsible_mobile)">
						{{info.responsible_mobile || ''}}
					</view>
				</view>
				<view class="flex flex-wrap">
					<view class="basis-sm padding-sm">备注</view>
					<view class="basis-lg padding-sm">{{info.remark != null ? info.remark : ''}}</view>
				</view>
				<view class="flex flex-wrap">
					<view class="basis-sm padding-sm">设备文档</view>
					<view class="basis-lg padding-sm text-blue" @click="viewDocument(info.document)"><text
							v-if="info.document != ''">查看</text></view>
				</view>
			</view>
		</view>

		<view class="card cu-list menu margin-top bg-white shadow-warp">
			<view class="sub-title">历史记录</view>
			<block v-if="info.records.length === 0">
				<view class="none">
					暂无记录
				</view>
			</block>
			<block v-else>
				<view class="cu-timeline margin-top padding-bottom">
					<view class="cu-item"
						:class="[item.type == 'inspection' ? 'text-cyan' : '', item.type == 'maintenance' ? 'text-pink' : '', item.type == 'repair' ? 'text-red' : '']"
						v-for="(item, index) in info.records" :key="index">
						<view class="content" @click="recordInfo(item.id, item.type)">
							<view>{{item.name}}</view>
							<view class="action">{{item.createtime}}&nbsp;&nbsp;&nbsp;&nbsp;{{item.user}}</view>
						</view>
					</view>
				</view>
			</block>
		</view>

		<view class="through" style="height: 60px;"></view>
		<block v-if="info.work_status != 'scrapped'">
			<view class="cu-bar bg-white tabbar flex flex-wrap" @click="repair()"
				v-if="(type == 'repair') && (info.work_status == 'normal')">
				<button class="bg-black submit basis-sm" style="border-radius: 0;">我要报修</button>
			</view>
		</block>
	</view>
</template>

<script>
	var _self
	var coding = ''
	var login = false
	var loginFlag = false
	export default {
		data() {
			return {
				info: {},
				type: 'repair'
			};
		},
		onLoad(e) {
			_self = this;
			if (!e.coding) {
				_self.tui.toast("未知设备");
				setTimeout(function() {
					uni.navigateBack();
				}, 1500)

				return
			}

			if ('type' in e) {
				_self.type = e.type;
			}

			coding = e.coding
			login = !getApp().globalData.token ? false : true;
			_self.getInfo();
		},
		methods: {
			getInfo() {
				uni.showLoading({
					title: '查询中...'
				})

				_self.$api.equipment({
					coding
				}).then((res) => {
					uni.hideLoading();
					_self.info = res.data;
					_self.changeColor(res.data.work_status);
					uni.setNavigationBarTitle({
						title: res.data.archive_name + ' ' + res.data.archive_model
					});
				}).catch((err) => {
					uni.hideLoading()
					_self.tui.toast(err.msg)
				})
			},
			changeColor(status) {
				var backgroundColor = '#25ac66'
				switch (status) {
					case 'sickness':
						backgroundColor = '#ea9003';
						break;
					case 'repairing':
						backgroundColor = '#dd0000';
						break;
					case 'scrapped':
						backgroundColor = '#999999';
						break;
					case 'normal':
					default:
						backgroundColor = '#25ac66';
						break;
				}
				uni.setNavigationBarColor({
					frontColor: '#ffffff',
					backgroundColor
				});
			},
			call(mobile) {
				if (mobile == '') return;
				uni.makePhoneCall({
					phoneNumber: mobile
				})
			},
			viewDocument(url) {
				if (url == '') return;

				let suffix = url.slice(-4);
				if (suffix == '.pdf') {
					uni.showLoading({
						title: '加载中...',
						mask: true
					})

					uni.downloadFile({
						url,
						success: function(res) {
							if (res.statusCode === 200) {
								let filePath = res.tempFilePath
								uni.openDocument({
									filePath,
									fileType: 'pdf',
									success: function(res) {},
									fail: function(err) {
										_self.tui.toast('文档加载失败');
									},
									complete: function() {
										uni.hideLoading();
									}
								})
							} else {
								uni.hideLoading();
								_self.tui.toast('文档加载失败');
							}
						},
						fail: function(err) {
							uni.hideLoading();
							_self.tui.toast('文档加载失败');
						}
					})
				} else {
					uni.previewImage({
						urls: [url],
					});
				}
			},
			repair() {
				if (loginFlag) return
				loginFlag = true

				if (!login) {
					wx.login({
						success(res) {
							if (res.code) {
								_self.goWeLogin(res.code)
							} else {
								_self.tipLogin()
							}
						},
						fail: function(error) {
							_self.tipLogin()
						}
					})
				} else {
					_self.goRepair()
				}
			},
			goRepair() {
				loginFlag = false
				uni.navigateTo({
					url: '/pages/repair/repair?equipment_id=' + _self.info.id,
					events: {
						// 为指定事件添加一个监听器，获取被打开页面传送到当前页面的数据
						doRefresh: function(data) {
							if (data && data.ischange) {
								_self.getInfo();
								uni.$emit('manageRefresh', {
									ischange: true
								});
							}
						}
					}
				})
			},
			repairInfo(id) {
				uni.$once('repairChange', function(data) {
					if (data && data.ischange) {
						_self.getInfo();
						uni.$emit('manageRefresh', {
							ischange: true
						});
					}
				});
				uni.navigateTo({
					url: '/pages/repair/info?id=' + id
				})
			},
			planTodo(id, type) {
				uni.navigateTo({
					url: '/pages/plan/dotask?id=' + id + '&type=' + type,
					events: {
						// 为指定事件添加一个监听器，获取被打开页面传送到当前页面的数据
						doRefresh: function(data) {
							if (data && data.ischange) {
								_self.getInfo()
								uni.$emit('manageRefresh', {
									ischange: true
								})
							}
						}
					}
				})
			},
			recordInfo(id, type) {
				if (type === 'inspection' || type === 'maintenance') {
					uni.navigateTo({
						url: '/pages/plan/info?id=' + id + '&type=' + type
					})
				}
				if (type === 'repair') {
					uni.navigateTo({
						url: '/pages/repair/record?id=' + id
					})
				}
			},
			goWeLogin(code) {
				_self.$api.welogin({
					code
				}).then((res) => {
					login = true
					getApp().globalData.token = res.data.token
					uni.setStorageSync('equipment_token', res.data.token)
					uni.setStorageSync('equipment_openid', res.data.openid)

					uni.$emit('loginStatusEvent', {
						isLogin: true
					})

					_self.goRepair()
				}).catch((err) => {
					_self.tipLogin()
				})
			},
			tipLogin() {
				loginFlag = false
				uni.showModal({
					title: '提示',
					content: '还未登录账号，无法进行本次操作',
					confirmText: '登录',
					cancelColor: '#8799a3',
					confirmColor: '#333333',
					success: function(res) {
						if (res.confirm) {
							uni.navigateTo({
								url: '/pages/login/login',
								events: {
									// 为指定事件添加一个监听器，获取被打开页面传送到当前页面的数据
									isLoginFromLogin: function(data) {
										if (data && data.isLogin) {
											login = true
										}
									}
								}
							})
						}
					}
				})
			}
		}
	}
</script>

<style lang="scss">
	.bg-normal {
		background-color: #25AC66;
	}

	.bg-sickness {
		background-color: #EA9003;
	}

	.bg-scrapped {
		background-color: #999999;
	}

	.bg-repairing {
		background-color: #DD0000;
	}

	.header-bg {
		width: 100vw;
		height: 100px;
	}

	.sub-title {
		color: #333333;
		font-size: 28rpx;
		padding: 10rpx 20rpx;
		font-weight: bold;
	}

	.task {
		margin-top: -50px;

		.sub-title {
			margin-top: 20rpx;
		}

		.cu-tag {
			margin-left: 10rpx;
		}

		&.cu-list.card-menu {
			margin-right: 20rpx;
			margin-left: 20rpx;
			padding-bottom: 10rpx;
			border-radius: 10rpx;
		}

		&.cu-list.menu>.cu-item {
			padding: 0 20rpx;

			.content {
				color: #333333;
				font-size: 28rpx;
				margin-left: 20rpx;
			}
		}
	}

	.info {
		margin-top: 10rpx;
		border: 1px #F1F1F1 solid;
		border-bottom: 0;
		font-size: 26rpx;
		color: #606266;

		.flex {
			border-bottom: 1px #F1F1F1 solid;
			background-color: #FFFFFF;

			view {
				padding: 8px 10px;
				box-sizing: border-box;
			}

			.basis-sm {
				border-right: 1px #F1F1F1 solid;
			}
		}
	}

	.none {
		height: 70px;
		text-align: center;
		line-height: 50px;
		color: #AAAAAA;
	}

	.through {
		width: 100vw;
		height: 20rpx;
	}

	.cu-list+.cu-list {
		margin-top: 20rpx;
	}

	.card {
		margin: 20rpx;
		border-radius: 10rpx;
		padding: 20rpx 0;
	}

	.cu-timeline {
		.cu-item {
			padding: 0rpx 30rpx 30rpx 120rpx;

			&:last-child {
				padding-bottom: 0;
			}

			.content {
				.action {
					color: #AAAAAA;
					font-size: 24rpx;
					margin-top: 10rpx;
				}
			}
		}
	}

	.cu-bar.tabbar {
		position: fixed;
		width: 100%;
		left: 0;
		bottom: 0;
	}

	.repair-avatar {
		background-color: #FFFFFF;
		background-image: url('data:image/png;base64,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');
	}

	.inspection-avatar {
		background-color: #FFFFFF;
		background-image: url('data:image/png;base64,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');
	}

	.maintenance-avatar {
		background-color: #FFFFFF;
		background-image: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAIAAAACACAMAAAD04JH5AAAAz1BMVEUAAAD/5eP/5uT/5eT/5eT/5eP/5eH/5uP/5OP/5eL/1tb/5eP/5eP/5uP/5eP/5OT/5eP/5eP/5eT/5eP/5OL/5uT/5eP/5eP/5eL/6OT/4eH/////5eP/5OL/5eX/59//5eP8u7b8UEL9eW//4d//0c79npf/2tf/1NH9wLz9y8f/3tz/19P+zcr+yMP9vbj9xL/+pJ39f3b+s63+r6n9hXz/3Nn9j4b8YVT8V0n9al79q6T9dWr9lIv+trH9mZD9ioH9e3H9cGX+qKH8XVDB3/VbAAAAIHRSTlMAkyXfvW0u6LZWA8qbb1kb8+Wfh4B6dWROQiIFimAnIHz+HqgAAAWJSURBVHja7ZvpUuJAEIDDrcghHqC7Hj25A4FwCoiI6L7/My1CLAxmOt0J7rpbfn9ilT+6melzekb55ptv4lJMX15UGuXz83KjcnGZLip/kJtcKZuBHTLZUu5G+XyqlVQGpGRSlaryiaRzBxDJQS6tfArVoHRch/2vQ/q0AAwKp/tdhnzqGJgcp/J7E/8jBbFI/diPw9cLEJNCfQ/h4fAIEnB0mFD8zxQkJPUzkfFlgUDTAIRsPsHy14BCbwYYtdjbcAYkbK3fApSzePJPgMZQ0x4B5ySO/CzQMPqaNnIBJ8uXnwEipraiBxFkuPKBiqet8SAKnnxy9HE0HycyJnHklxi/n6xBiS7/Coj0tHcMIYIruv8TGWsBTEBgxINDoGIyFYBDUvyvQTwNehBJLU/If0gAQjWYAYFsdG7k5V9rRF1/nxTfAHAGvvw+kpI5ZlDk1j+67wkDIHKEV2l14PK4lv8EZOpo/cuvP62+b4FUCj9iWGAryhHagEC3w7xso7stPB3NgUOevQCuQDTQR0hFxFuC9LGs7BXCsbGMZACHY1nfeCpdZbGiqUtT8lIHFqeS/lvmApZY4xi6pCp+Ah6F8O49BxLawscxQk1hbAKTnBLGAUjoii0dW/9oBANgchBqgiDhTgTwOoYVVEJ0gEuaswO2+IDXbbtbJYwWcMlxdqApwnGahm3d6b4iiffgFhATQPGcbqfZNlyrxdDlVtnlWhrpBB3Ht49orumtqCt4+I7CblczSBTg4jUtdq+YR02AT8dipsQyyBAxaeIbUaZ2g5YIZzgfDyIs0uJ0ilmqCcxGo+HqY/5S1YkpRF9Vl0KGzTiyyBBNYDFdCx5M1BX34uH184BoQLXCIjERzKYbwZq6Rmw+ppDhgpSiJBPhcdgXPN58posoBYRLzEeXtAUwVV/y5vO8+dwLBKklXgYUuKAtwL0a4G09BEJX5o0XAQUqpEw8VsOYrhZihMQDCKcSUKBB2YDhrx3J6pal4LpCQxIIkTzYVxHkUcnRCaHwnCDfVBEwTzAgjPMoBe7EDs8qAuYJjh6tQDm6DJirGHOBYEdvQSMyBzxOVIRnIUXW1jWi3NByxHtGKkZPyGnriBuigagdTAII/RiVyQUhFLtOIAlI+TWMkQ8vKclIb5JccBwnG6Zp6dj2aC7Ir0iKxIKk1VwnAYwFEoGkZOgTorsm7oIPceRDljOiQGPQ9EVm/6zxRQ7ktBEXRDLxHas/vgE5TyrCZCCLP4ByQx/TCdwFkUoIIcM4ptfQJIB64FCnnxVWQMIMLwMwC9SXA0AKsiBVkLBEkwDqggP5TLdKPqKxVYx2y/BEGK3XADLStB79mCwXYwcWfqj8QHdtAfJ5Zo5+TPeAZcFNqrW6oT7YQ2YpafpB5T1WhoCP8bEfcufIRPWAcVQ7QbJgb/T6X234YRGc9fr7PNnIDkQfVk/VaPoW6O3dbmgol1+oMo7rVQrPBoDr7UShnnSed4oMLNA4iLuD5QTzkL0eZz0iAwtSODbVaO7NTe3yZggewJsbLpAwTBtaedEWsHW0TqASMCROmGeN7VoTFWMyD9QdnUAptAidJqWYg8sXrBp72a06jPenIp42Zg4ulTojG02fPMm5ou7/7Yalgjp/eC0xPENSRjvOtpYSyPCaMb4fSQxPVshvXcgmj+/xyqiHGB42aX5ELJB3heNdc7x8sYBKG7nCwb3E4poPK/ozFxJQyye6xpOcw0QXmZJzlugqY3JOEl1mTE420XXG5GQUFrB3FCZHsFeOFDYl2CMlJQZXsDeuEl1sTs5ZkqvdyakdJrrcnphs/l++3v/3Hzh8gScef/+Ryxd45vMFHjp9gadeX+Cx24bb6xPsud/J9a3y+eTL4Q8ey9Fm9588+fzmm/+L3xG0O/rON7H3AAAAAElFTkSuQmCC');
	}
</style>

<template>
	<view style="padding: 80rpx; padding-top: 30vh;">
		<button size="default" type="primary" @click="subscribeMessage" hover-class="is-hover">订阅消息</button>

		<input type="text" v-model="openid"
			style="height: 80rpx; border: 1px solid #A6E22E; margin-top: 20px; margin-bottom: 20px;">

		<button size="default" type="primary" @click="getOpenid" hover-class="is-hover">获取openid</button>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				domain: 'www.example.com',
				openid: '',
			}
		},
		methods: {

			subscribeMessage() {
				let that = this;
				uni.request({
					url: that.domain + '/addons/notice/api/miniappTemplate',
					method: 'GET',
					data: {
						// 此处可以填写多个一次性订阅多个模板
						event: 'test,reduce_score'
					},
					success: res => {
						uni.requestSubscribeMessage({
							tmplIds: res.data.data.template,
							success(res) {
								console.log('success', res);
							},
							fail(err) {
								console.log('error', err);
							}
						});
					}
				});
			},

			getOpenid() {
				let that = this;
				uni.login({
					provider: 'weixin',
					success: function(loginRes) {
						console.log(loginRes);

						uni.request({
							url: that.domain + '/addons/notice/api/getMiniappOpenid',
							method: 'GET',
							data: loginRes,
							success: res => {
								if (res.data.code != 1) {
									uni.showToast({
										title: res.data.msg,
										icon: 'none'
									});
									return false;
								}
								
								let openid = res.data.data.openid;
								if (!openid) {
									uni.showToast({
										title: '获取失败',
										icon: 'none'
									});
									return false;
								}

								that.openid = openid;
							},
						});

					}
				});
			},

		}
	}
</script>

<style>

</style>
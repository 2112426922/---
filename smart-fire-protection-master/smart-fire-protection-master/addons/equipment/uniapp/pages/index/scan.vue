<template>
	<view class="container">
		<view class="scan-content">
			<image class="logo" src="/static/scan.png" mode="widthFix"></image>
			<button class="cu-btn block bg-black margin-tb-sm lg" @click="scancode()">
				<text class="cuIcon-scan margin-right-xs"></text> 扫描设备二维码</button>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {

			}
		},
		onLoad(query) {
			if (query && ('q' in query)) {
				// 获取到二维码原始链接内容
				let qrcode = decodeURIComponent(query.q)
				this.parsingCode(qrcode)
			}
		},
		methods: {
			scancode() {
				let _self = this
				uni.scanCode({
					scanType: ['qrCode'],
					success: function(res) {
						let qrcode = res.result
						if (!qrcode) {
							_self.tui.toast('无法识别二维码')
							return
						}

						_self.parsingCode(qrcode)
					}
				})
			},
			parsingCode(qrcode) {
				let qrArr = qrcode.split("/")
				let coding = qrArr[qrArr.length - 1]
				if (!coding || coding.length != 8) {
					this.tui.toast('无法识别二维码')
					return
				}

				uni.navigateTo({
					url: '/pages/equipment/info?type=repair&coding=' + coding,
				})
			}
		}
	}
</script>

<style lang="scss">
	.scan-content {
		height: 100vh;
		background-color: #FFFFFF;
		display: flex;
		flex-direction: column;
		align-items: center;
		justify-content: center;

		.logo {
			width: 50%;
			margin-bottom: 100rpx;
		}

		.text-area {
			display: flex;
			justify-content: center;
		}

		.title {
			font-size: 36rpx;
			color: #8f8f94;
		}
	}
</style>

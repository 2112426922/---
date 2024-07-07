<template>
	<view class="container workbench-content">
		<view class="cu-bar search bg-white">
			<view class="search-form round" @click="search()">
				<text class="cuIcon-search"></text>
				<input :adjust-position="false" type="text" placeholder="搜索 设备名称、型号、编号" disabled="true"></input>
			</view>
			<view class="action" @click="scancode()">
				<text class="cuIcon-scan text-black text-bold" style="font-size: 56rpx;"></text>
			</view>
		</view>
		<view class="cu-bar bg-white solid-bottom margin-top-sm text-black">
			<view class="action text-bold">
				工作台
			</view>
		</view>
		<view class="cu-list grid col-3">
			<view class="cu-item" @click="goPage('/pages/equipment/archives')">
				<view class="cuIcon">
					<image src="/static/workbench/archive.png" class="png" mode="widthFix"></image>
				</view>
				<text>设备档案</text>
			</view>
			<view class="cu-item" @click="goPage('/pages/equipment/list?plan_type=inspection')">
				<view class="cuIcon">
					<image src="/static/workbench/inspection.png" class="png" mode="widthFix"></image>
					<view class="cu-tag badge" v-if="inspection > 0">
						<block>{{inspection}}</block>
					</view>
				</view>
				<text>设备巡检</text>
			</view>
			<view class="cu-item" @click="goPage('/pages/equipment/list?plan_type=maintenance')">
				<view class="cuIcon">
					<image src="/static/workbench/maintenance.png" class="png" mode="widthFix"></image>
					<view class="cu-tag badge" v-if="maintenance > 0">
						<block>{{maintenance}}</block>
					</view>
				</view>
				<text>设备保养</text>
			</view>
			<view class="cu-item" @click="goPage('/pages/repair/list?type=pending')">
				<view class="cuIcon">
					<image src="/static/workbench/repair_pool.png" class="png" mode="widthFix"></image>
					<view class="cu-tag badge" v-if="repairPool > 0">
						<block>{{repairPool}}</block>
					</view>
				</view>
				<text>待接工单</text>
			</view>
			<view class="cu-item" @click="goPage('/pages/repair/list?type=registered')">
				<view class="cuIcon">
					<image src="/static/workbench/repair.png" class="png" mode="widthFix"></image>
					<view class="cu-tag badge" v-if="repair > 0">
						<block>{{repair}}</block>
					</view>
				</view>
				<text>我的工单</text>
			</view>
			<view class="cu-item" @click="goPage('/pages/equipment/list?status=scrapped')">
				<view class="cuIcon">
					<image src="/static/workbench/scrapped.png" class="png" mode="widthFix"></image>
				</view>
				<text>设备报废</text>
			</view>
		</view>
		<view class="cu-bar bg-white solid-bottom margin-top-sm text-black">
			<view class="action text-bold">
				设备运行情况
			</view>
		</view>
		<view class="cu-list bg-white padding">
			<qiun-data-charts type="pie"
				:opts="{dataLabel: false, legend: {position: 'right', fontColor: '#333333', lineHeight: 30}}"
				:chartData="equipmentChartsData" />
		</view>
		<view class="cu-bar bg-white solid-bottom margin-top-sm text-black">
			<view class="action text-bold">
				巡检/保养计划
			</view>
			<view class="action text-gray text-sm">
				近7天巡检/保养次数
			</view>
		</view>
		<view class="cu-list bg-white padding">
			<qiun-data-charts type="line"
				:opts="{legend: {fontColor: '#333333'}, yAxis: {gridType: 'dash', dashLength: 4, gridColor: '#F1F1F1'}}"
				:chartData="planChartsData" />
		</view>
		<view class="cu-bar bg-white solid-bottom margin-top-sm text-black">
			<view class="action text-bold">
				设备维修工单
			</view>
			<view class="action text-gray text-sm">
				近7天维修工单数
			</view>
		</view>
		<view class="cu-list bg-white padding">
			<qiun-data-charts type="column"
				:opts="{legend: {show: false}, yAxis: {gridType: 'dash', dashLength: 4, gridColor: '#F1F1F1'}}"
				:chartData="repairChartsData" />
		</view>
		<view class="cu-bar bg-white solid-bottom margin-top-sm text-black">
			<view class="action text-bold">
				设备故障原因
			</view>
			<view class="action text-gray text-sm">
				本月设备故障原因占比
			</view>
		</view>
		<view class="cu-list bg-white padding">
			<qiun-data-charts type="ring"
				:opts="{dataLabel: false, legend: {position: 'right', fontColor: '#333333', lineHeight: 30}, title: {name: ''}, subtitle: {name: ''}}"
				:chartData="failureChartsData" />
		</view>
	</view>
</template>

<script>
	var _self
	export default {
		data() {
			return {
				repair: 0,
				repairPool: 0,
				inspection: 0,
				maintenance: 0,
				equipmentChartsData: {},
				planChartsData: {},
				repairChartsData: {},
				failureChartsData: {}
			};
		},
		mounted() {
			_self = this
			_self.getWorkbenchInfo()
		},
		methods: {
			getWorkbenchInfo() {
				_self.$api.workbench().then((res) => {
					_self.repair = res.data.repair
					_self.repairPool = res.data.repairPool
					_self.inspection = res.data.inspection
					_self.maintenance = res.data.maintenance
					_self.packageChartData(res.data.statisticChart)
				}).catch((err) => {

				})
			},
			packageChartData(statisticChart) {
				let equipmentPie = {
					"series": [{
						"data": statisticChart.equipmentStatus
					}]
				};
				let planLine = {
					"categories": statisticChart.weekDate,
					"series": [{
						"name": "巡检计划",
						"data": statisticChart.inspectionWeekData
					}, {
						"name": "保养计划",
						"data": statisticChart.maintenanceWeekData
					}]
				};
				let repairColumn = {
					"categories": statisticChart.weekDate,
					"series": [{
						"name": "工单数",
						"data": statisticChart.repairWeekData
					}]
				};
				let failureRing = {
					"series": [{
						"data": statisticChart.failureCause
					}]
				};
				_self.equipmentChartsData = JSON.parse(JSON.stringify(equipmentPie))
				_self.planChartsData = JSON.parse(JSON.stringify(planLine))
				_self.repairChartsData = JSON.parse(JSON.stringify(repairColumn))
				_self.failureChartsData = JSON.parse(JSON.stringify(failureRing))
			},
			scancode() {
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
					_self.tui.toast('无法识别二维码')
					return
				}

				uni.navigateTo({
					url: '/pages/equipment/info?coding=' + coding,
				})
			},
			goPage(url) {
				uni.navigateTo({
					url
				})
			},
			search() {
				uni.navigateTo({
					url: '/pages/index/search'
				})
			}
		}
	}
</script>

<style lang="scss">
	.workbench-content {
		.grid.col-3 {
			image {
				width: 40%;
			}

			text {
				color: #333333 !important;
			}
		}
	}
</style>

<template>
	<view>
		<view class="cu-bar search bg-white">
			<view class="action text-black">
				<picker class="margin-right-xs" @change="pickerChange" :value="index" :range="picker">
					<view class="picker">
						{{picker[index]}}
					</view>
				</picker>
				<text class="cuIcon-triangledownfill"></text>
			</view>
			<view class="search-form radius">
				<text class="cuIcon-search"></text>
				<input type="text" placeholder="搜索 设备名称、型号、编号" confirm-type="search" v-model="keyword" :focus="true"
					:adjust-position="false" @confirm="search()"></input>
			</view>
		</view>
	</view>
</template>

<script>
	var _self
	export default {
		data() {
			return {
				keyword: '',
				index: 0,
				picker: ['设备档案', '设备编号'],
				pickerType: ['archive', 'equipment']
			};
		},
		onLoad() {
			_self = this
		},
		methods: {
			pickerChange(e) {
				_self.index = e.detail.value
			},
			search(e) {
				uni.navigateTo({
					url: '/pages/equipment/list?search_type=' + _self.pickerType[_self.index] + '&keyword=' + _self
						.keyword,
				})
			}
		}
	}
</script>

<style lang="scss">

</style>

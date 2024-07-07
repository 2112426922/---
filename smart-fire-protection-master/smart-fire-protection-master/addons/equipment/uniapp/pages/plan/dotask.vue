<template>
	<view class="container">
		<view class="form">
			<form>
				<block v-for="(item, index) in fields" :key="index">
					<view class="cu-form-group margin-top-sm" v-if="item.type == 'text'">
						<view class="title">{{item.label}}</view>
						<input type="text" :name="item.name" v-model="values[item.name].value"></input>
					</view>
					<view class="cu-form-group margin-top-sm border-custom" v-if="item.type == 'number'">
						<view class="title">{{item.label}}</view>
						<input type="number" :name="item.name" v-model="values[item.name].value"></input>
					</view>
					<view class="cu-form-group margin-top-sm align-start" v-if="item.type == 'textarea'">
						<view class="title">{{item.label}}</view>
						<textarea maxlength="-1" @input="textareaInput($event, item.name)"></textarea>
					</view>
					<view class="cu-form-group margin-top-sm" v-if="item.type == 'radio'">
						<view class="title">{{item.label}}</view>
						<picker @change="pickerChange($event, item.name)" :value="index"
							:range="values[item.name].options">
							<view class="picker text-grey">
								{{values[item.name].index > -1 ? values[item.name]['options'][values[item.name].index] : '请选择'}}
							</view>
						</picker>
					</view>
					<checkbox-group class="block margin-top-sm" @change="checkboxChange($event, item.name)"
						v-if="item.type == 'multiple'">
						<view class="cu-form-group">
							<view class="title">{{item.label}}</view>
						</view>
						<view class="cu-form-group" v-for="(option, key) in values[item.name].options" :key="key">
							<view class="title text-grey">{{option.value}}</view>
							<checkbox class="blue" :class="option.checked?'checked':''"
								:checked="option.checked?true:false" :value="option.value"></checkbox>
						</view>
					</checkbox-group>
				</block>

				<view class="cu-form-group margin-top-sm" v-if="type == 'inspection'">
					<view class="title text-bold" style="color: red;">巡检结果</view>
					<picker @change="pickerStatusChange" :value="workIndex" :range="workStatus">
						<view class="picker text-grey">
							{{workIndex > -1 ? workStatus[workIndex] : '请选择'}}
						</view>
					</picker>
				</view>

				<view class="cu-bar bg-white margin-top-sm">
					<view class="action text-df text-black">
						现场照片
					</view>
					<view class="action">
						{{imgList.length}}/4
					</view>
				</view>
				<view class="cu-form-group">
					<view class="grid col-4 grid-square flex-sub">
						<view class="bg-img" v-for="(item,index) in imgList" :key="index" @tap="viewImage"
							:data-url="imgList[index]">
							<image :src="imgList[index]" mode="aspectFill"></image>
							<view class="cu-tag bg-red" @tap.stop="delImg" :data-index="index">
								<text class='cuIcon-close'></text>
							</view>
						</view>
						<view class="solids" @tap="chooseImage" v-if="imgList.length<4">
							<text class='cuIcon-cameraadd'></text>
						</view>
					</view>
				</view>

				<view class="cu-form-group margin-top-sm">
					<textarea maxlength="-1" @input="textareaRemark" placeholder="备注"></textarea>
				</view>
			</form>
		</view>

		<view style="height: 60px;"></view>
		<view class="cu-bar bg-white tabbar flex flex-wrap" @click="submit()">
			<button class="bg-black submit basis-sm" style="border-radius: 0;">提交</button>
		</view>
	</view>
</template>

<script>
	import Config from '@/utils/config.js'

	var taskId
	var taskType
	var _self
	var flag = false
	export default {
		data() {
			return {
				fields: [],
				values: {},
				workIndex: 0,
				workStatus: ['正常', '带病运行', '停机待修', '停机报废'],
				imgList: [],
				remark: '',
				type: ''
			}
		},
		onLoad(e) {
			_self = this;
			if (!e.id) {
				_self.tui.toast("未知计划任务");
				setTimeout(function() {
					uni.navigateBack();
				}, 1500)

				return
			}

			taskId = e.id;
			taskType = e.type;
			_self.getInfo(taskId);
			_self.changeTitle(taskType);
		},
		methods: {
			changeTitle(type) {
				_self.type = type;
				if (type == 'inspection') {
					uni.setNavigationBarTitle({
						title: '巡检计划'
					});
				}
				if (type == 'maintenance') {
					uni.setNavigationBarTitle({
						title: '保养计划'
					});
				}
			},
			getInfo(id) {
				uni.showLoading({
					title: '查询中...'
				});

				_self.$api.planTaskField({
					id
				}).then((res) => {
					uni.hideLoading();
					var values = {};
					var fields = res.data
					fields.forEach((field, index) => {
						var value = {
							'value': field.default,
							'type': field.type,
							'label': field.label,
							'require': field.require,
						};
						if (field.type == 'radio') {
							value.index = -1;
							value.options = JSON.parse(field.options);
						}
						if (field.type == 'multiple') {
							var optionArr = JSON.parse(field.options);
							var options = []
							optionArr.forEach((item, key) => {
								options.push({
									'value': item,
									'checked': false
								});
							})
							value.options = options;
						}
						values[field.name] = value;
					})
					_self.values = values;
					_self.fields = fields;
				}).catch((err) => {
					uni.hideLoading();
					_self.tui.toast(err.msg);
				})
			},
			pickerChange(e, name) {
				var index = e.detail.value;
				_self.values[name].index = index;
				_self.values[name].value = _self.values[name].options[index];
			},
			checkboxChange(e, name) {
				var items = _self.values[name].options,
					values = e.detail.value;
				for (var i = 0, lenI = items.length; i < lenI; ++i) {
					items[i].checked = false;
					for (var j = 0, lenJ = values.length; j < lenJ; ++j) {
						if (items[i].value == values[j]) {
							items[i].checked = true;
							break
						}
					}
				}
			},
			textareaInput(e, name) {
				_self.values[name].value = e.detail.value;
			},
			pickerStatusChange(e) {
				_self.workIndex = e.detail.value;
			},
			textareaRemark(e) {
				_self.remark = e.detail.value;
			},
			chooseImage() {
				uni.chooseImage({
					count: 1,
					sizeType: ['original', 'compressed'], //可以指定是原图还是压缩图，默认二者都有
					success: (chooseImageRes) => {
						const tempFilePaths = chooseImageRes.tempFilePaths
						uni.uploadFile({
							url: Config.host + 'api/common/upload',
							filePath: tempFilePaths[0],
							name: 'file',
							header: {
								'token': uni.getStorageSync('equipment_token')
							},
							success: (uploadFileRes) => {
								let result = JSON.parse(uploadFileRes.data)
								_self.imgList.push(result.data.fullurl)
							},
							fail: () => {
								_self.tui.toast('图片上传失败')
							}
						})
					}
				});
			},
			viewImage(e) {
				uni.previewImage({
					urls: _self.imgList,
					current: e.currentTarget.dataset.url
				});
			},
			delImg(e) {
				uni.showModal({
					title: '提示',
					content: '确定要删除这张照片吗？',
					cancelText: '再看看',
					confirmText: '确定',
					cancelColor: '#8799a3',
					confirmColor: '#333333',
					success: res => {
						if (res.confirm) {
							_self.imgList.splice(e.currentTarget.dataset.index, 1)
						}
					}
				})
			},
			submit() {
				if (flag == true) return;

				flag = true;
				var data = [];
				var content = {
					images: _self.imgList,
					remark: _self.remark,
					form_data: []
				};
				if (taskType == 'inspection') {
					content.work_status = _self.workStatus[_self.workIndex];
				}
				var values = Object.values(_self.values);
				try {
					values.forEach((item, key) => {
						var string = '';
						switch (item.type) {
							case 'radio':
								string = item.options[item.index];
								if (string == undefined) string = '';
								break;
							case 'multiple':
								var value = [];
								var options = item.options;
								for (var i = 0, lenI = options.length; i < lenI; ++i) {
									if (options[i].checked == true) {
										value.push(options[i].value);
									}
								}
								string = value.join('、');
								break;
							case 'text':
							case 'textarea':
							case 'number':
							default:
								string = item.value;
								break;
						}
						if (item.require == 1 && (string == '' || string == null || string == undefined)) {
							throw new Error(item.label + ' 不能为空');

						}
						data.push({
							'label': item.label,
							'value': string
						});
					});
				} catch (e) {
					flag = false;
					_self.tui.toast(e.message);
					return false;
				}
				content.form_data = data;

				uni.showLoading({
					title: '提交中...'
				});
				_self.$api.submitPlanTask({
					id: taskId,
					type: taskType,
					content
				}).then((res) => {
					flag = false;
					uni.hideLoading();
					_self.tui.toast('提交成功', '', 'success')
					setTimeout(function() {
						const eventChannel = _self.getOpenerEventChannel();
						eventChannel.emit('doRefresh', {
							ischange: true,
							type: 'inspection'
						});

						uni.navigateBack();
					}, 1500)
				}).catch((err) => {
					flag = false;
					uni.hideLoading();
					_self.tui.toast(err.msg);
				})
			}
		}
	}
</script>

<style lang="scss">
	page {
		background-color: #F1F4F6;
	}

	.cu-form-group .title {
		color: #333333;
		font-size: 28rpx;
		min-width: calc(4em + 15px);
	}

	.cu-form-group+.cu-form-group.margin-top-sm {
		border: 0;
	}

	.text-df {
		font-size: 28rpx !important;
	}

	.text-grey {
		color: #8799a3 !important;
		font-size: 26rpx !important;
	}

	.cu-bar.tabbar {
		position: fixed;
		width: 100%;
		left: 0;
		bottom: 0;
	}
</style>

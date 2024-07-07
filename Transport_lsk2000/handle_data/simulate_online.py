# -*- coding: utf-8 -*-
# 该文件模拟设备上线和下线情况: 0 表示下线，1 表示上线。

# 必要的头文件
import time
import json
import client_init

# 用于存储设备状态的字典，以设备ID为键，最近一次更新的时间为值
devices = {}


# 异步模拟设备上线和下线情况，type: 2
async def simulation_online(json_data, timeout):
    global devices

    try:
        # 解析 JSON 数据，获取设备 ID
        device_id = data_deal(json_data)

        if device_id is not None:
            # 获取当前时间
            current_time = get_current_time()

            if device_id not in devices:  # 如果之前没有见过这个ID，则为新上线的ID
                # 记录设备上线时间
                devices[device_id] = current_time
                print(f"{current_time}    :设备{device_id}上线了")

                # 准备上线数据
                data = {
                    "type": 2,
                    "code": device_id,
                    "state": 1
                }
                # 将数据转换为 JSON 字符串
                send_data = json.dumps(data)

                # 使用 await 调用异步函数，将上线信息发送到客户端
                await client_init.clientRun(data=send_data)
            else:
                # 计算当前时间与设备最后活动时间的差值，单位为秒
                if (convert_time(current_time) - convert_time(devices[device_id])) > timeout:
                    print(f"{current_time}    :设备{device_id}下线了")

                    # 准备下线数据
                    data = {
                        "type": 2,
                        "code": device_id,
                        "state": 0
                    }
                    # 将数据转换为 JSON 字符串
                    send_data = json.dumps(data)

                    # 使用 await 调用异步函数，将下线信息发送到客户端
                    await client_init.clientRun(data=send_data)
                else:
                    # 设备仍在线，更新最后活动时间
                    print(f"{current_time}    :设备{device_id}更新了一次数据")
                    devices[device_id] = current_time  # 更新设备的最后活动时间
    except json.JSONDecodeError as e:
        # 捕获 JSON 解析错误并打印错误信息
        print(f"JSON解析错误: {e}")
    except Exception as e:
        # 捕获其他一般性异常并打印错误信息
        print(f"处理设备数据时出错: {e}")


# 获取当前时间的函数，返回格式化的时间字符串
def get_current_time():
    return time.strftime('%Y-%m-%d %H:%M:%S')  # 格式化当前时间为字符串


# 将时间字符串转换为时间戳的函数
def convert_time(timer):
    # 将时间字符串转换为时间元组
    time_dt = time.strptime(timer, '%Y-%m-%d %H:%M:%S')
    # 将时间元组转换为时间戳
    return time.mktime(time_dt)


# 处理 JSON 数据，提取设备 ID
def data_deal(json_data):
    # 解析 JSON 字符串
    data = json.loads(json_data)
    # 获取第一个键值对的键
    first_key = list(data.keys())[0]
    # 返回 "code" 键的值，如果第一个键不是 "code"，返回 None
    return data[first_key] if first_key == "code" else None

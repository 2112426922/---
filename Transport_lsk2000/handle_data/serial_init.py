#serial_init.py
# -*- coding: utf-8 -*-
import json
import serial

# 串口初始化
def serial_init(my_serial, baud):
    ser = serial.Serial(my_serial,
                        baudrate=baud,  # 波特率（通信速率）以每秒比特数表示
                        timeout=0.5,    # 读操作的最大等待时间，单位为秒
                        bytesize=8,     # 波特率（通信速率）以每秒比特数表示
                        parity='N',     # 奇偶校验类型，'N'表示无奇偶校验
                        stopbits=1,     # 停止位的数量，设置为1是常见的情况
                        xonxoff=0,      # 软件流控制，0表示无流控制
                        rtscts=0)       # 硬件流控制，0表示无流控制
    if ser.isOpen():  # 判断串口是否成功打开
        print(f"打开串口成功。串口号为{ser.name}\n")
        return ser
    else:
        print("打开串口失败。")

#去掉字符串的第一个字符
def remove_first_character(str):
    if len(str) > 0:
        return str[1:]
    else:
        return str


#将JSON串的格式规范
def string_to_json(s):
    """
    将字符串转换为JSON串
    :param s: 输入的字符串，形如 "key1:value1,key2:value2,key3:value3"
    :return: JSON格式的字符串
    """
    try:
        my_dict = {}            # 储存临时字典
        pairs = s.split(',')    # 使用逗号分隔键值对
        for pair in pairs:
            key, value = pair.split(':', 1)  # 使用冒号分隔键和值，最多分隔一次
            my_dict[key.strip()] = value.strip()

        json_string = json.dumps(my_dict)
        return json_string
    except ValueError as e:
        print("ValueError:", e)
        return None
    except Exception as e:
        print("An error occurred:", e)
        return None


#串口数据处理
def data_handle(data):
    """
    将串口接收到的数据都转换为JSON串
    :param data:串口接收到的一串数据
    :return:经处理后的JSON串
    """

    #去掉字符串的第一个字符
    if len(data) > 0 and data[1] == "{":
        data = remove_first_character(data)

    data = string_to_json(data)
    if data:
        print(data)
        return data
    else:
        print("Failed to convert string to JSON.")
        return False

#获取JSON串的第二个键
def get_second_key(json_string):
    """
    获取 JSON 字符串中的第二个键
    :param json_string: 输入的 JSON 字符串
    :return: 第二个键的名称，如果不存在则返回 None
    """
    data = json.loads(json_string)
    keys = list(data.keys())
    if len(keys) >= 2:
        return keys[1]
    else:
        return None

#向JSON串开头加入一对新的键值对
def add_prefix_to_json(json_string, new_key, new_value):
    """
    将新的键值对放在 JSON 字符串开头
    :param json_string: 输入的 JSON 字符串
    :param new_key: 新键的名称
    :param new_value: 新值
    :return: 添加新键值对后的 JSON 字符串
    """
    try:
        # 将原始 JSON 字符串解析为字典
        data = json.loads(json_string)
        # 将新的键值对插入到字典的开头
        data = {new_key: new_value, **data}
        # 将更新后的字典转换为 JSON 字符串
        updated_json_string = json.dumps(data)
        return updated_json_string
    except Exception as e:
        print("An error occurred:", e)
        return None

#整合数据
def add_to_accumulated_data(json_str, accumulated_data):
    # 解析 JSON 字符串为 Python 字典
    json_data = json.loads(json_str)

    # 遍历 accumulated_data 中的键
    for key in accumulated_data.keys():
        # 如果 JSON 数据中存在该键，则将值相加
        if key in json_data:
            accumulated_data[key] += json_data[key]/12
            json_data[key] = accumulated_data[key]

    # 将累积数据字典转换回 JSON 字符串并返回
    return json.dumps(json_data)





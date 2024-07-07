#test.py
# -*- coding: utf-8 -*-
import json
import time

def add_to_accumulated_data(json_str, accumulated_data):
    # 解析 JSON 字符串为 Python 字典
    json_data = json.loads(json_str)

    # 遍历 accumulated_data 中的键
    for key in accumulated_data.keys():
        # 如果 JSON 数据中存在该键，则将值相加
        if key in json_data:
            accumulated_data[key] += json_data[key]
            accumulated_data[key] = accumulated_data[key]/12
        json_data[key] = accumulated_data[key]

    # 将累积数据字典转换回 JSON 字符串并返回
    return json.dumps(accumulated_data),json.dumps(json_data)


# 测试
json_str = '{"temp": 25, "hum": 60, "press": 1005, "light": 500, "other_key": 10}'
accumulated_data = {'temp': 0, 'hum': 0, 'press': 0, 'light': 0}
while True:
    # 将 JSON 中的值添加到累积数据中，并转换回 JSON 字符串
    result_json_str = add_to_accumulated_data(json_str, accumulated_data)

    print(result_json_str)
    time.sleep(2)

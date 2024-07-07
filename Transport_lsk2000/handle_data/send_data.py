# -*- coding: utf-8 -*-

import serial_init
import client_init
import smtp_init

#------------------------------------------------------------
#全局变量
#------------------------------------------------------------
num_counter = 0         #传感器数据处理计数器
accumulated_data = {'temp': 0, 'hum': 0, 'press': 0, 'light': 0}      # 初始化全局字典，用于存储累加结果


#将处理后的数据发向终端
async def send_data(json_data):
    global num_counter, accumulated_data

    # 获JSON串的第二个键
    second_key = serial_init.get_second_key(json_data)

    # 如果为传感器定时数据，则"type”为 0
    if second_key == "temp":
        send_str = serial_init.add_prefix_to_json(json_data, "type", 0)

        if num_counter < 12:
            send_str = serial_init.add_to_accumulated_data(send_str, accumulated_data)
            num_counter += 1
        elif num_counter == 12:
            num_counter = 0

            # 使用await调用异步函数
            await client_init.clientRun(data = send_str)

    # 如果为报警数据，则“type”为 1
    elif second_key == "warning":
        send_str = serial_init.add_prefix_to_json(json_data, "type", 1)

        # 使用await调用异步函数
        await client_init.clientRun(data = send_str)
        await smtp_init.mailbox_alarm(text_str = send_str)

    else:
        print("收到的字符串有误")
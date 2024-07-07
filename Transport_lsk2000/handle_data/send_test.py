#send_test.py
# -*- coding: utf-8 -*-
#在接受到数据后，向底层发送信息，控制LORA模式或获取当前数据。
import serial
import serial_init
import time

ser_object = serial_init.serial_init('COM7',115200)

#检测串口有没有数据
def check_serial_buffer(serial_port):
    try:
        # 获取串口接收缓冲区中的字节数
        bytes_waiting = serial_port.in_waiting
        if bytes_waiting > 0:
            return True
        else:
            print("串口缓冲区中没有数据.")
            return False
    except serial.SerialException as e:
        print(f"发生串口错误: {e}")
        return False

#   接收串口缓冲区的数据
def send_data_serial(str):
    # 检查串口缓冲区是否有数据
    if check_serial_buffer(serial_port=ser_object):
        print(f"串口缓冲区中有数据，为： {ser_object.read_all()}.")
        # 清空输入缓冲区（丢弃任何未读取的数据）
        ser_object.flushInput()

    # 串口发送并输出发送的字节数。
    write_len = ser_object.write(str.encode('utf-8'))
    print("串口发出{}个字节。".format(write_len))

    time.sleep(2)

    # 读取底层的响应数据
    com_input = ser_object.readline().lstrip()
    if com_input:
        return com_input
    else:
        return None

# 发送数据并接收底层的响应
# com_input = send_data_serial("current")
# if com_input is not None:
#     print("底层响应数据：", com_input)
# else:
#     print("发送数据失败或底层无响应。")


# 发送数据并接收底层的响应
while True:
    com_input = send_data_serial("mode0")
    if com_input is not None:
        print("底层响应数据：", com_input)
    else:
        print("发送数据失败或底层无响应。")
# while True:
#     if check_serial_buffer(serial_port=ser_object):
#         com_input = ser_object.readline().lstrip()
#         print(com_input)
#     time.sleep(2)









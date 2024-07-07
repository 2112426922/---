#main.py
# -*- coding: utf-8 -*-
import serial_init
import asyncio
import simulate_online
import send_data
import location.position_analysis

# 串口初始化
ser_object = serial_init.serial_init('/dev/ttyUSB0', 115200)
# '/dev/ttyS3'对应龙芯派 55pin为UART5_TX，56pin为UART5_RX，57和58pin为GND
# ser_object = serial_init.serial_init('COM1', 115200)  # “COM1”是Windows的串口

#------------------------------------------------------------
#主进程
#------------------------------------------------------------
async def main():
    await receive_data()
    await location.position_analysis.location_timer()

#串口数据处理定时器
async def receive_data():
  global ser_object

  while True:
    if ser_object.in_waiting>0:
        # 接收一行串口数据
        str_data = ser_object.readline().decode('utf-8').lstrip()
        print(f"{simulate_online.get_current_time()}  ：接收到的初始数据为:{str_data}")

        # 将接收到的数据规范为JSON串
        json_data = serial_init.data_handle(str_data)
        print(f"{simulate_online.get_current_time()}  ：转换为JSON串数据为:{json_data}")

        task1 = asyncio.create_task(process_data1(json_data))
        task2 = asyncio.create_task(process_data2(json_data))

        # 等待两个任务完成
        await asyncio.gather(task1, task2)

    # 设置定时器，5秒后再次执行
    await asyncio.sleep(5)

#task1
async def process_data1(json_data):
    print("Processing data 1")
    await send_data.send_data(json_data)
    # 模拟处理数据的耗时操作
    await asyncio.sleep(0.1)
    print("Task 1 processed")

#task2
async def process_data2(json_data):
    print("Processing data 2")
    await simulate_online.simulation_online(json_data,20)
    # 模拟处理数据的耗时操作
    await asyncio.sleep(0.1)
    print("Task 2 processed")

if __name__ == '__main__':
    #启动串口数据处理
    asyncio.run(main())

#查看当前进程
# def task():
#     time.sleep(0.5)
#     # current_thread:获取当前线程的线程对象
#     thread = threading.current_thread()
#     print(thread)




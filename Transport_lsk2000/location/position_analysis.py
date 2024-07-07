import location.tcp_server
import math
import asyncio
import json
import handle_data.client_init

coordinates_dict = {
    'label_id': 0,
    'point_z': 0,
}

#计算斜边长度
def calculate_hypotenuse(a, b):
    return math.sqrt(a**2 + b**2)

#提取特定字典中的字节范围，并转为十进制的整数ID
def extract_label_id(data_dict, start_byte, end_byte):
    # 获取'data'键的值
    data_value = data_dict.get('data', '')

    if data_value:
        # 将值字符串解析为字节
        byte_data = bytes.fromhex(data_value.replace(' ', ''))

        # 获取指定范围内的字节作为label_id
        label_id = byte_data[start_byte:end_byte]

        # 将字节转换为十进制
        id = int.from_bytes(label_id, byteorder='big')

        # 返回 label_id 的十六进制表示形式
        return id
    else:
        return None

#取键为data中的 00 01 = 标签ID 即3-5    00 E4 = x坐标  即7-9   00 06 = Y坐标 即9-11
async def location_timer():
 while True:
    global coordinates_dict
    data = await location.tcp_server.receive_ascii_string("192.168.43.105", 60000)
    # data = await tcp_server.receive_ascii_string("127.0.0.1", 8888)

    if data:
        x = extract_label_id(data, 7, 9)
        y = extract_label_id(data, 9, 11)
        z = calculate_hypotenuse(x, y)
        if z - coordinates_dict['point_z',0] >= 1:
            data_to_send = json.dumps({
                "type": 1,
                "code": "E240310-024",
                "warning": "The device has been moved."
            }, indent=4)

            # await handle_data.client_init.clientRun(data_to_send)
            print(f"告警信息:{data_to_send}发送成功")

        coordinates_dict['label_id'] = extract_label_id(data, 3, 5)
        coordinates_dict['point_z'] = z

        data = {
            "type": 3,
            "code": coordinates_dict['label_id'],       #灭火器编码
            "floor": 4,                                 #所在楼层
            "position_x": x,                            #坐标x
            "position_y": y                             #坐标y
        }
        data_to_send = json.dumps(data)
        await handle_data.client_init.clientRun(data)
        print(f"{data_to_send}已成功发送")

    # 设置定时器，5秒后再次执行
    await asyncio.sleep(5)

# async def main():
#     while True:
#         await location_timer()
#
# # 运行主函数
# asyncio.run(main())

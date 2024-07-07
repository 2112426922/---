# -*- coding: utf-8 -*-
#建立一个tcp服务器，接收主基站发的数据，并进行简单的数据处理

#---------------必要的头文件----------#
import socket
import re
import json
import asyncio
import time

#--------------创建tcp_服务器----------#
async def receive_ascii_string(SERVER_IP, SERVER_PORT):
    # 创建TCP socket
    server_socket = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
    server_socket.setsockopt(socket.SOL_SOCKET, socket.SO_REUSEADDR, 1)
    server_socket.setblocking(False)

    try:
        # 绑定地址和端口
        server_socket.bind((SERVER_IP, SERVER_PORT))
        print(f"服务器正在监听 {SERVER_IP}:{SERVER_PORT}...")

        # 开始监听连接
        server_socket.listen(5)

        # 等待客户端连接
        client_socket, client_address = await asyncio.get_event_loop().sock_accept(server_socket)
        print(f"已建立与 {client_address} 的连接。")

        # 接收客户端发送的消息
        ascii_string = ""
        first_dict = None
        while True:
            #接收数据
            data = await asyncio.get_event_loop().sock_recv(client_socket, 512)
            if not data:
                print('接收数据失败')
                break

            #将接收到的数据转换为十六进制
            hex_data = data.hex()
            #将十六进制转换为列表
            # hex_list = [x for x in hex_data.decode('utf-8').split(' ') if x]
            hex_list = [hex_data[i:i + 2] for i in range(0, len(hex_data), 2)]

            #将十六进制字符串转换为对应的ASCII码
            try:
                ascii_string = ''.join(chr(int(hex_char, 16)) for hex_char in hex_list)
                # print(f"acsii_string:{ascii_string}")
            except OverflowError as e:
                print("Overflow error:", e)

            # 使用正则表达式提取第一个大括号内的内容
            match = re.search(r'\{(.*?)\}', ascii_string)
            # 如果找到匹配项，将其转换为字典
            if match:
                first_json_string = match.group(1)
                first_dict = json.loads('{' + first_json_string + '}')
                print(f'接收到的数据为：{first_dict}')
            else:
                print("接收到的数据不完整，无法分析")

            time.sleep(1)
            # data = {
            #     "type": 0,
            #     "code": "E240310-024",
            #     "position": "228cm"
            # }
            # send_data = json.dumps(data)
            # print(f"{data}已成功发送到'47.108.119.237:8283'")

        # 关闭连接
        # client_socket.close()

        return first_dict

    finally:
        # 关闭服务器套接字
        server_socket.close()

# 测试函数
# result = receive_ascii_string(SERVER_IP, SERVER_PORT)
# print("接收到的 ASCII 字符串:", result)

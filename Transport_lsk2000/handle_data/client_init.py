#client_init.py
# -*- coding: utf-8 -*-
import websockets
import json
import asyncio
import ssl
import logging

# 为了简化操作禁用 SSL 验证（生产环境中不推荐）
ssl._create_default_https_context = ssl._create_unverified_context

# 配置日志记录
logging.basicConfig(level=logging.INFO)

# # 握手，通过发送hello，接收"123"来进行双方的握手。
# async def clientHands(websocket):
#     while True:
#         try:
#             await websocket.send("hello")
#             response_str = await websocket.recv()
#             if "123" in response_str:
#                 print("握手成功")
#             else:
#                 print(f"握手失败，服务器响应: {response_str}")
#                 break
#         except websockets.WebSocketException as e:
#             print(f"WebSocket error during handshake: {e}")
#             break

# 向服务器端发送消息
async def clientSend(websocket, data):
    if data is None or data is False:
        await websocket.close(reason="exit")
        return False


    # # 将字符串编码为UTF-8
    # encoded_data = data.encode('utf-8')
    # # 发送UTF-8编码的数据
    # await websocket.send(encoded_data)

    # 发送UTF-8编码的数据
    await websocket.send(data)

    return True

# 进行websocket连接
async def clientRun(data):
    """
    建立 WebSocket 连接并发送数据。

    参数:
    - data: 要发送的数据。

    返回值:
    - 成功返回 True，失败返回 False。
    """
    IP_ADDR = "47.108.119.237"  # IP地址
    IP_PORT = "8283"  # 端口号

    ipaddress = f"wss://{IP_ADDR}:{IP_PORT}"

    # 连接websocket服务器
    async with websockets.connect(ipaddress,ssl = None) as websocket:
        try:
            logging.info(f"成功连接到 {ipaddress}")
            # 执行握手
            # await clientHands(websocket)
            # 向服务器发送消息
            return await clientSend(websocket, data)

        except websockets.exceptions.InvalidURI as e:
            logging.error(f"无效的URI: {e}")
            return False
        except websockets.exceptions.InvalidHandshake as e:
            logging.error(f"握手失败: {e}")
            return False
        except asyncio.TimeoutError:
            logging.error("连接超时")
            return False
        except Exception as e:
            logging.error(f"WebSocket连接出错: {e}")
            return False

# #测试用例
# async def main():
#     data_to_send = json.dumps({
#         "type": 1,
#         "code": "E240310-024",
#         "warning": "Flammable gas detected."
#     }, indent=4)
#
#     # 建立WebSocket连接并发送数据
#     success = await clientRun(data_to_send)
#
#     if success:
#         print("数据发送成功。")
#     else:
#         print("未能发送数据。")
#
# # 运行主函数
# asyncio.get_event_loop().run_until_complete(main())


import re
import json

hex_string = "7B 22 6C 61 6E 64 6D 61 72 6B 22 3A 22 31 34 22 2C 22 64 61 74 61 22 3A 22 20 30 31 20 30 33 20 31 41 20 30 30 20 30 31 20 30 31 20 30 46 20 30 30 20 45 34 20 30 30 20 30 36 20 30 30 20 30 30 20 30 31 20 30 41 20 30 30 20 35 37 20 30 31 20 35 39 20 30 31 20 31 36 20 30 30 20 30 30 20 30 30 20 30 30 20 30 30 20 30 30 20 30 30 20 30 30 20 30 35 20 30 37 22 7D 00 7B 22 6C 61 6E 64 6D 61 72 6B 22 3A 22 31 34 22 2C 22 64 61 74 61 22 3A 22 20 30 31 20 30 33 20 31 41 20 30 30 20 30 30 20 30 31 20 30 46 20 30 30 20 38 44 20 30 30 20 30 38 20 30 30 20 30 30 20 30 30 20 39 39 20 30 30 20 36 41 20 30 31 20 31 37 20 30 31 20 31 30 20 30 30 20 30 30 20 30 30 20 30 30 20 30 30 20 30 30 20 30 30 20 30 30 20 43 31 20 45 31 22 7D 00 7B 22 6C 61 6E 64 6D 61 72 6B 22 3A 22 31 34 22 2C 22 64 61 74 61 22 3A 22 20 30 31 20 30 33 20 31 41 20 30 30 20 30 31 20 30 31 20 30 46 20 30 30 20 45 35 20 30 30 20 30 37 20 30 30 20 30 30 20 30 31 20 30 39 20 30 30 20 35 38 20 30 31 20 35 38 20 30 31 20 31 35 20 30 30 20 30 30 20 30 30 20 30 30 20 30 30 20 30 30 20 30 30 20 30 30 20 36 46 20 32 38 22 7D 00 7B 22 6C 61 6E 64 6D 61 72 6B 22 3A 22 31 34 22 2C 22 64 61 74 61 22 3A 22 20 30 31 20 30 33 20 31 41 20 30 30 20 30 30 20 30 31 20 30 46 20 30 30 20 38 45 "

# 用空格分割十六进制字符串，并删除空字符串
hex_list = [x for x in hex_string.split(' ') if x]

# 将每个十六进制字符转换为ASCII字符
ascii_string = ''.join(chr(int(hex_char, 16)) for hex_char in hex_list)

print(ascii_string)


# 使用正则表达式提取第一个大括号内的内容
match = re.search(r'\{(.*?)\}', ascii_string)

# 如果找到匹配项，将其转换为字典
if match:
    first_json_string = match.group(1)
    first_dict = json.loads('{' + first_json_string + '}')
    print(first_dict)
else:
    print("接收到的数据不完整，无法分析")

#取键为data中的 00 01 = 标签ID 即3-5    00 E4 = x坐标  即7-9   00 06 = Y坐标 即9-11

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

# 测试函数
data_dict = {'landmark': '14', 'data': ' 01 03 1A 00 01 01 0F 00 E4 00 06 00 00 01 0A 00 57 01 59 01 16 00 00 00 00 00 00 00 00 05 07'}
start_byte = 9
end_byte = 11
label_id = extract_label_id(data_dict, start_byte, end_byte)
print("label_id:", label_id)



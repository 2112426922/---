#!/bin/bash

# 安装git函数
install_git() {
    echo "正在安装git..."
    yum install git -y
}

# 安装rsync函数
install_rsync() {
    echo "正在安装rsync..."
    yum install rsync -y
}

# 检查git是否安装
if ! command -v git &> /dev/null; then
    install_git
fi

# 检查rsync是否安装
if ! command -v rsync &> /dev/null; then
    install_rsync
fi

echo "----------------------------------------------"
# 设置要克隆的项目的URL
git_url="https://Aoisons:ylw1988213@gitee.com/aoisons/smart-fire-protection.git"

# 删除已存在的 smart-fire-protection 目录
rm -rf smart-fire-protection

# 首先进行git clone操作
echo "开始执行git clone操作..."
git clone $git_url

# 检查克隆是否成功
if [ $? -eq 0 ]; then
    echo "git clone操作成功！"
else
    echo "git clone操作失败！"
    exit 1
fi

# 检查 smart-fire-protection 文件夹是否存在
if [ -d "smart-fire-protection" ]; then
    # 如果 smart-fire-protection 文件夹存在，则将其内容剪切到 loongson.cgsjzs.com 文件夹内
    echo "开始剪切 smart-fire-protection 内容到 loongson.cgsjzs.com 文件夹..."
    rsync -azr --remove-source-files smart-fire-protection/* ./
    echo "smart-fire-protection 内容已成功剪切到 loongson.cgsjzs.com 文件夹！"
else
    echo "smart-fire-protection 文件夹不存在！"
    exit 1
fi

# 清理下载的git仓库
rm -rf .git
# 删除smart-fire-protection 目录
rm -rf smart-fire-protection
echo "----------------------------------------------"
chmod -R 755 ./*
echo "文件权限设置755权限！"
echo "----------------------------------------------"
# 更改文件所有者为www用户
chown -R www:www ./*
echo "文件所有者已更改www用户！"
echo "----------------------------------------------"

echo "脚本执行完毕！"

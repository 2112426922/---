#smtp_init.py
# -*- coding: utf-8 -*-
import smtplib  # 导入smtplib库，用于发送邮件
from email.mime.multipart import MIMEMultipart
from email.mime.text import MIMEText
from email.header import Header

def mailbox_alarm(text_str):
    # 通过创建SMTP对象并指定QQ邮箱的SMTP服务器地址，建立与SMTP服务器的连接。
    conn = smtplib.SMTP("smtp.qq.com")
    conn.login('2112426922@qq.com', 'ztykkjxndirdehji')  # 参数二：授权码

    mail = MIMEMultipart()  # 邮件对象
    theme = Header("传感器报警", "UTF-8").encode()  # 主题对象
    mail['Subject'] = theme  # 邮件主题
    mail['From'] = '2112426922@qq.com'  # 发件人
    mail['To'] = '2581693358@qq.com'  # 收件人


    text = MIMEText(text_str, 'plain', 'UTF-8')  # 邮件内容(以纯文本形式发送)
    mail.attach(text)  # 将邮件内容加入
    # 发送邮件
    conn.sendmail('2112426922@qq.com', '2581693358@qq.com', mail.as_string())

    print(text_str)
    # 关闭连接
    conn.quit()


# mailbox_alarm("hello niuniu")
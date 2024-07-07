/*
 * relay.c
 *
 * created: 2024/3/23
 *  author: 
 */

#include "relay.h"

/*
 * 四路继电器默认设备地址为0xFF
 *
 */
unsigned char relay_device = 0xFF;


/*校验码计算*/
static unsigned short crcmodbus_cal(unsigned char*data, unsigned char len)
{
    unsigned short tmp = 0xFFFF,ret;
    unsigned char i,j;

    for(i=0;i<len;i++){
        tmp = data[i] ^ tmp;
        for(j=0;j<8;j++){
            if(tmp & 0x01){
                tmp = tmp >> 1;
                tmp = tmp ^ 0xA001;
            }else
                tmp = tmp >> 1;
        }
    }

    ret = tmp >> 8;
    ret = ret | (tmp << 8);

    return ret;
}


/*初始化UART3*/
void UART3_Config_Init(void)
{
    unsigned int baud = 9600;
    ls1x_uart_init(devUART3, (void *)baud);
    ls1x_uart_open(devUART3, NULL);
}


/*
 * 打开/关闭一个继电器
 * @num:继电器地址(1-4)
 * @mode:开关,0-关 1-开
 */
void relay_on_off(unsigned char num, unsigned char mode)
{
    unsigned char data[8] = {0,0x05,0x00,0,0,0x00};
    unsigned short buf;

    if(num<1 || num>4)
        return;

    data[0] = relay_device;
    data[3] = num - 1;
    if(mode)
        data[4] = 0xFF;
    else
        data[4] = 0x00;

    buf = crcmodbus_cal(data, 6);
    data[6] = buf >> 8;
    data[7] = buf & 0xff;

    ls1x_uart_write(devUART3, data, sizeof(data), NULL);

    int i,len;
    unsigned char str[12];
    delay_ms(50);
    len = ls1x_uart_read(devUART3, str, 12, NULL);
    if(len > 0){
        printk("The data received:\n");
        for(i=0;i<len;i++)
            printk("0x%02x ",str[i]);
        printk("\n\n");
    }
    return;
}


/*
 * 同时打开/关闭4个继电器
 */
void relay_all_op(unsigned char mode)
{
    int i,len;
    unsigned char str[12],data[10] = {0xFF,0x0F,0x00,0x00,0x00,0x08,0x01};
    unsigned short buf;

    if(mode)
        data[7] = 0xFF;
    else
        data[7] = 0x00;
    buf = crcmodbus_cal(data, 8);
    data[8] = buf >> 8;
    data[9] = buf & 0xff;

    ls1x_uart_write(devUART3, data, sizeof(data), NULL);

    delay_ms(50);
    len = ls1x_uart_read(devUART3, str, 12, NULL);
    if(len > 0){
        printk("The data received:\n");
        for(i=0;i<len;i++)
            printk("0x%02x ",str[i]);
        printk("\n\n");
    }
}


/*
 * 以闪闭模式/闪断模式操作继电器
 * @num:继电器号(1-4)
 * @time:延时时间
 * @mode:闪闭模式(RELAY_FLASH_OFF) 闪断模式(RELAY_FLASH_ON)
 * 闪闭模式：继电器打开若干秒后自动关闭
 * 闪断模式：继电器关闭若干秒后自动打开
 */
void relay_flash_off(unsigned char num,unsigned short time,unsigned char mode)
{
    unsigned char i,len,str[12],buf[13] = {0xFF,0x10,0,0,0x00,0x02,0x04,0x00};
    unsigned short data;

    switch(num){
        case 1:
            buf[2] = 0x00;
            buf[3] = 0x03;
            break;
        case 2:
            buf[2] = 0x00;
            buf[3] = 0x08;
            break;
        case 3:
            buf[2] = 0x00;
            buf[3] = 0x0D;
            break;
        case 4:
            buf[2] = 0x00;
            buf[3] = 0x12;
            break;
        default:
            return;
    }
    buf[8] = mode;
    if(time<0 || time>6553)
        return;
    buf[9] = (time*10) >> 8;
    buf[10] = (time*10) & 0xFF;

    data = crcmodbus_cal(buf, 11);
    buf[11] = data >> 8;
    buf[12] = data & 0xff;

    ls1x_uart_write(devUART3, buf, sizeof(buf), NULL);

    delay_ms(50);
    len = ls1x_uart_read(devUART3, str, 12, NULL);
    if(len > 0){
        printk("The data received:\n");
        for(i=0;i<len;i++)
            printk("0x%02x ",str[i]);
        printk("\n\n");
    }
}


/*
 * 读取继电器状态
 * @return:bit0-bit7代表继电器1-8，1为打开，0为关闭
 */
unsigned char relay_get_status(void)
{
    unsigned char i,len,str[12],buf[8] = {0xFF,0x01,0x00,0x00,0x00,0x08,0x28,0x12};

    ls1x_uart_write(devUART3, buf, sizeof(buf), NULL);

    delay_ms(50);
    len = ls1x_uart_read(devUART3, str, 12, NULL);
    if(len > 0){
        printk("The data received:\n");
        for(i=0;i<len;i++)
            printk("0x%02x ",str[i]);
        printk("\n\n");
    }

    return str[3];
}


/*
 * 读取光耦输入状态
 * @return:bit0-bit7代表光耦1-8输入信号，1为高电平，0为低电平
 */
unsigned char relay_input_status(void)
{
    unsigned char i,len,str[12],buf[8] = {0xFF,0x02,0x00,0x00,0x00,0x08,0x6C,0x12};

    ls1x_uart_write(devUART3, buf, sizeof(buf), NULL);

    delay_ms(50);
    len = ls1x_uart_read(devUART3, str, 12, NULL);
    if(len > 0){
        printk("The data received:\n");
        for(i=0;i<len;i++)
            printk("0x%02x ",str[i]);
        printk("\n\n");
    }

    return str[3];
}


/*
 * 获取设备地址
 */
void relay_get_device(void)
{
    unsigned char i,len,buf[8] = {0x00, 0x03, 0x00, 0x00, 0x00, 0x01, 0x85, 0xDB},str[12];

    len = ls1x_uart_write(devUART3, buf, sizeof(buf), NULL);
    delay_ms(50);
    len = ls1x_uart_read(devUART3, str, 12, NULL);
    if(len > 0){
        printk("The data received:\n");
        for(i=0;i<len;i++)
            printk("0x%x ",str[i]);
        printk("\n\n");
    }
    return;
}


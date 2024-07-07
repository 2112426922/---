/*
 * gpio_uart.c
 *
 * created: 2024/1/8
 *  author:
 */

#include "gpio_uart.h"
#include "ls1b.h"
#include "ls1b_gpio.h"
#include "ns16550.h"
#include "stdio.h"
#include "string.h"


//串口初始化
void UART4_Config_Init(void)
{
    ls1x_uart_init(devUART4,NULL);
    ls1x_uart_open(devUART4,NULL);
}
void UART5_Config_Init(void)
{
    ls1x_uart_init(devUART5,NULL);
    ls1x_uart_open(devUART5,NULL);
}


//gpio初始化
void gpio_Config_Init(void)
{
    //设置GPIO为输出状态
    gpio_enable(M0,DIR_OUT);
    gpio_enable(M1,DIR_OUT);
    //gpio_enable(AUX,DIR_OUT);

    gpio_write(M0,0);//初始值为0
    gpio_write(M1,0);
    //gpio_write(AUX,1);//只能在 AUX 输出 1 的时候有效，否则会延迟切换

}
